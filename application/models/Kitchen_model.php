<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Kitchen_model extends CI_Model
{
    // orders
    var $orders_column_order = array(null, 'orders_id', 'orders_name', 'orders_quantity', null); //set column field database for datatable orderable
    var $orders_column_search = array('orders_id', 'orders_name', 'orders_quantity'); //set column field database for datatable searchable
    var $orders_order = array('orders_id' => 'DESC'); // default order

    private function _getdatatables_kitchen()
    {
        $this->db->select('*');
        $this->db->where_in('orders_status', array('DAPUR', 'DISIAPKAN'));
        $this->db->from('orders');
        $i = 0;
        foreach ($this->orders_column_search as $item) { // loop column
            if (@$_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->orders_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->orders_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $orders_order = $this->order;
            $this->db->order_by(key($orders_order), $orders_order[key($orders_order)]);
        }
    }

    function getdatatables_kitchen()
    {
        $this->_getdatatables_kitchen();
        if (@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_kitchen()
    {
        $this->_getdatatables_kitchen();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_kitchen()
    {
        $this->db->select('*');
        $this->db->where_in('orders_status', array('DAPUR', 'DISIAPKAN'));
        $this->db->from('orders');
        return $this->db->count_all_results();
    }


    public function get_ordermenu()
    {
        $this->db->select('*');
        $this->db->from('orders A');
        $this->db->join('orders_detail B', 'A.orders_number=B.orders_number');
        $this->db->where('A.orders_status', 'DISIAPKAN');
        $this->db->order_by('B.orderdetail_createdate', 'ASC');
        return $this->db->get();
    }

    public function get_listdapur()
    {
        $this->db->select('*');
        $this->db->from('orders A');
        $this->db->join('orders_detail B', 'A.orders_number=B.orders_number');
        $this->db->where('B.orderdetail_status', 'DISIAPKAN');
        $this->db->order_by('B.menu_sku ASC', 'B.orderdetail_createdate  ASC');
        return $this->db->get();
        // $this->db->get();
        // echo print_r($this->db->last_query());
    }

    public function get_listselesai()
    {
        $this->db->select('*');
        $this->db->from('orders A');
        $this->db->join('orders_detail B', 'A.orders_number=B.orders_number');
        $this->db->where('B.orderdetail_status', 'SELESAI');
        $this->db->order_by('B.orderdetail_createdate', 'ASC');
        return $this->db->get();
    }


    public function get_ordermenusama()
    {
        $this->db->select('*');
        $this->db->from('orders A');
        $this->db->join('orders_detail B', 'A.orders_number=B.orders_number');
        $this->db->where('A.orders_status', 'DISIAPKAN');
        $this->db->group_by('B.menu_name');
        $this->db->order_by('B.orderdetail_createdate', 'ASC');
        return $this->db->get();
    }


    var $disiapkan_column_order = array(null, 'orderdetail_quantity', 'orders_name', 'orderdetail_note', 'menu_name', 'orders_type', 'orderdetail_variant', null); //set column field database for datatable orderable
    var $disiapkan_column_search = array('orderdetail_quantity', 'orders_name', 'orderdetail_note', 'menu_name', 'orders_type', 'orderdetail_variant'); //set column field database for datatable searchable
    var $disiapkan_order = array('orders_name' => 'DESC'); // default order

    private function _getdatatables_disiapkan()
    {
        $this->db->select('*');
        $this->db->from('orders A');
        $this->db->join('orders_detail B', 'A.orders_number=B.orders_number');
        $this->db->where('A.orders_status', 'DISIAPKAN');
        $i = 0;
        foreach ($this->disiapkan_column_search as $item) { // loop column
            if (@$_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->disiapkan_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->disiapkan_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $disiapkan_order = $this->order;
            $this->db->order_by(key($disiapkan_order), $disiapkan_order[key($disiapkan_order)]);
        }
    }

    function getdatatables_disiapkan()
    {
        $this->_getdatatables_disiapkan();
        if (@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_disiapkan()
    {
        $this->_getdatatables_disiapkan();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_disiapkan()
    {
        $this->db->select('*');
        $this->db->from('orders A');
        $this->db->join('orders_detail B', 'A.orders_number=B.orders_number');
        $this->db->where('A.orders_status', 'DISIAPKAN');
        return $this->db->count_all_results();
    }


    // DAPUR
    var $dapur_column_order = array(null, 'orders_id', 'orders_name', 'orders_quantity', null); //set column field database for datatable orderable
    var $dapur_column_search = array('orders_id', 'orders_name', 'orders_quantity'); //set column field database for datatable searchable
    var $dapur_order = array('orders_id' => 'DESC'); // default order

    private function _getdatatables_dapur()
    {
        $this->db->select('*');
        $this->db->where_in('orders_status', array('DISIAPKAN'));
        $this->db->from('orders');
        $i = 0;
        foreach ($this->dapur_column_search as $item) { // loop column
            if (@$_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->dapur_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->dapur_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $dapur_order = $this->order;
            $this->db->order_by(key($dapur_order), $dapur_order[key($dapur_order)]);
        }
    }

    function getdatatables_dapur()
    {
        $this->_getdatatables_dapur();
        if (@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_dapur()
    {
        $this->_getdatatables_dapur();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_dapur()
    {
        $this->db->select('*');
        $this->db->where_in('orders_status', array('DISIAPKAN'));
        $this->db->from('orders');
        return $this->db->count_all_results();
    }
}
