<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends MY_Controller {

    public function index()
    {
        $this->category();
    }
    
    public function category($id = 0)
    {
        $uri = array();
        $order_by = 'order asc, id desc';
        $this->db->start_cache();
        // 分类
        if ($id) {
            $this->db->where("FIND_IN_SET('{$id}', class)");
        }
        // 搜索
        if ($q = from($_REQUEST, 'q')) {
            $this->db->like('name', $q);
            $uri['q'] = $q;
        }
        $data['count_items'] = $this->db->from('u_m_news')->count_all_results();
        $pager = pager('page', 12, $data['count_items']);
        $items = $this->db->order_by($order_by)->limit($pager['value'], $pager['offset'])->get("u_m_news")->result();
        if ($uri) $pager['uri'] = "&" . http_build_query($uri);
        $this->db->stop_cache();
        $this->db->flush_cache();
        
        $data = array(
            'title' => '新闻资讯',
            'items' => $items,
            'pager' => $pager,
        );
        $this->view('news_list', $data);
    }
    
    public function show($id = 0)
    {
        if ($item = $this->db->where('id', $id)->get('u_m_news')->row()) {
            $this->db->set('click_count', 'click_count + 1', false);
            $this->db->where('id', $id)->update('u_m_news');
            $data = array(
                'title' => $item->name,
                'keywords' => from($item, 'keywords'),
                'description' => from($item, 'description'),
                'item' => $item,
                'prev' => $this->db->where('id < ', $item->id)->order_by("id desc")->limit(1)->get("u_m_news")->row(),
                'next' => $this->db->where('id > ', $item->id)->order_by("id asc")->limit(1)->get("u_m_news")->row(),
            );
            $this->view('news_show', $data);
        } else {
            redirect('/', 'refresh');
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */