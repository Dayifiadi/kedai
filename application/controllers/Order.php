<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        date_default_timezone_set("ASIA/JAKARTA");
    }

    public function index()
    {
        $page_data['page_title']    = 'Pemesanan';
        $page_data['page_name']     = 'order';
        $page_data['menu']          = $this->order_model->get_menu();
        $this->load->view('index', $page_data);
    }

    public function order_detail($id)
    {
        $orders_id                  = decrypt_url($id);
        $page_data['page_title']    = 'Detail Pemesanan';
        $page_data['page_name']     = 'order_detail';
        $page_data['orders']        = $this->order_model->get_order($orders_id);
        $page_data['orderdetail']   = $this->order_model->get_orderdetail($page_data['orders']->row()->orders_number);
        $this->load->view('index', $page_data);
    }


    public function add_order()
    {
        if ($this->input->post('orders_number') == "" || $this->input->post('orders_number') == NULL) {
            $orders_number = $this->order_model->generate_ordernumber();
            $orders['orders_number']    = $orders_number;
            $orders['orders_name']      = $this->input->post('orders_name');
            $orders['orders_phone']     = $this->input->post('orders_phone');
            $orders['orders_status']    = 'KASIR';
            $orders['orders_createdby'] = $this->session->userdata('users_name');

            $this->db->insert('orders', $orders);
            $orders_id = $this->db->insert_id();
        } else {
            $orders_id = $this->db->get_where('orders', ['orders_number' => $this->input->post('orders_number')])->row()->orders_id;
            $orders_number = $this->input->post('orders_number');
        }


        $menu = $this->db->get_where('menu', ['menu_sku' => $this->input->post('menu_sku')]);

        $this->db->where('orders_number', $orders_number);
        $this->db->where('menu_sku', $this->input->post('menu_sku'));
        $orders = $this->db->get('orders_detail');

        $data['menu_sku']               = $this->input->post('menu_sku');
        $data['menu_name']              = $menu->row()->menu_name;
        $data['orders_number']          = $orders_number;
        $data['orders_id']              = $orders_id;
        $data['orderdetail_quantity']   = 1;
        $data['orderdetail_price']      = $menu->row()->menu_selling_price;
        $data['orderdetail_total']      = $menu->row()->menu_selling_price;

        $this->db->insert('orders_detail', $data);

        $this->db->group_by('orders_id,menu_sku');
        $order_data = $this->db->get_where('orders_detail', ['orders_id' => $orders_id]);
        $html = '';
        $total_orders = 0;
        $total = 0;

        $html .= '<div class="card">
                    <div class="card-header">
                        <h4 class="text-center">' . $orders_number . '<br><small>' . $this->input->post('orders_name') . '</samll></h4>
                    </div>
                    <div class="card-body">
                        <div class="invoice-detail">
                            <div class="invoice-item" style="max-height: 200px;overflow-y: auto;">
                            <table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
                            <thead>
                                <tr>
                                    <td style="font-size:14px"><strong>Menu</strong></td>
                                    <td class="text-center col-xs-1" style="font-size:14px"><strong>Jumlah</strong></td>
                                </tr>
                            </thead>';
        foreach ($order_data->result() as $row) {
            $this->db->where('orders_id', $orders_id);
            $this->db->where('menu_sku', $row->menu_sku);
            $total = $this->db->get('orders_detail');
            $total_orders = $total_orders + $row->orderdetail_quantity;
            $html .= '<tbody>
                                <tr style="height:8px">
                                    <td style="font-size:12px">' . $row->menu_name . '</td>
                                    <td class="text-center col-xs-1" style="font-size:12px">' . $total->num_rows() . '</td>
                                </tr>';
        }
        $html .= '</tbody>
                        </table>    
                            </div>
                        </div>
                        <br>
                        <a class="btn btn-primary btn-block" href="' . base_url('order/order_detail/') . encrypt_url($orders_id) . '">Buat Pesanan (' . $total_orders . ')</a>
                        <a href="' . base_url('order/delete_orderdata/') . $row->orders_number . '" class="btn btn-danger btn-block">Hapus Pesanan</a>
                    </div>
                </div>';


        echo json_encode([
            'status'  => 'success',
            'message' => 'Berhasil ditambahkan',
            'orders_number' => $orders_number,
            'html' => $html,
        ]);
    }

    public function delete_order()
    {
        $menu = $this->db->get_where('menu', ['menu_sku' => $this->input->post('menu_sku')]);
        $orders_id = $this->db->get_where('orders', ['orders_number' => $this->input->post('orders_number')])->row()->orders_id;

        $this->db->where('orders_number', $this->input->post('orders_number'));
        $this->db->where('menu_sku', $this->input->post('menu_sku'));
        $orders = $this->db->get('orders_detail');

        if ($orders->num_rows() > 0 && $orders->row()->orderdetail_quantity > 0) {
            $order_update['orderdetail_quantity']       = $orders->row()->orderdetail_quantity - 1;
            $order_update['orderdetail_total']          = $menu->row()->menu_selling_price * $order_update['orderdetail_quantity'];

            if ($order_update['orderdetail_quantity'] > 0) {
                $this->db->where('orders_number', $this->input->post('orders_number'));
                $this->db->where('menu_sku', $this->input->post('menu_sku'));
                $this->db->update('orders_detail', $order_update);

                $status = 'success';
                $message = 'Berhasil ditambahkan';
            } else {
                $this->db->where('orders_number', $this->input->post('orders_number'));
                $this->db->where('menu_sku', $this->input->post('menu_sku'));
                $this->db->delete('orders_detail');


                $this->db->where('orders_number', $this->input->post('orders_number'));
                $orders_detail_check = $this->db->get('orders_detail');


                $status = 'success';
                $message = 'Berhasil ditambahkan';
                if ($orders_detail_check->num_rows() == 0) {
                    $this->db->where('orders_number', $this->input->post('orders_number'));
                    $this->db->delete('orders');

                    $status = 'delete';
                    $message = 'Pesanan berhasil dihapus';
                }
            }
        } else {
            $status = 'failed';
            $message = 'Belum memesan menu ' . $menu->row()->menu_name;
        }

        $order_data = $this->db->get_where('orders_detail', ['orders_number' => $this->input->post('orders_number')]);
        $html = '';
        $total_orders = 0;
        $html .= '<div class="card">
            <div class="card-header">
                <h4 class="text-center">' . $this->input->post('orders_number') . '<br><small>' . $this->input->post('orders_name') . '</samll></h4>
            </div>
            <div class="card-body ">
                <div class="invoice-detail">
                    <div class="invoice-item" style="max-height: 200px;overflow-y: auto;">
                        <div class="">
                            <table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
                                <thead>
                                    <tr>
                                        <td style="font-size:14px"><strong>Menu</strong></td>
                                        <td class="text-center col-xs-1" style="font-size:14px"><strong>Jumlah</strong></td>
                                    </tr>
                                </thead>';
        foreach ($order_data->result() as $row) {
            $total_orders = $total_orders + $row->orderdetail_quantity;
            $html .= '<tbody>
                                    <tr style="height:8px">
                                        <td style="font-size:12px">' . $row->menu_name . '</td>
                                        <td class="text-center col-xs-1" style="font-size:12px">' . $row->orderdetail_quantity . '</td>
                                    </tr>';
        }
        $html .= '</tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <a class="btn btn-primary btn-block" href="' . base_url('order/order_detail/') . encrypt_url($orders_id) . '">Buat Pesanan (' . $total_orders . ')</a>
                    <a href="' . base_url('order/delete_orderdata/') . $this->input->post('orders_number') . '" class="btn btn-danger btn-block">Hapus Pesanan</a>
                </div>
            </div>';


        echo json_encode([
            'status'  => $status,
            'message' => $message,
            'orders_number' => '',
            'html' => $html,
        ]);
    }

    function ordervariant_add()
    {

        if ($this->input->post('orders_number') == "" || $this->input->post('orders_number') == NULL) {
            $orders_number = $this->order_model->generate_ordernumber();
            $orders['orders_number']    = $orders_number;
            $orders['orders_name']      = $this->input->post('orders_name');
            $orders['orders_phone']     = $this->input->post('orders_phone');
            $orders['orders_status']    = 'KASIR';
            $orders['orders_createdby'] = $this->session->userdata('users_name');

            $this->db->insert('orders', $orders);
            $orders_id = $this->db->insert_id();
        } else {
            $orders_number = $this->input->post('orders_number');
            $orders_id = $this->db->get_where('orders', ['orders_number' => $this->input->post('orders_number')])->row()->orders_id;
        }


        $menu = $this->db->get_where('menu', ['menu_id' => $this->input->post('menu_id')]);
        for ($i = 0; $i < count($this->input->post('sequence')); $i++) {
            if ($this->input->post('menu_quantity')[$i] > 0) {

                for ($j = 0; $j < $this->input->post('menu_quantity')[$i]; $j++) {
                    $data['menu_sku']               = $menu->row()->menu_sku;
                    $data['menu_name']              = $menu->row()->menu_name;
                    $data['orders_number']          = $orders_number;
                    $data['orders_id']              = $orders_id;
                    $data['orderdetail_quantity']   = 1;
                    $data['orderdetail_price']      = $menu->row()->menu_selling_price;
                    $data['orderdetail_total']      = $menu->row()->menu_selling_price;
                    $data['orderdetail_variant']    = $this->input->post('variant_name')[$i];

                    $this->db->insert('orders_detail', $data);
                }
            }
        }

        $this->session->set_flashdata('orders_number', $orders_number);
        redirect(site_url('order'), 'refresh');
    }

    public function edit_order()
    {
        $orders = $this->db->get_where('orders', ['orders_number' => $this->input->post('orders_number')]);

        $orders_id = $orders->row()->orders_id;
        $this->db->group_by('orders_id,menu_sku');
        $order_data = $this->db->get_where('orders_detail', ['orders_id' => $orders_id]);
        $html = '';
        $total_orders = 0;
        $total = 0;

        $html .= '<div class="card">
                        <div class="card-header">
                            <h4 class="text-center">' . $this->input->post('orders_number') . '<br><small>' . $this->input->post('orders_name') . '</samll></h4>
                        </div>
                        <div class="card-body">
                            <div class="invoice-detail">
                                <div class="invoice-item" style="max-height: 200px;overflow-y: auto;">
                                <table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
                                <thead>
                                    <tr>
                                        <td style="font-size:14px"><strong>Menu</strong></td>
                                        <td class="text-center col-xs-1" style="font-size:14px"><strong>Jumlah</strong></td>
                                    </tr>
                                </thead>';
        foreach ($order_data->result() as $row) {
            $this->db->where('orders_id', $orders_id);
            $this->db->where('menu_sku', $row->menu_sku);
            $total = $this->db->get('orders_detail');
            $total_orders = $total_orders + $row->orderdetail_quantity;
            $html .= '<tbody>
                                    <tr style="height:8px">
                                        <td style="font-size:12px">' . $row->menu_name . '</td>
                                        <td class="text-center col-xs-1" style="font-size:12px">' . $total->num_rows() . '</td>
                                    </tr>';
        }
        $html .= '</tbody>
                            </table>    
                                </div>
                            </div>
                            <br>
                            <a class="btn btn-primary btn-block" href="' . base_url('order/order_detail/') . encrypt_url($orders_id) . '">Buat Pesanan (' . $total_orders . ')</a>
                            <a href="' . base_url('order/delete_orderdata/') . $this->input->post('orders_number') . '" class="btn btn-danger btn-block">Hapus Pesanan</a>
                        </div>
                    </div>';


        echo json_encode([
            'status'  => 'success',
            'message' => 'Ubah data berhasil',
            'orders_number' => $this->input->post('orders_number'),
            'html' => $html,
        ]);
    }

    function delete_orderdata($orders_number)
    {
        $this->db->where('orders_number', $orders_number);
        $this->db->delete('orders');

        $this->db->where('orders_number', $orders_number);
        $this->db->delete('orders_detail');


        $this->session->set_flashdata('success-delete', 'Pesanan ' . $orders_number . ' berhasil dihapus');
        redirect(site_url('order'), 'refresh');
    }

    function orderdetail()
    {
        $orderdetail    = $this->order_model->get_orderdetail($this->input->post('orders_id'));
        $quantity       = $this->order_model->get_orderdetailqty($this->input->post('orders_id'));
        $html = '';
        $html .= '<div class="col-md-12">
        <div class="card card-plain">
            <div class="card-body">
                <h3 class="card-title"><?= $page_title ?></h3>
                <br />
                <div class="table-responsive">
                    <table class="table table-shopping">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Total</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>';

        $sum_total = 0;
        $sum_quantity = 0;
        foreach ($orderdetail->result() as $row) {
            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->where('menu_sku', $row->menu_sku);
            $qty = $this->db->get('orders_detail');

            if ($qty->num_rows() > 0) {
                $orders_quantity    = $qty->num_rows();
                $sum_quantity       += $qty->num_rows();
                $orderdetail_price  = $row->orderdetail_price * $orders_quantity;
                $sum_total          += $orderdetail_price;
            } else {
                $orders_quantity    = 0;
                $sum_quantity       += $orders_quantity;
                $orderdetail_price  = 0;
                $sum_total          += $orderdetail_price;
            }

            $menu = $this->db->get_where('menu', ['menu_sku' => $row->menu_sku])->row();
            if ($orders_quantity == 1) {
                $minus = '';
            } else {
                $minus = '
                <button type="button" class="btn btn-link" onclick="minus(' . $row->orderdetail_id . ')">
                    <i class="fas fa-minus"></i>
                </button>';
            }
            $html .= '<tr>
                                    <td class="td-name">
                                        <b>' . $menu->menu_name . '</b><br>&emsp;&emsp;<small>' . $row->orderdetail_variant . '</small>
                                        <br />
                                        <small>
                                        <div class="input-icon mt-2 mb-2">
                                            <input type="text" id="orderdetail_note[' . $row->orderdetail_id . ']" name="orderdetail_note[' . $row->orderdetail_id . ']" class="form-control form-control-sm" placeholder="Note" value="' . $row->orderdetail_note . '" >
                                            <span class="input-icon-addon" onclick="orderdetail_updatenote(' . $row->orderdetail_id . ')">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                        </small>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($row->orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-number text-center">
                                        ' . $minus . '
                                        ' . $orders_quantity . '
                                        <button type="button" class="btn btn-link" onclick="plus(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-actions text-center">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link" onclick="hapus_list(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-trash" style="color:red"></i>
                                        </button>
                                    </td>
                                </tr>';
        }
        $html .= '<tr>
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="td-total text-center">
                                    ' . number_format($sum_quantity, 0) . '
                                </td>
                                <td colspan="1" class="td-price text-center">
                                    <small>Rp.' . number_format($sum_total, 0) . '
                                </td>
                                <td class="text-center">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>';

        echo json_encode([
            'html' => $html,
            'total' => number_format($sum_total, 0),
            'subtotal' => number_format($sum_total, 0),
            'quantity' => $quantity,
        ]);
    }

    function ordertotal()
    {
        $orderdetail = $this->order_model->get_orderdetail($this->input->post('orders_number'));
        $sum_total = 0;
        $sum_quantity = 0;
        foreach ($orderdetail->result() as $row) {
            $sum_total += $row->orderdetail_total;
            $sum_quantity += $row->orderdetail_quantity;
        }
        $html = '';
        $html .= number_format($sum_total, 0);

        echo json_encode([
            'html' => $html,
            'total' => number_format($sum_total, 0),
            'qty'   => $sum_quantity,
        ]);
    }

    function orderpayment()
    {
        $orderdetail = $this->order_model->get_orderdetail($this->input->post('orders_number'));
        $subtotal = 0;
        $sum_quantity = 0;
        foreach ($orderdetail->result() as $row) {
            $subtotal += $row->orderdetail_total;
            $sum_quantity += $row->orderdetail_quantity;
        }
        $payment_cash = preg_replace('/[^0-9]/', '', $this->input->post('payment_cash'));

        if ($payment_cash >= $subtotal) {
            $kembalian =  $payment_cash - $subtotal;
            $nominal_kembalian = number_format($kembalian, 0);
            $status = 1;
        } else {
            $nominal_kembalian = '';
            $status = 0;
        }

        echo json_encode([
            'quantity' => $sum_quantity,
            'bayar' => number_format($payment_cash, 0),
            'kembalian' => $nominal_kembalian,
            'subtotal' => number_format($subtotal, 0),
            'status' => $status,
        ]);
    }

    function orderdetail_plus()
    {

        $order_plus = $this->db->get_where('orders_detail', ['orderdetail_id' => $this->input->post('orderdetail_id')]);

        $data['orders_id']              = $order_plus->row()->orders_id;
        $data['orders_number']          = $order_plus->row()->orders_number;
        $data['menu_sku']               = $order_plus->row()->menu_sku;
        $data['menu_name']              = $order_plus->row()->menu_name;
        $data['orderdetail_quantity']   = 1;
        $data['orderdetail_price']      = $order_plus->row()->orderdetail_price;
        $data['orderdetail_total']      = $order_plus->row()->orderdetail_total;
        $data['orderdetail_variant']    = $order_plus->row()->orderdetail_variant;
        $data['orderdetail_note']       = $order_plus->row()->orderdetail_note;

        $this->db->insert('orders_detail', $data);

        $orderdetail    = $this->order_model->get_orderdetail($order_plus->row()->orders_id);
        $quantity       = $this->order_model->get_orderdetailqty($this->input->post('orders_id'));
        $html = '';
        $html .= '<div class="col-md-12">
        <div class="card card-plain">
            <div class="card-body">
                <h3 class="card-title"><?= $page_title ?></h3>
                <br />
                <div class="table-responsive">
                    <table class="table table-shopping">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Total</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>';

        $sum_total = 0;
        $sum_quantity = 0;
        foreach ($orderdetail->result() as $row) {
            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->where('menu_sku', $row->menu_sku);
            $qty = $this->db->get('orders_detail');

            if ($qty->num_rows() > 0) {
                $orders_quantity    = $qty->num_rows();
                $sum_quantity       += $qty->num_rows();
                $orderdetail_price  = $row->orderdetail_price * $orders_quantity;
                $sum_total          += $orderdetail_price;
            } else {
                $orders_quantity    = 0;
                $sum_quantity       += $orders_quantity;
                $orderdetail_price  = 0;
                $sum_total          += $orderdetail_price;
            }

            $menu = $this->db->get_where('menu', ['menu_sku' => $row->menu_sku])->row();
            if ($orders_quantity == 1) {
                $minus = '';
            } else {
                $minus = '
                <button type="button" class="btn btn-link" onclick="minus(' . $row->orderdetail_id . ')">
                    <i class="fas fa-minus"></i>
                </button>';
            }
            $html .= '<tr>
                                    <td class="td-name">
                                        <b>' . $menu->menu_name . '</b><br>&emsp;&emsp;<small>' . $row->orderdetail_variant . '</small>
                                        <br />
                                        <small>
                                        <div class="input-icon mt-2 mb-2">
                                            <input type="text" id="orderdetail_note[' . $row->orderdetail_id . ']" name="orderdetail_note[' . $row->orderdetail_id . ']" class="form-control form-control-sm" placeholder="Note" value="' . $row->orderdetail_note . '" >
                                            <span class="input-icon-addon" onclick="orderdetail_updatenote(' . $row->orderdetail_id . ')">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                        </small>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($row->orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-number text-center">
                                        ' . $minus . '
                                        ' . $orders_quantity . '
                                        <button type="button" class="btn btn-link" onclick="plus(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-actions text-center">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link" onclick="hapus_list(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-trash" style="color:red"></i>
                                        </button>
                                    </td>
                                </tr>';
        }
        $html .= '<tr>
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="td-total text-center">
                                    ' . number_format($sum_quantity, 0) . '
                                </td>
                                <td colspan="1" class="td-price text-center">
                                    <small>Rp.' . number_format($sum_total, 0) . '
                                </td>
                                <td class="text-center">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>';
        echo json_encode([
            'html' => $html,
            'total' => number_format($sum_total, 0),
            'quantity' => $quantity,
        ]);
    }

    function orderdetail_min()
    {

        $this->db->where('orderdetail_id', $this->input->post('orderdetail_id'));
        $this->db->delete('orders_detail');

        $orderdetail    = $this->order_model->get_orderdetail($this->input->post('orders_id'));
        $quantity       = $this->order_model->get_orderdetailqty($this->input->post('orders_id'));
        $html = '';
        $html .= '<div class="col-md-12">
        <div class="card card-plain">
            <div class="card-body">
                <h3 class="card-title"><?= $page_title ?></h3>
                <br />
                <div class="table-responsive">
                    <table class="table table-shopping">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Total</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>';

        $sum_total = 0;
        $sum_quantity = 0;
        foreach ($orderdetail->result() as $row) {
            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->where('menu_sku', $row->menu_sku);
            $qty = $this->db->get('orders_detail');

            if ($qty->num_rows() > 0) {
                $orders_quantity    = $qty->num_rows();
                $sum_quantity       += $qty->num_rows();
                $orderdetail_price  = $row->orderdetail_price * $orders_quantity;
                $sum_total          += $orderdetail_price;
            } else {
                $orders_quantity    = 0;
                $sum_quantity       += $orders_quantity;
                $orderdetail_price  = 0;
                $sum_total          += $orderdetail_price;
            }

            $menu = $this->db->get_where('menu', ['menu_sku' => $row->menu_sku])->row();
            if ($orders_quantity == 1) {
                $minus = '';
            } else {
                $minus = '
                <button type="button" class="btn btn-link" onclick="minus(' . $row->orderdetail_id . ')">
                    <i class="fas fa-minus"></i>
                </button>';
            }
            $html .= '<tr>
                                    <td class="td-name">
                                        <b>' . $menu->menu_name . '</b><br>&emsp;&emsp;<small>' . $row->orderdetail_variant . '</small>
                                        <br />
                                        <small>
                                        <div class="input-icon mt-2 mb-2">
                                            <input type="text" id="orderdetail_note[' . $row->orderdetail_id . ']" name="orderdetail_note[' . $row->orderdetail_id . ']" class="form-control form-control-sm" placeholder="Note" value="' . $row->orderdetail_note . '" >
                                            <span class="input-icon-addon" onclick="orderdetail_updatenote(' . $row->orderdetail_id . ')">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                        </small>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($row->orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-number text-center">
                                        ' . $minus . '
                                        ' . $orders_quantity . '
                                        <button type="button" class="btn btn-link" onclick="plus(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-actions text-center">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link" onclick="hapus_list(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-trash" style="color:red"></i>
                                        </button>
                                    </td>
                                </tr>';
        }
        $html .= '<tr>
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="td-total text-center">
                                    ' . number_format($sum_quantity, 0) . '
                                </td>
                                <td colspan="1" class="td-price text-center">
                                    <small>Rp.' . number_format($sum_total, 0) . '
                                </td>
                                <td class="text-center">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>';
        echo json_encode([
            'html' => $html,
            'total' => number_format($sum_total, 0),
            'quantity' => $quantity,
        ]);
    }

    public function modal_variant()
    {
        $html = '';
        $variant = $this->db->get_where('variant', ['menu_id' => $this->input->post('menu_id')]);

        $html .= '<div class="col-md-12">
                <h3 class="card-title"><?= $page_title ?></h3>
                <br />
                <div class="table-responsive">
                    <table class="table table-shopping">
                        <thead>
                            <tr>
                                <th class="text-center">Varian</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>';
        $i = 0;
        foreach ($variant->result() as $row) {
            $html .= '<input type="hidden" name="sequence[]" value="' . $i++ . '">';
            $html .= '<input type="hidden" name="variant_name[]" value="' . $row->variant_name . '">';
            $html .= '<tr>
                        <td class="td-name">
                        ' . $row->variant_name . '
                        </td>
                        <td>
                            <div class="form-group">
                                <select class="form-control" name="menu_quantity[]" data-live-search="true" data-size="10">
                                    <option value="" selected>-</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                        </td>
                    </tr>';
        }

        $html .= '</tbody>
                    </table>
                </div>
            </div>';
        $html .= '<input type="hidden" name="menu_id" value="' . $this->input->post('menu_id') . '">';
        $html .= '<input type="hidden" name="orders_number" value="' . $this->input->post('orders_number') . '">';


        echo $html;
    }

    function confirm_payment()
    {
        $this->db->select('SUM(orderdetail_total) as SUBTOTAL, orders_number');
        $this->db->where('orders_number', $this->input->post('orders_number'));
        $subtotal = $this->db->get('orders_detail');

        $this->db->where('orders_number', $this->input->post('orders_number'));
        $orders_quantity = $this->db->get('orders_detail')->num_rows();



        $data['orders_name']        = $this->input->post('orders_name');
        $data['orders_phone']       = $this->input->post('orders_phone');
        $data['orders_paymentnow']  = $this->input->post('orders_paymentnow');
        $data['orders_type']        = $this->input->post('orders_type');
        $data['orders_payment']     = $this->input->post('orders_payment');
        $data['orders_quantity']    = $orders_quantity;
        $data['orders_cash']        = preg_replace('/[^0-9]/', '', $this->input->post('payment_cash'));
        $data['orders_subtotal']    = $subtotal->row()->SUBTOTAL;
        $data['orders_change']      = preg_replace('/[^0-9]/', '', $this->input->post('payment_cash')) - $subtotal->row()->SUBTOTAL;
        $data['orders_status']      = 'DAPUR';
        // $data['orders_id']          = $this->input->post('orders_id');

        $this->db->where('orders_id', $this->input->post('orders_id'));
        $this->db->update('orders', $data);

        echo "<pre>";
        echo print_r($data);
        echo "</pre>";
    }

    function orderdetail_updatenote()
    {
        $orders_detail = $this->db->get_where('orders_detail', ['orderdetail_id' => $this->input->post('id')]);
        $this->db->where('orders_id', $orders_detail->row()->orders_id);
        $this->db->where('menu_sku', $orders_detail->row()->menu_sku);
        $this->db->where('orderdetail_variant', $orders_detail->row()->orderdetail_variant);
        $check = $this->db->get('orders_detail');

        $data['orderdetail_note'] = $this->input->post('note');
        if ($check->num_rows() > 0) {
            $this->db->where('orders_id', $orders_detail->row()->orders_id);
            $this->db->where('menu_sku', $orders_detail->row()->menu_sku);
            $this->db->where('orderdetail_variant', $orders_detail->row()->orderdetail_variant);
        } else {
            $this->db->where('orderdetail_id', $this->input->post('id'));
        }

        $this->db->update('orders_detail', $data);

        echo json_encode([
            'id' => $this->input->post('id'),
            'note' => $this->input->post('note'),
            'message' => 'Note berhasil diubah menjadi "' . $this->input->post('note') . '"',
        ]);
    }

    function orderdetail_delete()
    {
        $this->db->where('orderdetail_id', $this->input->post('orderdetail_id'));
        $menu_sku_del = $this->db->get('orders_detail')->row()->menu_sku;

        $this->db->where('orders_id', $this->input->post('orders_id'));
        $this->db->where('menu_sku', $menu_sku_del);
        $this->db->delete('orders_detail');

        $orderdetail    = $this->order_model->get_orderdetail($this->input->post('orders_id'));
        $quantity       = $this->order_model->get_orderdetailqty($this->input->post('orders_id'));
        $html = '';
        $html .= '<div class="col-md-12">
        <div class="card card-plain">
            <div class="card-body">
                <h3 class="card-title"><?= $page_title ?></h3>
                <br />
                <div class="table-responsive">
                    <table class="table table-shopping">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Total</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>';

        $sum_total = 0;
        $sum_quantity = 0;
        foreach ($orderdetail->result() as $row) {
            $this->db->where('orders_id', $this->input->post('orders_id'));
            $this->db->where('menu_sku', $row->menu_sku);
            $qty = $this->db->get('orders_detail');

            if ($qty->num_rows() > 0) {
                $orders_quantity    = $qty->num_rows();
                $sum_quantity       += $qty->num_rows();
                $orderdetail_price  = $row->orderdetail_price * $orders_quantity;
                $sum_total          += $orderdetail_price;
            } else {
                $orders_quantity    = 0;
                $sum_quantity       += $orders_quantity;
                $orderdetail_price  = 0;
                $sum_total          += $orderdetail_price;
            }

            $menu = $this->db->get_where('menu', ['menu_sku' => $row->menu_sku])->row();
            if ($orders_quantity == 1) {
                $minus = '';
            } else {
                $minus = '
                <button type="button" class="btn btn-link" onclick="minus(' . $row->orderdetail_id . ')">
                    <i class="fas fa-minus"></i>
                </button>';
            }
            $html .= '<tr>
                                    <td class="td-name">
                                        <b>' . $menu->menu_name . '</b><br>&emsp;&emsp;<small>' . $row->orderdetail_variant . '</small>
                                        <br />
                                        <small>
                                        <div class="input-icon mt-2 mb-2">
                                            <input type="text" id="orderdetail_note[' . $row->orderdetail_id . ']" name="orderdetail_note[' . $row->orderdetail_id . ']" class="form-control form-control-sm" placeholder="Note" value="' . $row->orderdetail_note . '" >
                                            <span class="input-icon-addon" onclick="orderdetail_updatenote(' . $row->orderdetail_id . ')">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                        </small>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($row->orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-number text-center">
                                        ' . $minus . '
                                        ' . $orders_quantity . '
                                        <button type="button" class="btn btn-link" onclick="plus(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <td class="td-number text-center">
                                        Rp.' . number_format($orderdetail_price, 0) . '
                                    </td>
                                    <td class="td-actions text-center">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link" onclick="hapus_list(' . $row->orderdetail_id . ')">
                                            <i class="fas fa-trash" style="color:red"></i>
                                        </button>
                                    </td>
                                </tr>';
        }
        $html .= '<tr>
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="td-total text-center">
                                    ' . number_format($sum_quantity, 0) . '
                                </td>
                                <td colspan="1" class="td-price text-center">
                                    <small>Rp.' . number_format($sum_total, 0) . '
                                </td>
                                <td class="text-center">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>';
        echo json_encode([
            'html' => $html,
            'total' => number_format($sum_total, 0),
            'quantity' => $quantity,
        ]);
    }
}
