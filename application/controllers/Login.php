<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("ASIA/JAKARTA");
    }
    public function index()
    {
        $this->load->view('login');
        // $this->load->view('index-test');
    }

    public function auth()
    {
        $this->load->library('bcrypt');
        $users = $this->login_model->get_users($this->input->post('number'));
        if ($users->num_rows() > 0 && $users->row()->users_status == 'Y') {
            if ($this->bcrypt->check_password($this->input->post('password'), $users->row()->users_password)) {
                echo "masuk";

                $this->session->set_userdata('users_name', $users->row()->users_name);
                $this->session->set_userdata('users_number', $users->row()->users_number);
                $this->session->set_userdata('users_type', $users->row()->users_type);
                redirect(site_url('login/dashboard'));
            } else {
                echo "password tidak sama";
            }
        } else if ($users->num_rows() > 0 && $users->row()->users_status == 'N') {
            echo "tidak aktif";
        } else {
            echo "tidak terdaftar";
        }
    }

    public function dashboard()
    {
        $page_data['page_title']    = 'Dashboard';
        $page_data['page_name']     = 'dashboard';
        $this->load->view('index', $page_data);
    }

    public function pass()
    {
        $this->load->library('encryption');
        $data['pass']       = 'CheckPassword@11213123';
        $data['encrypt']    = $this->encryption->encrypt($data['pass']);
        $data['decrypt']    = $this->encryption->decrypt($data['encrypt']);
        $data['jumlah']     = strlen($data['encrypt']);

        echo "<pre>";
        echo print_r($data);
        echo "</pre>";


        $this->load->library('bcrypt');
        // $password = '123';
        // echo $hash = $this->bcrypt->hash_password($password);

        // $password2 = '123';

        // if ($this->bcrypt->check_password($password2, $hash)) {
        //     // Password does match stored password.
        //     // echo "Sama";
        // } else {
        //     // echo 'Tidak sama';
        //     // Password does not match stored password.
        // }
    }
}
