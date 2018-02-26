<?php

define('SMTP_STATUS_NOT_CONNECTED', 1, true);
define('SMTP_STATUS_CONNECTED',     2, true);

class smtp
{
    var $connection;
    var $recipients;
    var $headers;
    var $timeout;
    var $errors;
    var $status;
    var $body;
    var $from;
    var $host;
    var $ssl;
    var $port;
    var $helo;
    var $auth;
    var $user;
    var $pass;

    /**
     *  参数为一个数组
     *  host        SMTP 服务器的主机       默认：localhost
     *  port        SMTP 服务器的端口       默认：25
     *  helo        发送HELO命令的名称      默认：localhost
     *  user        SMTP 服务器的用户名     默认：空值
     *  pass        SMTP 服务器的登录密码   默认：空值
     *  timeout     连接超时的时间          默认：10
     *  @return  bool
     */
    function smtp($params = array())
    {
        if (!defined('CRLF'))
        {
            define('CRLF', "\r\n", true);
        }

        $this->timeout  = 10;
        $this->status   = SMTP_STATUS_NOT_CONNECTED;
        $this->host     = 'localhost';
        $this->ssl      = false;
        $this->port     = 25;
        $this->auth     = false;
        $this->user     = '';
        $this->pass     = '';
        $this->errors   = array();

        foreach ($params AS $key => $value)
        {
            $this->$key = $value;
        }

        $this->helo     = $this->host;

        //  如果没有设置用户名则不验证
        $this->auth = ('' == $this->user) ? false : true;
    }

    function connect($params = array())
    {
        if (!isset($this->status))
        {
            $obj = new smtp($params);

            if ($obj->connect())
            {
                $obj->status = SMTP_STATUS_CONNECTED;
            }
            return $obj;
        }
        else
        {
            if ($this->ssl)
            {
                $this->host = "ssl://" . $this->host;
            }
            $this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

            if ($this->connection === false)
            {
                $this->errors[] = 'Access is denied.';

                return false;
            }

            @socket_set_timeout($this->connection, 0, 250000);

            $greeting = $this->get_data();

            if (is_resource($this->connection))
            {
                $this->status = 2;

                return $this->auth ? $this->ehlo() : $this->helo();
            }
            else
            {
                $this->errors[] = 'Failed to connect to server: ' . $errstr;

                return false;
            }
        }
    }

    /**
     * 参数为数组
     * recipients      接收人的数组
     * from            发件人的地址，也将作为回复地址
     * headers         头部信息的数组
     * body            邮件的主体
     */

    function send($params = array())
    {
        foreach ($params AS $key => $value)
        {
            $this->$key = $value;
        }

        if ($this->is_connected())
        {
            //  服务器是否需要验证
            if ($this->auth)
            {
                if (!$this->auth())
                {
                    return false;
                }
            }

            $this->mail($this->from);

            if (is_array($this->recipients))
            {
                foreach ($this->recipients AS $value)
                {
                    $this->rcpt($value);
                }
            }
            else
            {
                $this->rcpt($this->recipients);
            }

            if (!$this->data())
            {
                return false;
            }

            $headers = str_replace(CRLF . '.', CRLF . '..', trim(implode(CRLF, $this->headers)));
            $body    = str_replace(CRLF . '.', CRLF . '..', $this->body);
            $body    = substr($body, 0, 1) == '.' ? '.' . $body : $body;

            $this->send_data($headers);
            $this->send_data('');
            $this->send_data($body);
            $this->send_data('.');

            return (substr($this->get_data(), 0, 3) === '250');
        }
        else
        {
            $this->errors[] = 'Not connected!';

            return false;
        }
    }

    function helo()
    {
        if (is_resource($this->connection)
                AND $this->send_data('HELO ' . $this->helo)
                AND substr($error = $this->get_data(), 0, 3) === '250' )
        {
            return true;
        }
        else
        {
            $this->errors[] = 'HELO command failed, output: ' . trim(substr($error, 3));

            return false;
        }
    }

    function ehlo()
    {
        if (is_resource($this->connection)
                AND $this->send_data('EHLO ' . $this->helo)
                AND substr($error = $this->get_data(), 0, 3) === '250' )
        {
            return true;
        }
        else
        {
            $this->errors[] = 'EHLO command failed, output: ' . trim(substr($error, 3));

            return false;
        }
    }

    function auth()
    {
        if (is_resource($this->connection)
                AND $this->send_data('AUTH LOGIN')
                AND substr($error = $this->get_data(), 0, 3) === '334'
                AND $this->send_data(base64_encode($this->user))            // Send username
                AND substr($error = $this->get_data(),0,3) === '334'
                AND $this->send_data(base64_encode($this->pass))            // Send password
                AND substr($error = $this->get_data(),0,3) === '235' )
        {
            return true;
        }
        else
        {
            $this->errors[] = 'AUTH command failed: ' . trim(substr($error, 3));

            return false;
        }
    }

    function mail($from)
    {
        if ($this->is_connected()
            AND $this->send_data('MAIL FROM:<' . $from . '>')
            AND substr($this->get_data(), 0, 2) === '250' )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function rcpt($to)
    {
        if ($this->is_connected()
            AND $this->send_data('RCPT TO:<' . $to . '>')
            AND substr($error = $this->get_data(), 0, 2) === '25')
        {
            return true;
        }
        else
        {
            $this->errors[] = trim(substr($error, 3));

            return false;
        }
    }

    function data()
    {
        if ($this->is_connected()
            AND $this->send_data('DATA')
            AND substr($error = $this->get_data(), 0, 3) === '354' )
        {
            return true;
        }
        else
        {
            $this->errors[] = trim(substr($error, 3));

            return false;
        }
    }

    function is_connected()
    {
        return (is_resource($this->connection) AND ($this->status === SMTP_STATUS_CONNECTED));
    }

    function send_data($data)
    {
        if (is_resource($this->connection))
        {
            return fwrite($this->connection, $data . CRLF, strlen($data) + 2);
        }
        else
        {
            return false;
        }
    }

    function get_data()
    {
        $return = '';
        $line   = '';

        if (is_resource($this->connection))
        {
            while (strpos($return, CRLF) === false OR $line{3} !== ' ')
            {
                $line    = fgets($this->connection, 512);
                $return .= $line;
            }

            return trim($return);
        }
        else
        {
            return '';
        }
    }

    /**
     * 获得最后一个错误信息
     *
     * @access  public
     * @return  string
     */
    function error_msg()
    {
        if (!empty($this->errors))
        {
            $len = count($this->errors) - 1;
            return $this->errors[$len];
        }
        else
        {
            return '';
        }
    }
}

?>
<?php

final class Mail {
   protected $to;
   protected $from;
   protected $sender;
   protected $subject;
   protected $text;
   protected $html;
   protected $attachments = array();
   public $protocol = 'mail';
   public $smtp_owner;
   public $hostname;
   public $username;
   public $password;
   public $port = 25;
   public $timeout = 5;
   public $newline = "\n";
   public $crlf = "\r\n";
   public $verp = FALSE;
   public $parameter = '';

   
   public function setTo($to) {
      $this->to = $to;
   }

   public function setBcc($bcc) {
      $this->bcc = $bcc;
   }

   public function setFrom($from) {
      $this->from = $from;
   }

   public function addheader($header, $value) {
      $this->headers[$header] = $value;
   }

   public function setSender($sender) {
      $this->sender = html_entity_decode($sender, ENT_COMPAT, 'UTF-8');
   }

   public function setSubject($subject) {
      $this->subject = html_entity_decode($subject, ENT_COMPAT, 'UTF-8');
   }

   public function setText($text) {
      $this->text = $text;
   }

   public function setHtml($html) {
      $this->html = $html;
   }

   public function addAttachment($file, $filename = '') {
      if (!$filename) {
         $filename = basename($file);
      }

      $this->attachments[] = array(
         'filename' => $filename,
         'file'     => $file
      );
   }

   public function send() {
     // wont send mail if there is no mail setting.
     if ($this->protocol == '0') {
        return 0;
     }
    
     if (!$this->to) {
         exit('Error: E-Mail to required!');
      }

      if (!$this->from) {
         exit('Error: E-Mail from required!');
      }

      if (!$this->sender) {
         exit('Error: E-Mail sender required!');
      }

      if (!$this->subject) {
         exit('Error: E-Mail subject required!');
      }

      if ((!$this->text) && (!$this->html)) {
         exit('Error: E-Mail message required!');
      }

      if (is_array($this->to)) {
          $this->to = implode(',',$this->to);
      }
      
      $params = array(
          'mail_service' => $this->protocol,
          'smtp_host' => $this->hostname,
          'smtp_user' => $this->username,
          'smtp_pass' => $this->password,
          'smtp_port' => $this->port,
          'timeout' => $this->timeout,
          'name' => $this->to,
          'email' => $this->to,
          'subject' => $this->subject,
          'sender_name' => $this->sender,
          'sender_email' => $this->from,
          'type' => ($this->html) ? 1 : 0,
          'content' => ($this->html) ? $this->html : $this->text,
      );
      $status = $this->send_mail($params);
      if ($status !== true) {
          global $log;
          $log->write("E-mail error: {$status}");
          $log->write(base64_encode(serialize($params)));
      }
    }
   
    /**
     * 邮件发送
     *
     * @param: $name[string]        接收人姓名
     * @param: $email[string]       接收人邮件地址
     * @param: $subject[string]     邮件标题
     * @param: $content[string]     邮件内容
     * @param: $type[int]           0 普通邮件， 1 HTML邮件
     * @param: $sender_name[string]  发送者名称
     * @param: $sender_email[string]  邮件回复地址 abc@mail.com
     * @param: $smtp_host[string]  SMTP 主机 smtp.qq.com
     * @param: $smtp_port[int]  SMTP 端口 25
     * @param: $smtp_user[string]  SMTP 用户 abc@mail.com
     * @param: $smtp_pass[string]  SMTP 密码 ***
     * @param: $smtp_timeout[int]  SMTP 超时时间 10
     * @param: $smtp_ssl[bool]  是否SSL
     * @param: $mail_service[string]  mail 使用mail函数发送邮件， smtp 使用smtp服务发送邮件
     * @param: $notification[bool]  true 要求回执， false 不用回执
     *
     * @return boolean 成功返回 true ，不成功返回错误信息
     */
    public function send_mail($params = array())
    {
        $name = $email = $subject = $content = $sender_name = $sender_email = $smtp_host = $smtp_port = $smtp_user = $smtp_pass = '';
        $smtp_timeout = 10;
        $type = 0;
        $mail_service = 'smtp';
        $notification = $smtp_ssl = false;
        extract($params);
        $charset   = 'utf-8';
        /**
         * 使用mail函数发送邮件
         */
        if ($mail_service == 'mail' && function_exists('mail'))
        {
            /* 邮件的头部信息 */
            $content_type = ($type == 0) ? 'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
            $headers = array();
            $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($sender_name) . '?='.'" <' . $sender_email . '>';
            $headers[] = $content_type . '; format=flowed';
            if ($notification)
            {
                $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($sender_name) . '?='.'" <' . $sender_email . '>';
            }

            $res = @mail($email, '=?' . $charset . '?B?' . base64_encode($subject) . '?=', $content, implode("\r\n", $headers));

            if (!$res)
            {
                return 'Sendemail false';
            }
            else
            {
                return true;
            }
        }
        /**
         * 使用smtp服务发送邮件
         */
        else
        {
            /* 邮件的头部信息 */
            $content_type = ($type == 0) ?
                'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
            $content   =  base64_encode($content);

            $headers = array();
            $headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0000';
            $headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
            $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($sender_name) . '?='.'" <' . $sender_email . '>';
            $headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
            $headers[] = $content_type . '; format=flowed';
            $headers[] = 'Content-Transfer-Encoding: base64';
            $headers[] = 'Content-Disposition: inline';
            if ($notification)
            {
                $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($sender_name) . '?='.'" <' . $sender_email . '>';
            }

            /* 获得邮件服务器的参数设置 */
            $params['host'] = $smtp_host;
            $params['port'] = $smtp_port;
            $params['user'] = $smtp_user;
            $params['pass'] = $smtp_pass;
            $params['ssl'] =  $smtp_ssl;
            $params['timeout'] =  $smtp_timeout;

            if (empty($params['host']) || empty($params['port']))
            {
                // 如果没有设置主机和端口直接返回 false
                return 'smtp setting error';
            }
            else
            {
                // 发送邮件
                if (!function_exists('fsockopen'))
                {
                    //如果fsockopen被禁用，直接返回
                    return 'Disabled fsockopen';
                }

                static $smtp;

                $send_params['recipients'] = $email;
                $send_params['headers']    = $headers;
                $send_params['from']       = $sender_email;
                $send_params['body']       = $content;
                if (!isset($smtp))
                {
                    $smtp = new smtp($params);
                }

                if ($smtp->connect() && $smtp->send($send_params))
                {
                    return true;
                }
                else
                {
                    $err_msg = $smtp->error_msg();
                    if (empty($err_msg))
                    {
                        return 'Unknown Error';
                    }
                    else
                    {
                        return $err_msg;
                    }
                    return false;
                }
            }
        }
    }
}
?>
