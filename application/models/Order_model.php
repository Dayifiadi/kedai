<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order_model extends CI_Model
{

    public function generate_ordernumber()
    {

        $MONTH = date('m');
        $YEAR = date('Y');
        $date = date('Y-m-d');
        $MY = date('my');


        $this->db->where('MONTH(ordernumber_date)', $MONTH);
        $this->db->where('YEAR(ordernumber_date)', $YEAR);
        $this->db->where('ordernumber_type', 'ORDER');
        $orders_number = $this->db->get('orders_number');

        if ($orders_number->num_rows() == 0) {
            $orders = 1;

            $update['ordernumber_date'] = $date;
            $update['ordernumber_last'] = $orders;

            $this->db->where('ordernumber_type', 'ORDER');
            $this->db->update('orders_number', $update);
        } else {
            $orders = $orders_number->row()->ordernumber_last + 1;

            $update['ordernumber_last'] = $orders;
            $this->db->where('ordernumber_type', 'ORDER');
            $this->db->update('orders_number', $update);
        }

        if ($orders > 0 || $orders < 10) {
            $number = '000' . $orders;
        } else if ($orders > 10 || $orders < 100) {
            $number = '00' . $orders;
        } else if ($orders > 100 || $orders < 1000) {
            $number = '0' . $orders;
        } else {
            $number = $orders;
        }

        $order_number = 'K-' . $MY . '-' . $number;
        return $order_number;
    }

    public function get_menu()
    {
        return $this->db->get('menu');
    }

    public function get_order($orders_id)
    {
        $orders = $this->db->get_where('orders', ['orders_id' => $orders_id]);
        return $orders;
    }

    public function get_orderdetail($orders_id)
    {
        $this->db->group_by('menu_sku, orderdetail_variant');
        return $this->db->get_where('orders_detail', ['orders_id' => $orders_id]);
    }

    public function get_orderdetailqty($orders_id)
    {
        return $this->db->get_where('orders_detail', ['orders_id' => $orders_id])->num_rows();
    }

    public function get_variant($menu_id)
    {
        return $this->db->get_where('variant', ['menu_id' => $menu_id]);
    }
}
