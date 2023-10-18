<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function get_users($numbers)
    {
        $this->db->where('users_number', $numbers);
        return $this->db->get_where('users');
    }
}
