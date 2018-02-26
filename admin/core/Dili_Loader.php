<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * CMS Loader 扩展CI_Loader
 *
 * 用于支持多皮肤
 *
 * @package     CMS
 * @subpackage  core
 * @category    core
 * @author      Jeongee
 * @link        http://www.cms.com
 */		
class Dili_Loader extends CI_Loader
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
     * 切换视图路径
     *
     * @access  public
     * @return  void
     */
	public function switch_theme($theme = 'default')
	{
		$this->_ci_view_paths = array(APPPATH . 'templates/' . $theme . '/'	=> TRUE);
	}
	
	// ------------------------------------------------------------------------

}

function CI()
{
    $CI = & get_instance();
    return $CI;
}

/* End of file Dili_Loader.php */
/* Location: ./admin/core/Dili_Loader.php */