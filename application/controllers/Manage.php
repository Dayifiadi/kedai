<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Manage extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("ASIA/JAKARTA");
        $this->load->library('bcrypt');
    }

    public function index()
    {
        $page_data['page_title']    = 'Manage';
        $page_data['page_name']     = 'manage_users';
        $this->load->view('index', $page_data);
    }


    public function menu_add()
    {
        $page_data['page_title']    = 'Tambah Menu';
        $page_data['page_name']     = 'manage_menu_add';
        $this->load->view('index', $page_data);
    }

    function getdatatables_users()
    {
        $list = $this->manage_model->getdatatables_users();
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $no++;
            $detail = '';
            $row = array();
            $row[] = '<small style="font-size:12px">' . $item->users_number . '</small>';
            $row[] = '<small style="font-size:12px">' . $item->users_name . '</small>';
            $row[] = '<small style="font-size:12px">' . $item->users_type . '</small>';
            $row[] = '<small style="font-size:12px">' . $item->users_type . '</small>';
            $row[] = $detail;
            $data[] = $row;
        }
        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->manage_model->count_all_users(),
            "recordsFiltered" => $this->manage_model->count_filtered_users(),
            "data" => $data,
        );
        // output to json format
        echo json_encode($output);
    }

    public function users_add()
    {
        $users_add = $this->manage_model->users_add();
        if ($users_add == "success") {
            $this->session->set_flashdata('success', 'Pengguna berhasil ditambahkan');
            redirect(site_url('manage'), 'refresh');
        } else {
            $this->session->set_flashdata('error', 'Pengguna gagal ditambahkan, Nomor HP sudah terdaftar');
            redirect(site_url('manage'), 'refresh');
        }
    }

    public function get_sku()
    {
        $rand = $this->bcrypt->hash_password(date('ymdHis'));
        $menu_sku = 'M-' . substr($rand, 40, 48);
        echo json_encode([
            'status'  => 'success',
            'menu_sku' => $menu_sku,
        ]);
    }

    public function menu($params)
    {
        if ($params == "add") {
            $menu_add = $this->manage_model->menu_add();
            if ($menu_add == "success") {
                $this->session->set_flashdata('success', 'Menu berhasil ditambahkan');
                redirect(site_url('manage/menu_add'), 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Menu gagal ditambahkan, SKU sudah terdaftar');
                redirect(site_url('manage/menu_add'), 'refresh');
            }
        }
    }
}
