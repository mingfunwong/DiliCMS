<?php

class User_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    // 获取用户信息
    function getUserInfo($user_id) {
        $user = $this->db->where('id', $user_id)->get('u_m_users')->row();
        return $user;
    }
    

}
