<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Kitchen extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        date_default_timezone_set("ASIA/JAKARTA");
    }

    public function index()
    {
        $page_data['page_title']    = 'Dapur';
        $page_data['page_name']     = 'kitchen';
        $this->load->view('index', $page_data);
    }

    public function progress()
    {
        $page_data['page_title']    = 'Dapur';
        $page_data['page_name']     = 'kitchen_progress';
        $this->load->view('index', $page_data);
    }



    function getdatatables_kitchen()
    {
        $list = $this->kitchen_model->getdatatables_kitchen();
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $no++;
            if ($item->orders_status == "DAPUR") {
                $checked = '';
            } else {
                $checked = 'checked';
            }
            $row = array();
            $row[] = '<small style="font-size:12px"><input type="checkbox" name="order_id[]" onclick="checklist(' . $item->orders_id . ')" value="' . @$item->order_id . '" class="form-check-input ml-2 data-check" id="cs3" ' . @$item->order_id . ' ' . $checked . '></small>';
            $row[] = '<small style="font-size:12px">' . $item->orders_name . '</small>';
            $row[] = '<small style="font-size:12px">' . $item->orders_quantity . ' Pesanan</small>';
            $data[] = $row;
        }
        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->kitchen_model->count_all_kitchen(),
            "recordsFiltered" => $this->kitchen_model->count_filtered_kitchen(),
            "data" => $data,
        );
        // output to json format
        echo json_encode($output);
    }


    function getdatatables_disiapkan()
    {
        $list = $this->kitchen_model->getdatatables_disiapkan();
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = '<small style="font-size:12px">' . $item->orderdetail_quantity . '</small>';
            $row[] = '<small style="font-size:12px"><b>' . $item->orders_name . '</b><br><small class="text-info">' . $item->orderdetail_note . '</small>';
            $row[] = '<small style="font-size:12px"><b>' . $item->menu_name . '</b><br><small class="text-info">' . $item->orders_type . '</small>';
            $row[] = '<small style="font-size:12px">' . $item->orderdetail_variant . '</small>';
            $data[] = $row;
        }
        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->kitchen_model->count_all_disiapkan(),
            "recordsFiltered" => $this->kitchen_model->count_filtered_disiapkan(),
            "data" => $data,
        );
        // output to json format
        echo json_encode($output);
    }

    function getdatatables_dapur()
    {
        $list = $this->kitchen_model->getdatatables_dapur();
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = '<small style="font-size:12px"><input type="checkbox" name="order_id[]" onclick="checklist(' . $item->orders_id . ')" value="' . @$item->order_id . '" class="form-check-input ml-2 data-check" id="cs3" ' . @$item->order_id . '></small>';
            $row[] = '<small style="font-size:12px">' . $item->orders_name . '</small>';
            $row[] = '<small style="font-size:12px">' . $item->orders_quantity . ' Pesanan</small>';
            $data[] = $row;
        }
        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->kitchen_model->count_all_dapur(),
            "recordsFiltered" => $this->kitchen_model->count_filtered_dapur(),
            "data" => $data,
        );
        // output to json format
        echo json_encode($output);
    }


    function list_ordermenu()
    {
        $menu = $this->kitchen_model->get_ordermenu();
        $html = '';
        $html .= '<div class="card full-height">
            <div class="card-body" style="max-height: 550px;overflow-y: auto;">';
        foreach ($menu->result() as $row) {
            if ($row->orderdetail_variant != "") {
                $variant = '<span class="pl-3">(' . $row->orderdetail_variant . ')</span>';
            } else {
                $variant = '';
            }
            $html .= '<div class="d-flex">
                    <div class="flex-1 ml-3 pt-1">
                        <h6 class="text-uppercase fw-bold mb-1">' . $row->menu_name  . $variant . ' <span class="text-info pl-3">' . $row->orders_type . '</span></h6>
                        <span class="text-muted">' . $row->orderdetail_note . '</span>
                    </div>
                    <div class="float-right pt-1">
                        <small class="text-muted">' . date('H:i:s', strtotime($row->orderdetail_createdate)) . '</small>
                    </div>
                </div>
                <div class="separator-dashed"></div>';
        }

        $html .= '</div>
        </div>';


        $menu_sama = $this->kitchen_model->get_ordermenusama();
        $html .= '<div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Menu yang sama</h4>
                    </div>
                    <div class="card-body text-center">';
        foreach ($menu_sama->result() as $row) {
            $this->db->where('orderdetail_status', 'DISIAPKAN');
            $this->db->where('menu_sku', $row->menu_sku);
            $total = $this->db->get('orders_detail');
            if ($total->num_rows() > 1) {
                $html .= '' . $row->menu_name  . ' (' . $total->num_rows() . ' Pcs),&emsp;';
            }
        }
        $html .= '</div>
        ';

        echo json_encode([
            'html' => $html,
        ]);
    }

    public function order_checklist()
    {
        $this->db->where('orders_id', $this->input->post('orders_id'));
        $status = $this->db->get('orders');


        if ($status->row()->orders_status == "DAPUR") {

            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->update('orders', ['orders_status' => 'DISIAPKAN']);


            $this->db->where('orderdetail_status', NULL);
            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->update('orders_detail', ['orderdetail_status' => "DISIAPKAN"]);
        } else if ($status->row()->orders_status == "DISIAPKAN") {

            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->update('orders', ['orders_status' => 'DAPUR']);


            $this->db->where('orderdetail_status !=', "SELESAI");
            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->update('orders_detail', ['orderdetail_status' => NULL]);
        }

        echo json_encode([
            'status' => 'OK',
        ]);
    }



    function list_dapur()
    {
        $menu = $this->kitchen_model->get_listdapur();
        $html = '';
        $html .= '<div class="card full-height">
            <div class="card-body" style="max-height: 550px;overflow-y: auto;">';
        foreach ($menu->result() as $row) {
            if ($row->orderdetail_variant != "") {
                $variant = '<span class="pl-3">(' . $row->orderdetail_variant . ')</span>';
            } else {
                $variant = '';
            }
            $html .= '<div class="d-flex">
                    <div class="avatar">
                        <input type="checkbox" name="orderdetail_id[]" onclick="selesai(' . $row->orderdetail_id . ',' . $row->orders_id . ')" class="form-check-input ml-2 mt-3 data-check">
                    </div>
                    <div class="flex-1 ml-3 pt-1">
                        <h6 class="text-uppercase fw-bold mb-1">' . $row->orders_name . '<br>' . $row->menu_name  . $variant . ' <span class="text-info pl-3">' . $row->orders_type . '</span></h6>
                        <span class="text-muted">' . $row->orderdetail_note . '</span>
                    </div>
                    <div class="float-right pt-1">
                        <small class="text-muted">' . date('H:i:s', strtotime($row->orderdetail_createdate)) . '</small>
                    </div>
                </div>
                <div class="separator-dashed"></div>';
        }

        $html .= '</div>
        </div>';


        echo json_encode([
            'html' => $html,
        ]);
    }
    function list_selesai()
    {
        $menu = $this->kitchen_model->get_listselesai();
        $html = '';
        $html .= '<div class="card full-height">
            <div class="card-body" style="max-height: 550px;overflow-y: auto;">';
        foreach ($menu->result() as $row) {
            if ($row->orderdetail_variant != "") {
                $variant = '<span class="pl-3">(' . $row->orderdetail_variant . ')</span>';
            } else {
                $variant = '';
            }
            $html .= '<div class="d-flex">
                    <div class="avatar">
                        <input type="checkbox" name="orderdetail_id[]" onclick="dapur(' . $row->orderdetail_id . ')" class="form-check-input ml-2 mt-3 data-check">
                    </div>
                    <div class="flex-1 ml-3 pt-1">
                        <h6 class="text-uppercase fw-bold mb-1">' . $row->orders_name . '<br>' . $row->menu_name  . $variant . ' <span class="text-info pl-3">' . $row->orders_type . '</span></h6>
                        <span class="text-muted">' . $row->orderdetail_note . '</span>
                    </div>
                    <div class="float-right pt-1">
                        <small class="text-muted">' . date('H:i:s', strtotime($row->orderdetail_createdate)) . '</small>
                    </div>
                </div>
                <div class="separator-dashed"></div>';
        }

        $html .= '</div>
        </div>';


        echo json_encode([
            'html' => $html,
        ]);
    }

    public function action_orderselesai()
    {
        $this->db->where('orderdetail_id', $this->input->post('orderdetail_id'));
        $this->db->update('orders_detail', ['orderdetail_status' => 'SELESAI']);

        $this->db->where('orders_id', $this->input->post('orders_id'));
        $this->db->where('orderdetail_status', 'SELESAI');
        $orders_detail = $this->db->get('orders_detail');

        echo json_encode([
            'status' => 'OK',
            'total' => $orders_detail->num_rows(),
        ]);
    }

    public function action_ordersdapur()
    {
        $this->db->where('orderdetail_id', $this->input->post('orderdetail_id'));
        $this->db->update('orders_detail', ['orderdetail_status' => 'DISIAPKAN']);

        echo json_encode([
            'status' => 'OK',
        ]);
    }
}
