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
 * Excel 上传 下载
 *
 * @package     CMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Jeongee
 * @link        http://www.cms.com
 */
class Excel extends Admin_Controller
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
        require_once dirname(__FILE__) . '/../libraries/PHPExcel.php';
	}
	
    // 下载文件
    public function download(){
        $model = $this->input->get('model', TRUE);
        if ( ! $model AND $this->acl->_default_link)
        {
            redirect($this->acl->_default_link);
        }
        $this->_check_permit();
        if ( ! $this->platform->cache_exists(CMS_SHARE_PATH . 'settings/model/' . $model . '.php'))
        {
            $this->_message('不存在的模型！', '', TRUE);
        }
        $this->plugin_manager->trigger('reached');
        $this->settings->load('model/' . $model);
        $data['model'] = $this->settings->item('models');
        $data['model'] = $data['model'][$model];
        $this->load->library('form');
        $this->load->library('field_behavior');
        $data['provider'] = $this->_pagination_all($data['model']);
        $array = $fields_temp = array();
        $fields = array('序号', '时间');
        $pass_fields = array(
            //'users' => explode(' ', 'id create_time nickname sex phone local interest last_login_time last_login_local height weight name'), // 用户列表
        );
        foreach($data['model']['fields'] as $key => $val) {
        	$fields_temp[$val['name']] = $val;
            if (isset($pass_fields[$model]) && !in_array($val['name'], $pass_fields[$model])) {
                continue;
            }
            if ($val['type'] == 'file') {
            	continue;
            }
            $fields[] = $val['description'];
        }
        $array[] = $fields;
        foreach($data['provider']['list'] as $key => $val) {
            unset($val->update_time);
            $val->create_time = date('Y-m-d H:i:s', $val->create_time);
            if (isset($val->image)) unset($val->image);
            if (isset($val->image2)) unset($val->image2);
            if (isset($val->file)) unset($val->file);
            if (isset($val->file2)) unset($val->file2);
            if (isset($val->userinfo_data)) unset($val->userinfo_data);
            if (isset($val->avatar)) unset($val->avatar);
            if (isset($val->content)) $val->content = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $val->content);
            foreach($val as $key2 => $val2) {
                if (isset($pass_fields[$model]) && !in_array($key2, $pass_fields[$model])) {
                    unset($val->$key2);
                }
                if (isset($fields_temp[$key2]) && $fields_temp[$key2]['type'] == 'content') {
                	$value = explode('|', $fields_temp[$key2]['values']);
                	$value = from($this->db->select($value[1])->where('id', $val2)->get("u_m_{$value[0]}")->row(), $value[1]);
                	$val->$key2 = $value;
                }
                if (isset($fields_temp[$key2]) && $fields_temp[$key2]['type'] == 'select_from_model') {
                	$value = explode('|', $fields_temp[$key2]['values']);
                	$value = from($this->db->select($value[1])->where('id', $val2)->get("u_c_{$value[0]}")->row(), $value[1]);
                	$val->$key2 = $value;
                }
            }
            $list = array(
                $val->id,
                $val->create_time,
            );
            foreach($data['model']['fields'] as $key2 => $val2) {
                // if (isset($val->$val2['name'])) $list[] = '"' . str_replace('"', '""', $val->$val2['name']) . '"';
                if (isset($val->$val2['name'])) $list[] = $val->$val2['name'];
            }
            $array[] = $list;
        }
        $filename = date("Ymd ") . $data['model']['description'];
        $data = $array;
        $this->_download($filename, $data);
    }

    // 上传文件
    public function _upload_post(){
        if (!$_FILES['file']['tmp_name']) {
            $this->_message('上传失败', '', TRUE);
        }
        $model = $this->input->get('model', TRUE);
        $table = $this->db->dbprefix('u_m_') . $model;

        $objPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $filename = $_FILES['file']['tmp_name'];
        $PHPExcel = $objReader->load($filename);
        $currentSheet = $PHPExcel->getSheet(0);
        $array = $currentSheet->ToArray();
        
        // 去除首行
        if (isset($array[0])) unset($array[0]);
        $data = array();
        foreach ($array as $key => $value) {
            $values = array();
            foreach ($value as $key => $value) {
                if ($key == 1) {
                    // 时间
                    $value = strtotime($value);
                    $values[] = $this->db->escape($value);
                    $values[] = $this->db->escape($value);
                    // uid
                    $values[] = $this->db->escape($this->_admin->uid);
                    $value = $this->_admin->uid;
                }
                $values[] = $this->db->escape($value);
            }
            $data[] = $values;
        }

        $this->db->db_debug = false;
        $this->db->trans_start();
        echo "<!--\n";
        foreach ($data as $key => $values) {
            $sql = "INSERT INTO ".$table." VALUES (".implode(', ', $values).");";
            echo "{$sql}\n";
            $this->db->query($sql);
        }
        echo "-->\n";
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            $this->_message('导入失败，导入格式有误。<br/><br/>可能发生的错误：<br/>序号已被使用。序号可不填，但不能填写重复的序号。', '', TRUE);
        } else {
            $this->_message('导入成功！', '', TRUE);
        }
    }

    private function _download($filename, $data) {
        $objPHPExcel = new PHPExcel();
        // 填充数据
        $l=0;//行
        foreach ($data as $key => $value) {
            $count = count($value);
            for ($p=0; $p < $count; $p++) { //列
                $skey = PHPExcel_Cell::stringFromColumnIndex($p) . ($l + 1); // 生成行列索引
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($skey,$value[$p]);
                // 首行加粗
                if ($l == 0)
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($skey)->getFont()->setBold(true);
            }
            $l++;        
        }
        // 指向第一格
        $objPHPExcel->setActiveSheetIndex()->getStyle('A1');
        // 导出
        // Redirect output to a client’s web browser (Excel5)
        $xlsTitle = iconv('utf-8', 'gb2312', $filename);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $xlsTitle . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    private function _pagination_all($model)
    {
        $this->load->library('pagination');
        $config['base_url'] = backend_url('content/view');
        $config['per_page'] = $model['perpage'];
        $config['uri_segment'] = 3;
        $config['suffix'] = '?model=' . $model['name'];
            
        $condition = array('id >' => '0');
        $data['where'] = array();
        
        foreach ($model['searchable'] as $v)
        {
            $this->field_behavior->on_do_search($model['fields'][$v], $condition, $data['where'], $config['suffix']);
        }
        
        $this->plugin_manager->trigger('querying', $condition);
        
        $config['total_rows'] = $this->db->where($condition)->count_all_results($this->db->dbprefix('u_m_') . $model['name']);
        
        $this->db->from($this->db->dbprefix('u_m_') . $model['name']);
        $this->db->where($condition);
        $this->field_behavior->set_extra_condition();
        
        $this->db->order_by('id', 'ASC');
        $this->db->offset($this->uri->segment($config['uri_segment'], 0));
        
        $data['list'] = $this->db->get()->result();
        
        $this->plugin_manager->trigger('listing', $data['list']);
        
        $config['first_url'] = $config['base_url'] . $config['suffix'];
        
        $this->pagination->initialize($config);
        
        $data['pagination'] = $this->pagination->create_links();
        
        return $data;
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
        if (is_array($array)) $return = (isset($array[$key]) === TRUE && empty($array[$key]) === FALSE) ? $array[$key] : $default;

        return $return;
    } 

    
	
}

/* End of file content.php */
/* Location: ./admin/controllers/excel.php */
