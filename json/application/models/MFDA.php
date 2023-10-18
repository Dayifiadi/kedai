<?php
class MFDA extends CI_Model
{
    private $table_fda_category   = "fda_category";
    private $table_fda_request    = "fda_request";
    private $table_employee       = "employee";
    private $table_employee_app   = "e";

    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->load->model('Msetting');
    }


    public function findCategory()
    {
        $select = "";


        $this->db->order_by($this->table_fda_category . ".fda_category_name", 'ASC');
        $this->db->where($this->table_fda_category . '.fda_category_reff =', NULL);

        return $this->db->get($this->table_fda_category)->result();
    }


    public function findCategoryDetail()
    {
        $select = "";


        $this->db->order_by($this->table_fda_category . ".fda_category_name", 'ASC');
        $this->db->where($this->table_fda_category . '.fda_category_reff', $this->input->post('idNA'));

        return $this->db->get($this->table_fda_category)->result();
    }


    public function checkFDA()
    {
        $select = "";
        $tanggal   = $this->input->post('tanggal');
        $nik   = $this->input->post('nik');

        $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggal);
        $this->db->where($this->table_fda_request . '.fda_request_nik', $nik);
        $ids = array('Open', 'Approved');
        $this->db->where_in($this->table_fda_request . '.fda_request_status', $ids);

        return $this->db->get($this->table_fda_request)->num_rows();
    }

    public function totalFDA($STATUS)
    {
        $select = "";
        $tanggal   = $this->input->post('tanggal');
        $nik   = $this->input->post('nik');

        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->db->where($this->table_fda_request . '.fda_request_nik', $nik);
        if ($STATUS != "ALL") {
            $this->db->where($this->table_fda_request . '.fda_request_status', $STATUS);
        }

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(fda_request.fda_request_date) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggalSearchAkhir);
        }

        return $this->db->get($this->table_fda_request)->num_rows();
    }


    public function findHs($nik, $result_type, $option = null)
    {

        $select = "";
        $status  = $this->input->post('status');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');


        $this->db->select($this->table_fda_request . ".fda_request_id");
        $this->db->select($this->table_fda_request . ".fda_request_nik");
        $this->db->select($this->table_fda_request . ".fda_request_nik_approval");
        $this->db->select($this->table_fda_request . ".fda_request_created_date");
        $this->db->select($this->table_fda_request . ".fda_request_approval_date");
        $this->db->select($this->table_fda_request . ".fda_request_date");
        $this->db->select($this->table_fda_request . ".fda_request_image");
        $this->db->select($this->table_fda_request . ".fda_request_desciprtian");
        $this->db->select($this->table_fda_request . ".fda_request_status");
        $this->db->select($this->table_fda_request . ".fda_category_id");
        $this->db->select($this->table_fda_category . ".fda_category_name");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table_employee_app . ".employee_name as approval_name");
        $this->db->order_by($this->table_fda_request . ".fda_request_id", 'DESC');
        $this->db->join($this->table_employee, $this->table_fda_request . '.fda_request_nik = ' . $this->table_employee   . '.nik', 'left');
        $this->db->join('employee e', $this->table_fda_request . '.fda_request_nik_approval = e.nik', 'left');
        $this->db->join($this->table_fda_category, $this->table_fda_request . '.fda_category_id = ' . $this->table_fda_category   . '.fda_category_id', 'left');
        $this->db->group_by($this->table_fda_request . ".fda_request_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(fda_request.fda_request_date) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggalSearchAkhir);
        }

        $this->db->where($this->table_fda_request . '.fda_request_nik', $nik);

        if ($status != "") {
            if ($status != "ALL") {
                $this->db->where($this->table_fda_request . '.fda_request_status', $status);
            }
        }


        if (array_key_exists('order', $option)) {
            $this->db->order_by($this->table_fda_request . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->db->limit($this->table_fda_request . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->db->order_by($this->table_fda_request . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->db->limit($option['limit'], $page);
            }
        }



        $data_product = $this->db->get($this->table_fda_request)->result();

        $array_data   = array();
        foreach ($data_product as $product) {


            $new_array  = array(
                'data' => $product,
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }



    public function findExport($nik, $result_type, $option = null)
    {

        $select = "";
        $all  = $this->input->post('all');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->db->select($this->table_fda_request . ".fda_request_id");
        $this->db->select($this->table_fda_request . ".fda_request_nik");
        $this->db->select($this->table_fda_request . ".fda_request_nik_approval");
        $this->db->select($this->table_fda_request . ".fda_request_created_date");
        $this->db->select($this->table_fda_request . ".fda_request_approval_date");
        $this->db->select($this->table_fda_request . ".fda_request_date");
        $this->db->select($this->table_fda_request . ".fda_request_image");
        $this->db->select($this->table_fda_request . ".fda_request_desciprtian");
        $this->db->select($this->table_fda_request . ".fda_request_status");
        $this->db->select($this->table_fda_request . ".fda_category_id");
        $this->db->select($this->table_fda_category . ".fda_category_name");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table_employee_app . ".employee_name as approval_name");
        $this->db->order_by($this->table_fda_request . ".fda_request_id", 'DESC');
        $this->db->join($this->table_employee, $this->table_fda_request . '.fda_request_nik = ' . $this->table_employee   . '.nik', 'left');
        $this->db->join('employee e', $this->table_fda_request . '.fda_request_nik_approval = e.nik', 'left');
        $this->db->join($this->table_fda_category, $this->table_fda_request . '.fda_category_id = ' . $this->table_fda_category   . '.fda_category_id', 'left');
        $this->db->group_by($this->table_fda_request . ".fda_request_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(fda_request.fda_request_date) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->table_fda_request . '.fda_request_date)', $tanggalSearchAkhir);
        }

        $this->db->where($this->table_fda_request . '.fda_request_nik', $nik);


        if ($all == 2) {
            $ids = array('STCST');
            $this->db->where_in($this->table_fda_request . '.order_sheet_pickup_status', $ids);
        } elseif ($all == 1) {
            $ids = array('STCS', 'TRST');
            $this->db->where_in($this->table_fda_request . '.order_sheet_pickup_status', $ids);
        }

        if (array_key_exists('order', $option)) {
            $this->db->order_by($this->table_fda_request . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('order_by', $option)) {
                $this->db->order_by($this->table_fda_request . "." . $option['order_by']['field'], $option['order_by']['option']);
            }
        }



        $data_product = $this->db->get($this->table_fda_request)->result();

        $array_data   = array();
        foreach ($data_product as $product) {


            $new_array  = array(
                'data' => $product,
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }
}