<?php


/**
 * 翻页设置
 *
 * @access  protected
 * @param   string
 * @return  array
 */
function pager($page, $value, $count)
{
    $page = intval(from($_REQUEST, $page));
    $page = ($page > 1 AND $page < 1000) ? $page : 1;
    $uri = $_GET;
    if (isset($uri['page'])) unset($uri['page']);
    $pager = array(
        'uri' => ($uri) ? '&' . http_build_query($uri) : '',
        'page' => $page,
        'value' => $value,
        'offset' => ($page-1)*$value,
        'count' => $count,
        'max' => ceil($count / $value)
    );
    return $pager;
}

/**
* 获得数组指定键的值
*
* @access global
* @param array,object $array
* @param string $key
* @param mixed $default
* @return mixed
*/
function from($array, $key, $default = FALSE)
{
    $return = $default;
    if (is_object($array)) $return = (isset($array->$key) === TRUE && empty($array->$key) === FALSE) ? $array->$key : $default;
    if (is_array($array))  $return = (isset($array[$key]) === TRUE && empty($array[$key]) === FALSE) ? $array[$key] : $default;
    
    return $return;
}

/**
 * IP 地址转换物理地址
 * 
 * @access global
 * @param string $ip
 * @return string
 */
function ip2addr($ip = '')
{
    $CI =& get_instance(); 
    $CI->load->library('ip');
    if (!$ip) $ip = from($_SERVER, 'REMOTE_ADDR');
    $addr = array_unique(ip::find($ip));
    if (isset($addr[0]) && $addr[0] == '中国') unset($addr[0]);
    $addr = implode('', $addr);
    if($addr == '本机地址') $addr = '上海';
    return $addr;
}

// 发送Email
function send_email($email, $title, $content){
    $CI =& get_instance(); 
    $CI->load->library('mail');
    
    return $CI->mail->send_mail(array(
        'email' => $email,
        'subject' => $title,
        'content' => $content,
        'type' => 1,
        'sender_name' => '网站名称',
        'sender_email' => 'username@qq.com',
        'smtp_host' => 'smtp.exmail.qq.com',
        'smtp_port' => '25',
        'smtp_user' => 'username@qq.com',
        'smtp_pass' => 'password',
        'smtp_timeout' => '10',
        'smtp_ssl' => false,
        'mail_service' => 'smtp',
    ));
}


// 生成筛选选项
// echo select_attr('date', 'now=可立即入住|1week=一周内入住|2week=两周内入住|3week=三周内入住', $uri, 'check'); 
function select_attr($attr_name, $select, $uri, $type = 'radio', $default = '全部', $class_name = 'aNow'){
    $return = '';
    $href = current_url();
    $nowuri = $uri;
    unset($nowuri[$attr_name]);
    $href = ($nowuri) ? current_url() . "?" . http_build_query($nowuri) : current_url();
    $is_now = (!from($_REQUEST, $attr_name));
    $class = ($is_now) ? $class_name : '';
    $return .= "<a href='{$href}' class='{$class}'>{$default}</a>";
    foreach(explode('|', $select) as $key => $val) {
        $val = explode("=", $val);
        $is_now = (is_array(from($_REQUEST, $attr_name))) ? in_array($val[0], from($_REQUEST, $attr_name)) : ($val[0] == from($_REQUEST, $attr_name));
        $class = ($is_now) ? $class_name : '';
        $nowuri = $uri;
        if ($type == 'radio') {
            $nowuri[$attr_name] = $val[0];
        } else {
            if ($is_now && isset($nowuri[$attr_name])  && is_array($nowuri[$attr_name])) {
                foreach($nowuri[$attr_name] as $key2 => $val2) {if ($val2 == $val[0]) unset($nowuri[$attr_name][$key2]);}
            } elseif (isset($nowuri[$attr_name]) && is_array($nowuri[$attr_name])) {
                $nowuri[$attr_name] = array_merge($nowuri[$attr_name], array($val[0]));
            } else {
                $nowuri[$attr_name] = array($val[0]);
            }
        }
        $href = ($nowuri) ? current_url() . "?" . http_build_query($nowuri) : current_url();
        $text = isset($val[1]) ? $val[1] : $val[0];
        $return .= "<a href='{$href}' class='{$class}'>{$text}</a>";
    }
    return $return;
}

// html 转 images array
function html2images($html){
    $CI =& get_instance(); 
    $CI->load->library('LoadPhpquery');
    $CI->loadphpquery->init();
    $html = phpQuery::newDocument($html);
    $images = array();
    foreach($html['img'] as $key => $img) {
        $images[] = pq($img)->attr('src');
    }
    return $images;
}

// 格式化时间
function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    $return = '';
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
    return  "刚刚";
}
