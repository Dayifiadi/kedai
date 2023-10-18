<?php
class MOsp extends CI_Model
{
    private $table_order_sheet_ritase                     = "order_sheet_ritase";
    private $order_sheet_pickup                           = "order_sheet_pickup";
    private $order_sheet_pickup_detail                    = "order_sheet_pickup_detail";
    private $table_employee                               = "employee";
    private $table_stop_type                              = "stop_type";
    private $areakerja                                    = "areakerja";


    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->dbMyAsset = $this->load->database('dbMyAsset', TRUE);
        $this->load->model('Msetting');
    }


    public function getRitase($option = null)
    {
        $select = "";

        $keyword              = $this->input->post('search');
        $branch  = $this->input->post('branch');
        $origin  = $this->input->post('origin');


        $this->db->select($this->table_order_sheet_ritase . ".order_sheet_ritase_id as order_sheet_ritase_type");
        $this->db->select($this->table_order_sheet_ritase . ".order_sheet_ritase_id");
        $this->db->select($this->table_order_sheet_ritase . ".order_sheet_name");


        if (trim($keyword) != "") {
            $this->db->like('order_sheet_name', $keyword);
        }
        if (array_key_exists('order', $option)) {
            $this->db->order_by($this->table_order_sheet_ritase . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->db->limit($this->table_order_sheet_ritase . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->db->order_by($this->table_order_sheet_ritase . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->db->limit($option['limit'], $page);
            }
        }
        return $this->db->get($this->table_order_sheet_ritase)->result();
    }


    public function findHs($nik, $result_type, $option = null)
    {
        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();
        $subunit_code = $dataNa->subunit_code;
        $select = "";
        $all  = $this->input->post('all');
        $branch  = $this->input->post('branch');
        $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
        $ADMIN  = $this->input->post('ADMIN');
        $KOORDINATOR  = $this->input->post('KOORDINATOR');
        $karyawanNik  = $this->input->post('karyawanNik');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->db->select($this->order_sheet_pickup . ".order_sheet_pickup_date_created");
        $this->db->select($this->order_sheet_pickup . ".order_sheet_pickup_id");
        $this->db->select($this->order_sheet_pickup . ".order_sheet_ritase_type");
        $this->db->select($this->table_stop_type . ".stop_type_name");
        $this->db->select($this->table_stop_type . ".stop_type_code");
        $this->db->select($this->table_employee . ".nik");
        $this->db->select($this->table_employee . ".employee_name");

        $this->db->order_by($this->order_sheet_pickup . ".order_sheet_pickup_id", 'DESC');
        $this->db->join($this->table_employee, $this->order_sheet_pickup . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->join($this->table_stop_type, $this->order_sheet_pickup . '.order_sheet_pickup_status = ' . $this->table_stop_type   . '.stop_type_code');
        $this->db->join($this->order_sheet_pickup_detail, $this->order_sheet_pickup . '.order_sheet_pickup_id = ' . $this->order_sheet_pickup_detail   . '.order_sheet_pickup_id');
        $this->db->group_by($this->order_sheet_pickup . ".order_sheet_pickup_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(order_sheet_pickup.order_sheet_pickup_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->db->where($this->order_sheet_pickup . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->db->where($this->order_sheet_pickup . '.nik', $karyawanNik);
        }

        if ($all == 2) {
            $ids = array('STCST');
            $this->db->where_in($this->order_sheet_pickup . '.order_sheet_pickup_status', $ids);
        } elseif ($all == 1) {
            $ids = array('STCS', 'TRST');
            $this->db->where_in($this->order_sheet_pickup . '.order_sheet_pickup_status', $ids);
        }

        if (array_key_exists('order', $option)) {
            $this->db->order_by($this->order_sheet_pickup . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->db->limit($this->order_sheet_pickup . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->db->order_by($this->order_sheet_pickup . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->db->limit($option['limit'], $page);
            }
        }



        $data_product = $this->db->get($this->order_sheet_pickup)->result();

        $array_data   = array();
        foreach ($data_product as $product) {
            $data_bar   = $this->getJumlahBarang($product->order_sheet_pickup_id);
            $data_hvs   = $this->getJumlahBarangHVS($product->order_sheet_pickup_id);
            $data_non   = $this->getJumlahBarangNon($product->order_sheet_pickup_id);
            $data_visit   = $this->countVist($product->order_sheet_pickup_id);


            $new_array  = array(
                'data' => $product,
                'data_bar' => $data_bar,
                'data_hvs' => $data_hvs,
                'data_non' => $data_non,
                'data_visit' => $data_visit,
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }



    public function findExp($nik)
    {
        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();
        $subunit_code = $dataNa->subunit_code;
        $all  = $this->input->post('all');
        $branch  = $this->input->post('branch');
        $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
        $ADMIN  = $this->input->post('ADMIN');
        $KOORDINATOR  = $this->input->post('KOORDINATOR');
        $karyawanNik  = $this->input->post('karyawanNik');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->db->select($this->order_sheet_pickup . ".order_sheet_pickup_date_created");
        $this->db->select($this->order_sheet_pickup . ".order_sheet_pickup_id");
        $this->db->select($this->order_sheet_pickup . ".order_sheet_ritase_type");
        $this->db->select($this->table_stop_type . ".stop_type_name");
        $this->db->select($this->table_stop_type . ".stop_type_code");
        $this->db->select($this->table_employee . ".nik");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->order_sheet_pickup_detail . ".*");
        $this->db->select($this->table_order_sheet_ritase . ".*");
        $this->db->select($this->areakerja . ".*");

        $this->db->order_by($this->order_sheet_pickup . ".order_sheet_pickup_id", 'DESC');
        $this->db->join($this->table_employee, $this->order_sheet_pickup . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->join($this->table_stop_type, $this->order_sheet_pickup . '.order_sheet_pickup_status = ' . $this->table_stop_type   . '.stop_type_code');
        $this->db->join($this->order_sheet_pickup_detail, $this->order_sheet_pickup . '.order_sheet_pickup_id = ' . $this->order_sheet_pickup_detail   . '.order_sheet_pickup_id');
        $this->db->join($this->table_order_sheet_ritase, $this->order_sheet_pickup_detail . '.order_sheet_pickup_detail_id_ritase = ' . $this->table_order_sheet_ritase   . '.order_sheet_ritase_id', 'left');
        $this->db->join($this->areakerja, $this->order_sheet_pickup_detail . '.area_kerja = ' . $this->areakerja   . '.areakerja_id', 'left');
        // $this->db->group_by($this->order_sheet_pickup . ".order_sheet_pickup_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(order_sheet_pickup.order_sheet_pickup_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->db->where($this->order_sheet_pickup . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->db->where($this->order_sheet_pickup . '.nik', $karyawanNik);
        }

        if ($all == 2) {
            $ids = array('STCST');
            $this->db->where_in($this->order_sheet_pickup . '.order_sheet_pickup_status', $ids);
        } elseif ($all == 1) {
            $ids = array('STCS', 'TRST');
            $this->db->where_in($this->order_sheet_pickup . '.order_sheet_pickup_status', $ids);
        }


        $data_product = $this->db->get($this->order_sheet_pickup)->result();

        $array_data   = array();
        foreach ($data_product as $product) {
            $data_bar   = $this->getJumlahBarang($product->order_sheet_pickup_id);

            $new_array  = array(
                'data' => $product,
                'data_bar' => $data_bar,
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }


    public function findDetailHs()
    {
        $order_sheet_pickup_id  = $this->input->post('order_sheet_pickup_id');

        $this->db->select($this->order_sheet_pickup . ".order_sheet_pickup_date_created");
        $this->db->select($this->order_sheet_pickup . ".order_sheet_pickup_id");
        $this->db->select($this->table_stop_type . ".stop_type_name");
        $this->db->select($this->table_stop_type . ".stop_type_code");
        $this->db->select($this->table_employee . ".nik");
        $this->db->select($this->table_employee . ".employee_name");

        $this->db->order_by($this->order_sheet_pickup . ".order_sheet_pickup_id", 'DESC');
        $this->db->join($this->table_employee, $this->order_sheet_pickup . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->join($this->table_stop_type, $this->order_sheet_pickup . '.order_sheet_pickup_status = ' . $this->table_stop_type   . '.stop_type_code');
        $this->db->join($this->order_sheet_pickup_detail, $this->order_sheet_pickup . '.order_sheet_pickup_id = ' . $this->order_sheet_pickup_detail   . '.order_sheet_pickup_id');
        $this->db->group_by($this->order_sheet_pickup . ".order_sheet_pickup_id");

        $this->db->where($this->order_sheet_pickup . '.order_sheet_pickup_id', $order_sheet_pickup_id);


        $product = $this->db->get($this->order_sheet_pickup)->row();

        $array_data   = array();
        $data_bar   = $this->getJumlahBarang($order_sheet_pickup_id);
        $data_hvs   = $this->getJumlahBarangHVS($order_sheet_pickup_id);
        $data_non   = $this->getJumlahBarangNon($order_sheet_pickup_id);
        $data_visit   = $this->countVist($order_sheet_pickup_id);


        $new_array  = array(
            'data' => $product,
            'data_bar' => $data_bar,
            'data_hvs' => $data_hvs,
            'data_non' => $data_non,
            'data_visit' => $data_visit,
        );

        array_push($array_data, $new_array);
        return $array_data;
    }
    public function getJumlahBarangHVS($order_sheet_pickup_id)
    {
        $select = "";

        $this->db->select("SUM(order_sheet_pickup_detail_jumlah_hvs) as total_barang");
        $this->db->where($this->order_sheet_pickup_detail . '.order_sheet_pickup_id', $order_sheet_pickup_id);

        return $this->db->get($this->order_sheet_pickup_detail)->row();
    }
    public function getJumlahBarangNon($order_sheet_pickup_id)
    {
        $select = "";

        $this->db->select("SUM(order_sheet_pickup_detail_jumlah_total) as total_barang");
        $this->db->where($this->order_sheet_pickup_detail . '.order_sheet_pickup_id', $order_sheet_pickup_id);

        return $this->db->get($this->order_sheet_pickup_detail)->row();
    }
    public function countVist($order_sheet_pickup_id)
    {
        $select = "";

        $ignore = array('STCST');
        $this->db->where_not_in($this->order_sheet_pickup_detail . '.order_sheet_pickup_detail_status', $ignore);
        $this->db->where($this->order_sheet_pickup_detail . '.order_sheet_pickup_id', $order_sheet_pickup_id);

        return $this->db->get($this->order_sheet_pickup_detail)->num_rows();
    }



    public function findList()
    {
        $order_sheet_pickup_id  = $this->input->post('order_sheet_pickup_id');

        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_created_date");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_status");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_id_ritase");
        $this->db->select($this->areakerja . ".areakerja_name");
        $this->db->select($this->order_sheet_pickup_detail . ".nama_agen");
        $this->db->select($this->order_sheet_pickup_detail . ".nama_agen as order_sheet_name");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_foto");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_jumlah");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_jumlah_hvs");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_jumlah_total");
        $this->db->select($this->order_sheet_pickup_detail . ".latitude");
        $this->db->select($this->order_sheet_pickup_detail . ".longitude");
        $this->db->select($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_keterangan");
        $this->db->select($this->order_sheet_pickup . ".order_sheet_ritase_type");
        $this->db->select($this->table_order_sheet_ritase . ".order_sheet_ritase_id");

        $this->db->order_by($this->order_sheet_pickup_detail . ".order_sheet_pickup_detail_id", 'ASC');
        $this->db->join($this->order_sheet_pickup, $this->order_sheet_pickup_detail . '.order_sheet_pickup_id = ' . $this->order_sheet_pickup   . '.order_sheet_pickup_id');
        $this->db->join($this->table_order_sheet_ritase, $this->order_sheet_pickup_detail . '.order_sheet_pickup_detail_id_ritase = ' . $this->table_order_sheet_ritase   . '.order_sheet_ritase_id', 'left');
        $this->db->join($this->areakerja, $this->order_sheet_pickup_detail . '.area_kerja = ' . $this->areakerja   . '.areakerja_id', 'left');
        $this->db->where($this->order_sheet_pickup_detail . '.order_sheet_pickup_id', $order_sheet_pickup_id);


        return $data_product = $this->db->get($this->order_sheet_pickup_detail)->result();
    }

    public function findSt($nik, $result_type, $option = null)
    {
        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();
        $subunit_code = $dataNa->subunit_code;
        $select = "";
        $all  = $this->input->post('all');
        $branch  = $this->input->post('branch');
        $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
        $ADMIN  = $this->input->post('ADMIN');
        $KOORDINATOR  = $this->input->post('KOORDINATOR');
        $karyawanNik  = $this->input->post('karyawanNik');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->db->select($this->table_employee . ".employee_name");
        $this->db->order_by($this->order_sheet_pickup . ".order_sheet_pickup_id", 'DESC');
        $this->db->join($this->table_employee, $this->order_sheet_pickup . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->join($this->table_stop_type, $this->order_sheet_pickup . '.order_sheet_pickup_status = ' . $this->table_stop_type   . '.stop_type_code');
        $this->db->join($this->order_sheet_pickup_detail, $this->order_sheet_pickup . '.order_sheet_pickup_id = ' . $this->order_sheet_pickup_detail   . '.order_sheet_pickup_id');
        $this->db->group_by($this->order_sheet_pickup . ".order_sheet_pickup_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(order_sheet_pickup.order_sheet_pickup_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->db->where($this->order_sheet_pickup . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->db->where($this->order_sheet_pickup . '.nik', $karyawanNik);
        }

        $ids = array('STCS', 'TRST');
        $this->db->where_in($this->order_sheet_pickup . '.order_sheet_pickup_status', $ids);





        return $data_product = $this->db->get($this->order_sheet_pickup)->num_rows();
    }




    public function find_not_fin($nik, $result_type, $option = null)
    {
        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();
        $subunit_code = $dataNa->subunit_code;
        $select = "";
        $all  = $this->input->post('all');
        $branch  = $this->input->post('branch');
        $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
        $ADMIN  = $this->input->post('ADMIN');
        $KOORDINATOR  = $this->input->post('KOORDINATOR');
        $karyawanNik  = $this->input->post('karyawanNik');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->db->select($this->table_employee . ".employee_name");
        $this->db->order_by($this->order_sheet_pickup . ".order_sheet_pickup_id", 'DESC');
        $this->db->join($this->table_employee, $this->order_sheet_pickup . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->join($this->table_stop_type, $this->order_sheet_pickup . '.order_sheet_pickup_status = ' . $this->table_stop_type   . '.stop_type_code');
        $this->db->join($this->order_sheet_pickup_detail, $this->order_sheet_pickup . '.order_sheet_pickup_id = ' . $this->order_sheet_pickup_detail   . '.order_sheet_pickup_id');
        $this->db->group_by($this->order_sheet_pickup . ".order_sheet_pickup_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(order_sheet_pickup.order_sheet_pickup_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->order_sheet_pickup . '.order_sheet_pickup_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->db->where($this->table_employee . '.branch_code', $branch);
            $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->db->where($this->order_sheet_pickup . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->db->where($this->order_sheet_pickup . '.nik', $karyawanNik);
        }

        $ids = array('STCST');
        $this->db->where_in($this->order_sheet_pickup . '.order_sheet_pickup_status', $ids);


        return $data_product = $this->db->get($this->order_sheet_pickup)->num_rows();
    }

    public function getJumlahBarang($order_sheet_pickup_id)
    {
        $select = "";

        $this->db->select("SUM(order_sheet_pickup_detail_jumlah) as total_barang");
        $this->db->where($this->order_sheet_pickup_detail . '.order_sheet_pickup_id', $order_sheet_pickup_id);

        return $this->db->get($this->order_sheet_pickup_detail)->row();
    }
}