<?php if ( ! defined('IN_CMS')) exit('No direct script access allowed');
/**
 * CMS
 *
 * 一款基于并面向CodeIgniter开发者的开源轻型后端内容管理系统.
 *
 * @package     CMS
 * @author      CMS Team
 * @copyright   Copyright (c) 2011 - 2012, CMS Team.
 * @license     http://www.cms.com/license
 * @link        http://www.cms.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CMS 系统相关控制器
 *
 * @package     CMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Jeongee
 * @link        http://www.cms.com
 */
class System extends Admin_Controller
{
	/**
     * 构造函数
     *
     * @access  public
     * @return  void
     */
	public function __construct()
	{
		parent::__construct();
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * 后台默认首页
     *
     * @access  public
     * @return  void
     */
	public function home()
	{
		$this->_template('sys_default');
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * 用户修改密码表单页入口
     *
     * @access  public
     * @return  void
     */
	public function password()
	{
		$this->_check_permit();
		$this->_password_post();
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * 用户修改密码表单页呈现/处理函数 
     *
     * @access  public
     * @return  void
     */
	public function _password_post() {
		$this->_check_permit();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('old_pass', "旧密码", 'required');
		$this->form_validation->set_rules('new_pass', "新密码", 'required|min_length[6]|max_length[16]|match[new_pass_confirm]');
		$this->form_validation->set_rules('new_pass_confirm', "确认新密码", 'required|min_length[6]|max_length[16]');
		if ($this->form_validation->run() == FALSE) {
			$this->_template('sys_password');
		} else {
			$old_pass = sha1(trim($this->input->post('old_pass', TRUE)).$this->_admin->salt);
			$stored = $this->user_mdl->get_user_by_uid($this->session->userdata('uid'));

			$new = $this->input->post('new_pass', TRUE);
			$new_two = $this->input->post('new_pass_confirm', TRUE);
			if ($stored AND $old_pass == $stored->password) {
				if ($new == $new_two) {
					$this->user_mdl->update_user_password();
					$this->_message("密码更新成功!", '', TRUE);
				} else {
					$this->_message("新密码与确认新密码不一致,请重新输入!", '', TRUE);
				}
			} else {
				$this->_message("密码验证失败!", '', TRUE);
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * 更新缓存表单页
     *
     * @access  public
     * @return  void
     */
	public function cache()
	{
		$this->_check_permit();
		$this->_template('sys_cache');
	}
	
	// ------------------------------------------------------------------------
	
	/**
     * 更新缓存处理函数 
     *
     * @access  public
     * @return  void
     */
	public function _cache_post()
	{
		$this->_check_permit();
		$cache = $this->input->post('cache');
		if ($cache AND is_array($cache))
		{
			update_cache($cache);
		}
		$this->_message("缓存更新成功！", '', TRUE);	
	}
	
	// ------------------------------------------------------------------------
	
}

/* End of file system.php */
/* Location: ./admin/controllers/syestem.php */