<?php
class Maktivitas extends CI_Model
{
    private $table   = "activity";
    private $table_area_kerja                          = "areakerja";
    private $table_employee_areakerja                          = "employee_areakerja";
    private $table_area_kerja_keluar                          = "areakerja ak";
    private $table_pengguna                           = "user";
    private $table_activity_kategory                          = "activity_kategory";
    private $table_position                          = "position";
    private $table_activity_repair_kategory                          = "activity_repair_kategory";
    private $table_activity_repair                          = "activity_repair";
    private $table_transportation_fuel_detail                          = "transportation_fuel_detail";

    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->dbMyAsset = $this->load->database('dbMyAsset', TRUE);
        $this->load->model('Msetting');
    }


    public function checkGlobal($nik, $isGlobal)
    {
        $select = "";

        $this->db->select($this->table_area_kerja . ".areakerja_id");
        $this->db->select($this->table_area_kerja . ".is_global");
        $this->db->join($this->table_area_kerja, $this->table_employee_areakerja . '.areakerja_id = ' . $this->table_area_kerja   . '.areakerja_id');
        $this->db->where($this->table_employee_areakerja . '.nik', $nik);
        $this->db->where($this->table_area_kerja . '.is_global', $isGlobal);
        return $this->db->get($this->table_employee_areakerja)->row();
    }

    public function create($data)
    {
        if ($this->dbMyAsset->insert($this->table, $data)) {
            return $this->dbMyAsset->insert_id();
        } else {
            return false;
        }
    }

    public function update($data, $where)
    {
        if ($this->db->update($this->table, $data, $where)) {
            return true;
        } else {
            return false;
        }
    }


    public function findHistory($nik, $result_type, $option = null)
    {
        $select = "";

        $tanggalSearch   = $this->input->post('tanggalSearch');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');
        $dateNow = date('Y-m-d');
        $this->dbMyAsset->select($this->table . ".activity_deskripsi_aktivitas");
        $this->dbMyAsset->select($this->table . ".activity_date_created");
        $this->dbMyAsset->select($this->table . ".activity_foto");
        $this->dbMyAsset->select($this->table . ".activity_foto1");
        $this->dbMyAsset->select($this->table . ".activity_foto2");
        $this->dbMyAsset->select($this->table . ".activity_foto3");
        $this->dbMyAsset->select($this->table . ".activity_id");
        $this->dbMyAsset->select($this->table . ".km_armada");
        $this->dbMyAsset->select($this->table . ".full_name");
        $this->dbMyAsset->select($this->table . ".status_armada");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_name");
        $this->dbMyAsset->select($this->table . ".areakerja_name");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".stop_type_code");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_id");
        $this->dbMyAsset->select($this->table . ".no_plat");
        $this->dbMyAsset->select($this->table . ".driverNik");
        $this->dbMyAsset->select($this->table . ".activity_type");
        $this->dbMyAsset->select($this->table . ".driverName");
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->join($this->table_transportation_fuel_detail, $this->table . '.activity_id = ' . $this->table_transportation_fuel_detail   . '.activity_id', 'left');
        $this->dbMyAsset->where($this->table . '.nik', $nik);
        $this->dbMyAsset->order_by($this->table . ".activity_id", 'DESC');
        $this->dbMyAsset->group_by($this->table . ".activity_id");

        if ($tanggalSearch != null) {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $tanggalSearch);
        } elseif ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(activity.activity_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
            // $this->dbMyAsset->or_where('date(absensi_karyawan.absensikaryawan_date_keluar) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $tanggalSearchAwal);
            // $this->dbMyAsset->or_where('date('.$this->table . '.absensikaryawan_date_keluar)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $tanggalSearchAkhir);
            // $this->dbMyAsset->or_where('date('.$this->table . '.absensikaryawan_date_keluar)', $tanggalSearchAkhir);
        } else {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $dateNow);
        }

        if (array_key_exists('order', $option)) {
            $this->dbMyAsset->order_by($this->table . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->dbMyAsset->limit($this->table . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->dbMyAsset->order_by($this->table . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->dbMyAsset->limit($option['limit'], $page);
            }
        }

        if ($result_type == "row") {
            return $this->dbMyAsset->get($this->table)->row();
        } else {
            return $this->dbMyAsset->get($this->table)->result();
        }
    }
    public function findHs($nik, $result_type, $option = null)
    {
        $select = "";

        $tanggalSearch   = $this->input->post('tanggalSearch');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');
        $dateNow = date('Y-m-d');
        $this->dbMyAsset->select($this->table . ".activity_deskripsi_aktivitas");
        $this->dbMyAsset->select($this->table . ".activity_date_created");
        $this->dbMyAsset->select($this->table . ".activity_foto");
        $this->dbMyAsset->select($this->table . ".activity_foto1");
        $this->dbMyAsset->select($this->table . ".activity_foto2");
        $this->dbMyAsset->select($this->table . ".activity_foto3");
        $this->dbMyAsset->select($this->table . ".activity_id");
        $this->dbMyAsset->select($this->table . ".km_armada");
        $this->dbMyAsset->select($this->table . ".full_name");
        $this->dbMyAsset->select($this->table . ".status_armada");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_name");
        $this->dbMyAsset->select($this->table . ".areakerja_name");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".stop_type_code");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_id");
        $this->dbMyAsset->select($this->table . ".no_plat");
        $this->dbMyAsset->select($this->table . ".driverNik");
        $this->dbMyAsset->select($this->table . ".activity_type");
        $this->dbMyAsset->select($this->table . ".driverName");
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->join($this->table_transportation_fuel_detail, $this->table . '.activity_id = ' . $this->table_transportation_fuel_detail   . '.activity_id', 'left');
        $this->dbMyAsset->where($this->table . '.nik', $nik);
        $this->dbMyAsset->order_by($this->table . ".activity_id", 'DESC');
        $this->dbMyAsset->group_by($this->table . ".activity_id");

        if ($tanggalSearch != null) {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $tanggalSearch);
        } elseif ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(activity.activity_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
            // $this->dbMyAsset->or_where('date(absensi_karyawan.absensikaryawan_date_keluar) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $tanggalSearchAwal);
            // $this->dbMyAsset->or_where('date('.$this->table . '.absensikaryawan_date_keluar)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(' . $this->table . '.activity_date_created)', $tanggalSearchAkhir);
            // $this->dbMyAsset->or_where('date('.$this->table . '.absensikaryawan_date_keluar)', $tanggalSearchAkhir);
        } else {
            // $this->dbMyAsset->where('date('.$this->table . '.activity_date_created)', $dateNow);
        }

        if (array_key_exists('order', $option)) {
            $this->dbMyAsset->order_by($this->table . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->dbMyAsset->limit($this->table . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->dbMyAsset->order_by($this->table . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->dbMyAsset->limit($option['limit'], $page);
            }
        }

        if ($result_type == "row") {
            return $this->dbMyAsset->get($this->table)->row();
        } else {
            return $this->dbMyAsset->get($this->table)->result();
        }
    }

    public function findKat($employeePosition)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_name");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_employee_position");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_id");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_type");
        // $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_employee_position', $employeePosition);
        if ($this->input->post('branch') != NULL) {
            $this->dbMyAsset->where($this->table_activity_kategory . '.branch_code', $this->input->post('branch'));
        }
        $this->dbMyAsset->order_by($this->table_activity_kategory . ".activity_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_kategory)->result();
    }

    public function findKatId($activityKategoriId, $employeePosition)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_name");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_employee_position");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_id");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_type");

        // $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_employee_position', $employeePosition);
        // if ($this->input->post('branch') != NULL) {
        // if ($this->input->post('branch') == "TSM000") {
        //     $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_id', $activityKategoriId);
        // } else {
        //     $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_name', $activityKategoriId);
        // }
        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_name', $activityKategoriId);

        $this->dbMyAsset->where($this->table_activity_kategory . '.branch_code', $this->input->post('branch'));
        // }
        $this->dbMyAsset->order_by($this->table_activity_kategory . ".activity_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_kategory)->row();
    }

    public function findByType($employeePosition)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_name");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_employee_position");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_id");
        $this->dbMyAsset->select($this->table_activity_kategory . ".activity_kategory_type");

        $this->dbMyAsset->like($this->table_activity_kategory . '.activity_kategory_employee_position', $employeePosition);
        $ids = array('BBM ARMADA', 'CEK & REPAIR', 'DAILY DRIVER');

        $this->dbMyAsset->where_in($this->table_activity_kategory . '.activity_kategory_type', $ids);
        // if ($this->input->post('branch') != NULL) {
        // if ($this->input->post('branch') == "TSM000") {
        //     $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_id', $activityKategoriId);
        // } else {
        //     $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_name', $activityKategoriId);
        // }

        $this->dbMyAsset->where($this->table_activity_kategory . '.branch_code', $this->input->post('branch'));
        // }
        $this->dbMyAsset->order_by($this->table_activity_kategory . ".activity_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_kategory)->row();
    }

    public function findKatRepair($activity_kategory_id)
    {
        $select = "";

        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_id', $activity_kategory_id);
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table_activity_repair_kategory . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->order_by($this->table_activity_repair_kategory . ".activity_repair_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_repair_kategory)->result();
    }

    public function findKatRepairType($employeePosition, $activity_kategory_id, $branch_code)
    {
        $select = "";

        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', $activity_kategory_id);
        // $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_employee_position', $employeePosition);
        $this->dbMyAsset->where($this->table_activity_kategory . '.branch_code', $branch_code);
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table_activity_repair_kategory . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->order_by($this->table_activity_repair_kategory . ".activity_repair_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_repair_kategory)->result();
    }
    public function findLastActivity($nik, $platNo)
    {
        $select = "";

        $this->dbMyAsset->select($this->table . ".*");
        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'DAILY DRIVER');
        $this->dbMyAsset->where($this->table . '.no_plat', $platNo);
        $this->dbMyAsset->where($this->table . '.nik', $nik);
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->order_by($this->table . ".activity_id", 'DESC');

        $this->dbMyAsset->group_by($this->table . ".activity_id");
        return $data_product = $this->dbMyAsset->get($this->table)->row();
    }
    public function findRepairYes($activity_id)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_id");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_kategory_id");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_description");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_type");
        $this->dbMyAsset->select($this->table . ".activity_id");
        $this->dbMyAsset->select($this->table_activity_repair_kategory . ".activity_repair_kategory_name");
        $this->dbMyAsset->where($this->table . '.activity_id', $activity_id);
        $this->dbMyAsset->join($this->table_activity_repair, $this->table . '.activity_id = ' . $this->table_activity_repair   . '.activity_id');
        $this->dbMyAsset->join($this->table_activity_repair_kategory, $this->table_activity_repair . '.activity_repair_kategory_id = ' . $this->table_activity_repair_kategory   . '.activity_repair_kategory_id');
        return $this->dbMyAsset->get($this->table)->result();
    }

    public function findRepairYesByFuelId($transportationFuelId, $lewat = NULL)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_id");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_kategory_id");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_description");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_type");
        $this->dbMyAsset->select($this->table . ".activity_id");
        $this->dbMyAsset->select($this->table_activity_repair_kategory . ".activity_repair_kategory_name");
        if ($lewat == 'DAILY') {
            $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'DAILY DRIVER');
            $this->dbMyAsset->join($this->table_activity_kategory, $this->table . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        }
        $this->dbMyAsset->where($this->table . '.transportation_fuel_id', $transportationFuelId);
        $this->dbMyAsset->join($this->table_activity_repair, $this->table . '.activity_id = ' . $this->table_activity_repair   . '.activity_id');
        $this->dbMyAsset->join($this->table_activity_repair_kategory, $this->table_activity_repair . '.activity_repair_kategory_id = ' . $this->table_activity_repair_kategory   . '.activity_repair_kategory_id');
        return $this->dbMyAsset->get($this->table)->result();
    }
    public function findKatRepairTypeNa($activity_kategory_id)
    {
        $select = "";

        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'DAILY DRIVER');
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table_activity_repair_kategory . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->order_by($this->table_activity_repair_kategory . ".activity_repair_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_repair_kategory)->result();
    }
    public function findDaily($employeePosition)
    {
        $select = "";

        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'DAILY DRIVER');
        $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_employee_position', $employeePosition);
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table_activity_repair_kategory . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->order_by($this->table_activity_repair_kategory . ".activity_repair_kategory_name", 'ASC');
        return $this->dbMyAsset->get($this->table_activity_repair_kategory)->result();
    }
    public function findKatRepairNew($activity_kategory_id, $platNo, $lewat)
    {
        $select = "";

        $this->dbMyAsset->select($this->table . ".*");
        $this->dbMyAsset->select("tfd.transportation_fuel_detail_km");
        if ($lewat == 'BBM') {
            $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'BBM ARMADA');
        } elseif ($lewat == 'CEK') {
            $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'CEK & REPAIR');
        } else {
            $this->dbMyAsset->where($this->table_activity_kategory . '.activity_kategory_type', 'DAILY DRIVER');
        }
        $this->dbMyAsset->where($this->table . '.no_plat', $platNo);
        $this->dbMyAsset->join($this->table_activity_kategory, $this->table . '.activity_kategory_id = ' . $this->table_activity_kategory   . '.activity_kategory_id', 'left');
        $this->dbMyAsset->join('transportation_fuel_detail tfd', $this->table . '.activity_id =  tfd.activity_id', 'left');
        $this->dbMyAsset->order_by($this->table . ".activity_id", 'DESC');

        $this->dbMyAsset->group_by($this->table . ".activity_id");
        return $data_product = $this->dbMyAsset->get($this->table)->row();

        // $array_data   = array();

        // $data_bar   = $this->findKatRepairYes($data_product->activity_id, $platNo);
        // $dataRepair   = $this->findKatCekYes($data_product->activity_id, $platNo);


        // $new_array  = array(
        //     'dataList' => $data_product,
        //     'dataCek' => $data_bar,
        //     'dataRepair' => $dataRepair,
        // );

        // array_push($array_data, $new_array);
        // return $array_data;
    }

    public function findKatRepairNewStop($transportation_fuel_detail_km)
    {
        $select = "";
        $this->dbMyAsset->select("st.stop_type_name");
        $this->dbMyAsset->where('tfd.transportation_fuel_detail_km', $transportation_fuel_detail_km);
        $this->dbMyAsset->join('stop_type st',  'st.stop_type_code =  tfd.stop_type_code', 'left');

        return $data_product = $this->dbMyAsset->get('transportation_fuel_detail tfd')->row();

        // $array_data   = array();

        // $data_bar   = $this->findKatRepairYes($data_product->activity_id, $platNo);
        // $dataRepair   = $this->findKatCekYes($data_product->activity_id, $platNo);


        // $new_array  = array(
        //     'dataList' => $data_product,
        //     'dataCek' => $data_bar,
        //     'dataRepair' => $dataRepair,
        // );

        // array_push($array_data, $new_array);
        // return $array_data;
    }
    public function findKatCekYes($activity_repair_kategory_id, $platNo)
    {
        $select = "";

        $this->db->select($this->table_activity_repair . ".activity_repair_id");
        $this->db->select($this->table_activity_repair . ".activity_repair_kategory_id");
        $this->db->select($this->table_activity_repair . ".activity_repair_description");
        $this->db->select($this->table_activity_repair . ".activity_repair_type");
        $this->db->select($this->table . ".activity_id");
        $this->db->select($this->table_activity_repair_kategory . ".activity_repair_kategory_name");
        $this->db->where($this->table . '.activity_id', $activity_repair_kategory_id);
        $this->db->where($this->table . '.no_plat', $platNo);
        $this->db->where($this->table_activity_repair . '.activity_repair_type', 'Repair');
        $this->db->join($this->table_activity_repair, $this->table . '.activity_id = ' . $this->table_activity_repair   . '.activity_id', 'left');
        $this->db->join($this->table_activity_repair_kategory, $this->table_activity_repair . '.activity_repair_kategory_id = ' . $this->table_activity_repair_kategory   . '.activity_repair_kategory_id', 'left');
        return $this->db->get($this->table)->result();
    }
    public function findKatRepairYes($activity_repair_kategory_id, $platNo)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_id");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_kategory_id");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_description");
        $this->dbMyAsset->select($this->table_activity_repair . ".activity_repair_type");

        $this->dbMyAsset->select($this->table . ".activity_id");
        $this->dbMyAsset->select($this->table_activity_repair_kategory . ".activity_repair_kategory_name");
        $this->dbMyAsset->where($this->table . '.activity_id', $activity_repair_kategory_id);
        $this->dbMyAsset->where($this->table . '.no_plat', $platNo);
        // $this->dbMyAsset->where($this->table_activity_repair_kategory . '.activity_kategory_type', 'Cek');
        $this->dbMyAsset->join($this->table_activity_repair, $this->table . '.activity_id = ' . $this->table_activity_repair   . '.activity_id', 'left');
        $this->dbMyAsset->join($this->table_activity_repair_kategory, $this->table_activity_repair . '.activity_repair_kategory_id = ' . $this->table_activity_repair_kategory   . '.activity_repair_kategory_id', 'left');
        return $this->dbMyAsset->get($this->table)->result();
    }
    public function findPos($employeePosition)
    {
        $select = "";

        $this->db->select($this->table_position . ".position_group");
        $this->db->where($this->table_position . '.position_name', $employeePosition);
        $this->db->order_by($this->table_position . ".position_name", 'ASC');
        $this->db->group_by($this->table_position . ".position_group");
        return $this->db->get($this->table_position)->row();
    }
    public function checkJarakGlobalArea($branch, $latitude_user, $longitude_user)
    {
        $query = "SELECT ae.areakerja_id,ae.areakerja_name,ae.max_distance,(6371 * acos( cos( radians(" . $latitude_user . ") ) * cos( radians( ae.areakerja_latitude ) ) *
                cos( radians( ae.areakerja_longitude ) - radians(" . $longitude_user . ") ) + sin( radians(" . $latitude_user . ") ) *
                sin( radians( ae.areakerja_latitude ) ) ) ) AS distance
        FROM areakerja ae where ae.branch_code='" . $branch . "'  order by distance ASC";
        $result = $this->db->query($query)->row();
        return $result;
    }
}
