<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class MY_Controller extends CI_Controller
{
    
    var $user = array();

    /**
     * 构造函数
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('tool');
        /*
        $this->load->library('session');
        $this->load->model('user_model');
        // 实时更新用户数据
        if ($user_id = $this->session->userdata('user_id')) {
            if ($this->user = $this->user_model->getUserInfo($user_id)) {
                $this->session->set_userdata(array('user_id' => $user_id));
            } else {
                $this->session->unset_userdata('user_id');
            }
        }
        */
    }

    /**
     * 加载视图
     *
     * @access  protected
     * @param   string
     * @param   array
     * @return  void
     */
    function view($template, $data = array(), $layout = '_layout')
    {
        $site['site'] = $this->db->get('site_settings')->row();
        $site['title'] = $site['site']->site_name;
        $site['keywords'] = $site['site']->site_keyword;
        $site['description'] = $site['site']->site_description;
        $site['lang'] = $this->lang();
        $data = array_merge((array)$site, (array)$data);
        if ($data['title'] !== $site['title']) {
            $data['title'] .= "-{$site['title']}";
        }
        $data['tpl'] = $template;
        $data['user'] = $this->user;
        $data['_this'] = $this;
        $this->load->view("{$site['lang']}/{$layout}", $data);
    }
    
    
    /**
     * 获得当前语言
     * 
     * @access global
     * @return void
     */
    function lang($default = 'default')
    {
        return (in_array($this->uri->segment(1), array('cn', 'en', 'ja', 'ar', 'ru', 'm'))) ? $this->uri->segment(1) : $default;
    }
    
    /**
     * 上传文件
     * 
     * @access global
     * @return void
     */
    function up_file($name, $type = array('jpg', 'gif', 'png', 'jpeg'))
    {
        $file_name = '';
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            $CI = & get_instance();
            $CI->load->helper('date');
            $_timestamp = now();
            $upload['folder'] = date('Y/m', $_timestamp);
            $target_path = FCPATH.'attachments/'.$upload['folder'];
            $realname = explode('.', $_FILES[$name]['name']);
            $upload['type'] = strtolower(array_pop($realname));
            $upload['realname'] = implode('.', $realname);
            $upload['name'] = $_timestamp.substr(md5($upload['realname']. rand()), 0, 16);
            $target_file = $target_path.'/'.$upload['name'].'.'.$upload['type'];
            if (in_array($upload['type'], $type)) {
                $target_path = dirname($target_file);
                if ( ! is_dir($target_path) AND  ! mkdir($target_path, 0755, TRUE))
                {
                    return '';
                }
                else
                {
                    if (move_uploaded_file($_FILES[$name]['tmp_name'], $target_file)) {
                        $file_name = '/attachments/'.$upload['folder'].'/'.$upload['name'].'.'.$upload['type'];
                    }
                }
            }
        }
        return $file_name;
    }

}
