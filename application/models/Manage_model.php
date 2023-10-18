<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Manage_model extends CI_Model
{
    // Users
    var $users_column_order = array(null, 'users_id', 'users_number', 'users_name', 'users_type', 'users_status', null); //set column field database for datatable orderable
    var $users_column_search = array('users_id', 'users_number', 'users_name', 'users_type', 'users_status'); //set column field database for datatable searchable
    var $users_order = array('users_id' => 'DESC'); // default order

    private function _getdatatables_users()
    {
        $this->db->select('*');
        $this->db->from('users');
        $i = 0;
        foreach ($this->users_column_search as $item) { // loop column
            if (@$_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->users_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->users_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $users_order = $this->order;
            $this->db->order_by(key($users_order), $users_order[key($users_order)]);
        }
    }

    function getdatatables_users()
    {
        $this->_getdatatables_users();
        if (@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_users()
    {
        $this->_getdatatables_users();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_users()
    {
        $this->db->select('*');
        $this->db->from('users');
        return $this->db->count_all_results();
    }


    public function users_add()
    {
        $users_password = $this->bcrypt->hash_password($this->input->post('users_password'));

        $data['users_id']       = $this->manage_model->generate_usersid();
        $data['users_number']   = $this->input->post('users_number');
        $data['users_name']     = $this->input->post('users_name');
        $data['users_password'] = $users_password;
        $data['users_status']   = 'Y';
        $data['users_type']     = $this->input->post('users_type');
        $data['users_createby'] = $this->session->userdata('users_name');

        $this->db->where('users_number', $this->input->post('users_number'));
        $users = $this->db->get('users');

        if ($users->num_rows() == 0) {
            $this->db->insert('users', $data);
            $result = 'success';
        } else {
            $result = 'duplicate';
        }
        return $result;
    }

    public function generate_usersid()
    {
        $users = $this->db->get('users')->num_rows() + 1;

        if ($users > 0 || $users < 10) {
            $number = '00' . $users;
        } else if ($users > 10 || $users < 100) {
            $number = '0' . $users;
        } else {
            $number = $users;
        }

        $users_id = 'U' . rand(1, 9) . '-' . date('my') . '-' . $number;
        return $users_id;
    }

    public function menu_add()
    {
        if (@$_FILES["menu_image"]['type'] != "") {
            $filetype = $_FILES["menu_image"]['type'];
            if ($filetype == "image/jpeg") {
                $menu_image     = 'uploads/menu/' . rand(1, 100) . date('dmHis') . '.jpg';
                move_uploaded_file($_FILES['menu_image']['tmp_name'], $menu_image);
            } else if ($filetype == "image/png") {
                $menu_image     = 'uploads/menu/' . rand(1, 100) . date('dmHis') . '.png';
                move_uploaded_file($_FILES['menu_image']['tmp_name'], $menu_image);
            } else {
                $menu_image     = 'uploads/menu/' . rand(1, 100) . date('dmHis') . '.jpg';
                move_uploaded_file($_FILES['menu_image']['tmp_name'], $menu_image);
            }
            $data['menu_image']            = base_url() . $menu_image;
        }

        $data['menu_sku']           = $this->input->post('menu_sku');
        $data['menu_name']          = $this->input->post('menu_name');
        $data['menu_basic_price']   = preg_replace('/[^0-9]/', '', $this->input->post('menu_basic_price'));
        $data['menu_selling_price'] = preg_replace('/[^0-9]/', '', $this->input->post('menu_selling_price'));
        $data['menu_description']   = $this->input->post('menu_description');


        $menu_check = $this->db->get_where('menu', ['menu_sku' => $this->input->post('menu_sku')]);
        if ($menu_check->num_rows() == 0) {
            $result = 'success';
            $this->db->insert('menu', $data);
            $menu_id = $this->db->insert_id();

            $stock['stock_menuid']      = $menu_id;
            $stock['stock_menuname']    = $this->input->post('menu_name');
            $stock['stock_menuvariant'] = NULL;
            $stock['stock_total']       = 0;
            $stock['stock_createdby']   = $this->session->userdata('users_name');

            $this->db->insert('stock', $stock);


            for ($i = 0; $i < count($this->input->post('varian_id')); $i++) {
                $varian['menu_id']          = $menu_id;
                $varian['variant_name']     = $this->input->post('menu_variant')[$i];

                if ($this->input->post('menu_variant')[$i] != "") {
                    $this->db->insert('variant', $varian);
                }


                $stock['stock_menuid']      = $menu_id;
                $stock['stock_menuname']    = $this->input->post('menu_name');
                $stock['stock_menuvariant'] = $this->input->post('menu_variant')[$i];
                $stock['stock_total']       = 0;
                $stock['stock_createdby']   = $this->session->userdata('users_name');

                $this->db->insert('stock', $stock);

                $this->db->where('stock_menuid', $menu_id);
                $this->db->where('stock_menuvariant', NULL);
                $this->db->delete('stock');
            }
        } else {
            $result = 'duplicate';
        }
        return $result;
    }
}
