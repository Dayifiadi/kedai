<?php
class Mpengguna extends CI_Model
{
  private $table                                    = "user";
  private $table_employee                           = "employee";
  private $table_employee_areakerja                 = "employee_areakerja";
  private $table_unit                               = "unit";
  private $table_subunit                            = "subunit";
  private $table_section                            = "section";
  private $table_firebase_token                     = "firebase_token";
  private $table_user_reset                         = "user_reset";

  private $field_list         = array(
    'user_id', 'user_password',
    'user_status', 'user_application', 'user_type',
    'user_deviceid', 'nik'
  );
  private $exception_field    = array('password');
  private $key_user_id        = "nik";

  function __construct()
  {
    parent::__construct();
    date_default_timezone_set("Asia/Jakarta");
    // $this->load->model('Msetting');
  }


  function Log($nik, $password, $result_type, $option = null)
  {
    $select = "";

    $this->db->select($this->table . ".nik");
    $this->db->select($this->table . ".user_deviceid");
    $this->db->select($this->table . ".user_type");
    $this->db->select($this->table_employee . ".employee_name");
    $this->db->select($this->table_employee . ".branch_code");
    $this->db->select($this->table_employee . ".regional_code");
    $this->db->select($this->table_employee . ".origin_code");
    $this->db->select($this->table_employee . ".unit_code");
    $this->db->select($this->table_employee . ".section_code");
    $this->db->select($this->table_employee . ".employee_version_app");
    $this->db->select($this->table_employee_areakerja . ".areakerja_id");
    $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik', 'left');
    $this->db->join($this->table_employee_areakerja, $this->table . '.nik = ' . $this->table_employee_areakerja   . '.nik', 'left');
    $this->db->where($this->table . '.nik', $nik);
    $this->db->where($this->table . '.user_password', $password);

    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($option['order_by']['field'], $option['order_by']['option']);
      }
    }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }

  /*
  $query = array(
    'user_id' =>  '1'
  )
  $result_type = String
  $option = array()
  */
  function find($query, $result_type, $option = null)
  {
    $select = "";

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (array_key_exists($this->field_list[$i], $query)) {
        $this->db->where($this->table . "." . $this->field_list[$i], $query[$this->field_list[$i]]);
      }
    }

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (!in_array($this->field_list[$i], $this->exception_field)) {
        $select .= $this->field_list[$i] . ",";
      }
    }

    $this->db->select($this->table . ".nik");
    $this->db->select($this->table . ".user_deviceid");
    $this->db->select($this->table . ".user_type");
    $this->db->select($this->table . ".user_application");
    $this->db->select($this->table_employee . ".employee_name");
    $this->db->select($this->table_employee . ".employee_position");
    $this->db->select($this->table_employee . ".employee_status");
    $this->db->select($this->table_employee . ".employee_level");
    $this->db->select($this->table_employee . ".employee_location");
    $this->db->select($this->table_employee . ".employee_area");
    $this->db->select($this->table_employee . ".employee_type");
    $this->db->select($this->table_employee . ".employee_join");
    $this->db->select($this->table_employee . ".employee_phone");
    $this->db->select($this->table_employee . ".employee_version_app");
    $this->db->select($this->table_employee . ".subunit_code");
    $this->db->select($this->table_employee . ".branch_code");
    $this->db->select($this->table_employee . ".unit_code");
    $this->db->select($this->table_employee . ".section_code");
    $this->db->select($this->table_unit . ".unit_name");
    $this->db->select($this->table_subunit . ".subunit_name");
    $this->db->select($this->table_section . ".section_name");

    $this->db->select($this->table_employee_areakerja . ".areakerja_id");
    $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
    $this->db->join($this->table_employee_areakerja, $this->table . '.nik = ' . $this->table_employee_areakerja   . '.nik', 'left ');
    $this->db->join($this->table_unit, $this->table_employee . '.unit_code = ' . $this->table_unit   . '.unit_code', 'left ');
    $this->db->join($this->table_subunit, $this->table_employee . '.subunit_code = ' . $this->table_subunit   . '.subunit_code', 'left ');
    $this->db->join($this->table_section, $this->table_employee . '.section_code = ' . $this->table_section   . '.section_code', 'left ');
    $user_application = array('MYSALES');
    $this->db->where_not_in($this->table . '.user_application', $user_application);
    $this->db->order_by($this->table . ".user_application", 'asc');
    $this->db->group_by($this->table . ".user_application");

    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($option['order_by']['field'], $option['order_by']['option']);
      }
    }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }



  function findMyhc($query, $result_type, $option = null)
  {
    $select = "";

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (array_key_exists($this->field_list[$i], $query)) {
        $this->db->where($this->table . "." . $this->field_list[$i], $query[$this->field_list[$i]]);
      }
    }

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (!in_array($this->field_list[$i], $this->exception_field)) {
        $select .= $this->field_list[$i] . ",";
      }
    }

    $this->db->select($this->table . ".nik");
    $this->db->select($this->table . ".user_deviceid");
    $this->db->select($this->table . ".user_type");
    $this->db->select($this->table . ".user_application");
    $this->db->select($this->table_employee . ".employee_name");
    $this->db->select($this->table_employee . ".employee_position");
    $this->db->select($this->table_employee . ".employee_status");
    $this->db->select($this->table_employee . ".employee_level");
    $this->db->select($this->table_employee . ".employee_location");
    $this->db->select($this->table_employee . ".employee_area");
    $this->db->select($this->table_employee . ".employee_type");
    $this->db->select($this->table_employee . ".employee_join");
    $this->db->select($this->table_employee . ".employee_phone");
    $this->db->select($this->table_employee . ".employee_version_app");
    $this->db->select($this->table_employee . ".subunit_code");
    $this->db->select($this->table_employee . ".branch_code");
    $this->db->select($this->table_employee . ".unit_code");
    $this->db->select($this->table_employee . ".section_code");
    $this->db->select($this->table_unit . ".unit_name");
    $this->db->select($this->table_subunit . ".subunit_name");
    $this->db->select($this->table_section . ".section_name");

    $this->db->select($this->table_employee_areakerja . ".areakerja_id");
    $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik', 'left');
    $this->db->join($this->table_employee_areakerja, $this->table . '.nik = ' . $this->table_employee_areakerja   . '.nik', 'left ');
    $this->db->join($this->table_unit, $this->table_employee . '.unit_code = ' . $this->table_unit   . '.unit_code', 'left ');
    $this->db->join($this->table_subunit, $this->table_employee . '.subunit_code = ' . $this->table_subunit   . '.subunit_code', 'left ');
    $this->db->join($this->table_section, $this->table_employee . '.section_code = ' . $this->table_section   . '.section_code', 'left ');
    $user_application = array('MYHC');
    $this->db->where_in($this->table . '.user_application', $user_application);
    $this->db->order_by($this->table . ".user_application", 'asc');
    $this->db->group_by($this->table . ".user_application");

    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($option['order_by']['field'], $option['order_by']['option']);
      }
    }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }


  function findMyhcNew($query, $result_type, $option = null)
  {
    $select = "";

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (array_key_exists($this->field_list[$i], $query)) {
        $this->db->where($this->table . "." . $this->field_list[$i], $query[$this->field_list[$i]]);
      }
    }

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (!in_array($this->field_list[$i], $this->exception_field)) {
        $select .= $this->field_list[$i] . ",";
      }
    }


    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($option['order_by']['field'], $option['order_by']['option']);
      }
    }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }

  function findUserType($query, $result_type, $option = null)
  {
    $select = "";

    // for ($i=0;$i<count($this->field_list);$i++)
    // {
    //   if (array_key_exists($this->field_list[$i],$query))
    //   {
    //     $this->db->where($this->table.".".$this->field_list[$i],$query[$this->field_list[$i]]);
    //   }
    // }
    //
    // for ($i=0;$i<count($this->field_list);$i++)
    // {
    //   if (!in_array($this->field_list[$i],$this->exception_field))
    //   {
    //     $select.=$this->field_list[$i].",";
    //   }
    // }

    $this->db->select($this->table . ".user_type");
    $this->db->select($this->table . ".user_application");
    $this->db->select($this->table . ".nik");
    $this->db->where($this->table . '.nik', $query);
    $user_application = array('MYSALES');
    $this->db->where_not_in($this->table . '.user_application', $user_application);

    $this->db->order_by($this->table . ".user_type", 'asc');
    // $this->db->group_by($this->table.".user_type");

    // if ($option!=null)
    // {
    //   if (array_key_exists('limit',$option))
    //   {
    //     $this->db->limit($option['limit']);
    //   }
    //
    //   if (array_key_exists('order_by',$option))
    //   {
    //     $this->db->order_by($option['order_by']['field'],$option['order_by']['option']);
    //   }
    // }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }
  public function findReset($nik, $subunit_code, $result_type, $option = null)
  {
    $select = "";
    $all  = $this->input->post('all');
    $nik  = $this->input->post('nik');
    $branch  = $this->input->post('branch');
    $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
    $ADMIN  = $this->input->post('ADMIN');
    $KOORDINATOR  = $this->input->post('KOORDINATOR');
    $JRSPV  = $this->input->post('JRSPV');
    $SPV  = $this->input->post('SPV');
    $nikNEW = array($nik);
    $karyawanNik   = $this->input->post('karyawanNik');
    $reasonNa   = $this->input->post('reasonNa');
    $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
    $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

    $this->db->select($this->table_employee . ".nik");
    $this->db->select($this->table_user_reset . ".nik_approval");
    $this->db->select($this->table_user_reset . ".reason_reset");
    $this->db->select($this->table_user_reset . ".status_reset");
    $this->db->select($this->table_user_reset . ".date_created");
    $this->db->select($this->table_user_reset . ".approval_date");
    $this->db->select($this->table_user_reset . ".user_reset_id");
    $this->db->select($this->table_user_reset . ".data_update");
    $this->db->select($this->table_user_reset . ".extra");
    $this->db->select($this->table_user_reset . ".reset_type");
    $this->db->select($this->table_user_reset . ".keterangan");
    $this->db->select($this->table_employee . ".employee_name");
    $this->db->select($this->table_employee . ".subunit_code");
    $this->db->select("ep.employee_name as nama_approval");
    $this->db->select("ep.subunit_code as subunit_code_approval");
    $this->db->select("tfd.transportation_fuel_detail_km");
    $this->db->select("tfd.transportation_fuel_detail_price");
    $this->db->select("fr.fda_request_date");
    $this->db->select("fr.fda_request_image");
    $this->db->select("fr.fda_category_id");
    $this->db->select("fc.fda_category_name");

    // $this->db->where($this->table_employee . '.nik', $nik);
    $this->db->where_not_in($this->table_user_reset . '.nik', $nikNEW);
    $this->db->order_by($this->table_user_reset . ".user_reset_id", 'DESC');
    $this->db->join($this->table_employee, $this->table_user_reset . '.nik = ' . $this->table_employee   . '.nik');
    $this->db->join('employee ep', $this->table_user_reset . '.nik_approval = ep.nik', 'left');
    $this->db->join('transportation_fuel_detail tfd', $this->table_user_reset . '.extra = tfd.transportation_fuel_detail_id', 'left');
    $this->db->join('fda_request fr', $this->table_user_reset . '.extra = fr.fda_request_id', 'left');
    $this->db->join('fda_category fc', 'fr.fda_category_id = fc.fda_category_id', 'left');

    if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
      $this->db->where('date(user_reset.date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
    } elseif ($tanggalSearchAwal != null) {
      $this->db->where('date(' . $this->table_user_reset . '.date_created)', $tanggalSearchAwal);
    } elseif ($tanggalSearchAkhir != null) {
      $this->db->where('date(' . $this->table_user_reset . '.date_created)', $tanggalSearchAkhir);
    }
    if ($nik == "TSM21E27M2930") {
      $this->db->where($this->table_employee . '.branch_code', $branch);
    } else {
      if ($HUMANCAPITAL == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($ADMIN == 1 && $KOORDINATOR == 0) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        $ids = array('fda');
        $this->db->where_not_in($this->table_user_reset . '.reset_type', $ids);
      } elseif ($KOORDINATOR == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($JRSPV == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($SPV == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } else {
        return FALSE;
      }
    }

    if ($karyawanNik != null) {
      $this->db->where($this->table_user_reset . '.nik', $karyawanNik);
    }

    if ($reasonNa == "Reset Login") {
      $ids = array('device');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "Reset Armada BBM") {
      $ids = array('nopol');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "Update Armada BBM") {
      $ids = array('bbm');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "FDA") {
      if ($ADMIN != 1 && $KOORDINATOR == 0) {
        $ids = array('fda');
        $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
      }
    }


    if ($all == 2) {
      $ids = array('Ditolak', 'Approved');
      $this->db->where_in($this->table_user_reset . '.status_reset', $ids);
    } else if ($all == 1) {
      $ids = array('Pengajuan');
      $this->db->where_in($this->table_user_reset . '.status_reset', $ids);
    }

    if (array_key_exists('order', $option)) {
      $this->db->order_by($this->table_user_reset . "." . $option['order']['order_by'], $option['order']['ordering']);
    }
    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($this->table_user_reset . "." . $option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($this->table_user_reset . "." . $option['order_by']['field'], $option['order_by']['option']);
      }

      if (array_key_exists('page', $option)) {
        $page = $option['page'] * $option['limit'];
        $this->db->limit($option['limit'], $page);
      }
    }



    $data_product = $this->db->get($this->table_user_reset)->result();

    return $data_product;
  }

  public function findEmployee($nik, $subunit_code, $result_type, $option = null)
  {
    $select = "";
    $nik  = $this->input->post('nik');
    $branch  = $this->input->post('branch');
    $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
    $ADMIN  = $this->input->post('ADMIN');
    $KOORDINATOR  = $this->input->post('KOORDINATOR');
    $search  = $this->input->post('search');
    $isLewat  = $this->input->post('isLewat');

    $this->db->select($this->table_employee . ".nik");
    $this->db->select($this->table_employee . ".employee_name");

    if ($isLewat != 'activity') {
      if ($HUMANCAPITAL == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
      } elseif ($ADMIN == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($KOORDINATOR == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      }
    } else {
      $this->db->where($this->table_employee . '.branch_code', $branch);
    }


    if (trim($search) != "") {
      $this->db->like($this->table_employee . '.employee_name', $search);
    }
    if (array_key_exists('order', $option)) {
      $this->db->order_by($this->table_employee . "." . $option['order']['order_by'], $option['order']['ordering']);
    }
    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($this->table_employee . "." . $option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($this->table_employee . "." . $option['order_by']['field'], $option['order_by']['option']);
      }

      if (array_key_exists('page', $option)) {
        $page = $option['page'] * $option['limit'];
        $this->db->limit($option['limit'], $page);
      }
    }



    $data_product = $this->db->get($this->table_employee)->result();

    return $data_product;
  }

  public function findSt($nik, $subunit_code, $result_type, $option = null)
  {
    $select = "";
    $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
    $ADMIN  = $this->input->post('ADMIN');
    $KOORDINATOR  = $this->input->post('KOORDINATOR');
    $JRSPV  = $this->input->post('JRSPV');
    $SPV  = $this->input->post('SPV');
    $branch  = $this->input->post('branch');
    $nikNEW = array($nik);
    $karyawanNik   = $this->input->post('karyawanNik');
    $reasonNa   = $this->input->post('reasonNa');
    $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
    $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');
    $ids = array('Pengajuan');
    $this->db->where_not_in($this->table_user_reset . '.nik', $nikNEW);

    $this->db->join($this->table_employee, $this->table_user_reset . '.nik = ' . $this->table_employee   . '.nik');
    $this->db->where_in($this->table_user_reset . '.status_reset', $ids);


    if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
      $this->db->where('date(user_reset.date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
    } elseif ($tanggalSearchAwal != null) {
      $this->db->where('date(' . $this->table_user_reset . '.date_created)', $tanggalSearchAwal);
    } elseif ($tanggalSearchAkhir != null) {
      $this->db->where('date(' . $this->table_user_reset . '.date_created)', $tanggalSearchAkhir);
    }

    if ($nik == "TSM21E27M2930") {
      $this->db->where($this->table_employee . '.branch_code', $branch);
    } else {
      if ($HUMANCAPITAL == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($ADMIN == 1 && $KOORDINATOR == 0) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        $ids = array('fda');
        $this->db->where_not_in($this->table_user_reset . '.reset_type', $ids);
      } elseif ($KOORDINATOR == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($JRSPV == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($SPV == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } else {
        return FALSE;
      }
    }
    if ($karyawanNik != null) {
      $this->db->where($this->table_user_reset . '.nik', $karyawanNik);
    }


    if ($reasonNa == "Reset Login") {
      $ids = array('device');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "Reset Armada BBM") {
      $ids = array('nopol');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "Update Armada BBM") {
      $ids = array('bbm');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "FDA") {
      if ($ADMIN != 1 && $KOORDINATOR == 0) {
        $ids = array('fda');
        $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
      }
    }

    return $this->db->get($this->table_user_reset)->num_rows();
  }
  public function find_not_fin($nik, $subunit_code, $result_type, $option = null)
  {
    $select = "";
    $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
    $ADMIN  = $this->input->post('ADMIN');
    $KOORDINATOR  = $this->input->post('KOORDINATOR');
    $branch  = $this->input->post('branch');
    $ids = array('Approved');
    $nikNEW = array($nik);
    $JRSPV  = $this->input->post('JRSPV');
    $SPV  = $this->input->post('SPV');
    $karyawanNik   = $this->input->post('karyawanNik');
    $reasonNa   = $this->input->post('reasonNa');
    $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
    $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');
    $this->db->where_not_in($this->table_user_reset . '.nik', $nikNEW);
    if ($HUMANCAPITAL == 1) {
      $this->db->where($this->table_employee . '.branch_code', $branch);
    } elseif ($ADMIN == 1) {
      $this->db->where($this->table_employee . '.branch_code', $branch);
      $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
    } elseif ($KOORDINATOR == 1) {
      $this->db->where($this->table_employee . '.branch_code', $branch);
      $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
    } elseif ($JRSPV == 1) {
      $this->db->where($this->table_employee . '.branch_code', $branch);
      $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
    } elseif ($SPV == 1) {
      $this->db->where($this->table_employee . '.branch_code', $branch);
      $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
    }
    $this->db->join($this->table_employee, $this->table_user_reset . '.nik = ' . $this->table_employee   . '.nik');
    $this->db->where_in($this->table_user_reset . '.status_reset', $ids);


    if ($nik == "TSM21E27M2930") {
      $this->db->where($this->table_employee . '.branch_code', $branch);
    } else {
      if ($HUMANCAPITAL == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($ADMIN == 1 && $KOORDINATOR == 0) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
        $ids = array('fda');
        $this->db->where_not_in($this->table_user_reset . '.reset_type', $ids);
      } elseif ($KOORDINATOR == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($JRSPV == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } elseif ($SPV == 1) {
        $this->db->where($this->table_employee . '.branch_code', $branch);
        $this->db->where($this->table_employee . '.subunit_code', $subunit_code);
      } else {
        return FALSE;
      }
    }
    if ($karyawanNik != null) {
      $this->db->where($this->table_user_reset . '.nik', $karyawanNik);
    }

    if ($reasonNa == "Reset Login") {
      $ids = array('device');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "Reset Armada BBM") {
      $ids = array('nopol');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "Update Armada BBM") {
      $ids = array('bbm');
      $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
    } else if ($reasonNa == "FDA") {
      if ($ADMIN != 1 && $KOORDINATOR == 0) {
        $ids = array('fda');
        $this->db->where_in($this->table_user_reset . '.reset_type', $ids);
      }
    }

    return $this->db->get($this->table_user_reset)->num_rows();
  }
  function findTypeAll($query, $result_type, $option = null)
  {
    $select = "";

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (array_key_exists($this->field_list[$i], $query)) {
        $this->db->where($this->table . "." . $this->field_list[$i], $query[$this->field_list[$i]]);
      }
    }

    for ($i = 0; $i < count($this->field_list); $i++) {
      if (!in_array($this->field_list[$i], $this->exception_field)) {
        $select .= $this->field_list[$i] . ",";
      }
    }

    $this->db->select($this->table . ".nik");
    $this->db->select($this->table . ".user_deviceid");
    $this->db->select($this->table . ".user_type");
    $this->db->select($this->table . ".user_application");

    $this->db->order_by($this->table . ".user_application", 'asc');

    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($option['order_by']['field'], $option['order_by']['option']);
      }
    }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }




  function create($data)
  {
    if ($this->db->insert($this->table, $data)) {
      return $this->db->insert_id();
    } else {
      return false;
    }
  }

  function update($data, $where)
  {
    if ($this->db->update($this->table, $data, $where)) {
      return true;
    } else {
      return false;
    }
  }

  function findNew($id_pengguna, $pass, $result_type, $option = null)
  {
    $select = "";

    $this->db->select($this->table . ".nama_lengkap");
    $this->db->where($this->table . '.id_pengguna', $id_pengguna);
    $this->db->where($this->table . '.password', $pass);
    if ($option != null) {
      if (array_key_exists('limit', $option)) {
        $this->db->limit($option['limit']);
      }

      if (array_key_exists('order_by', $option)) {
        $this->db->order_by($option['order_by']['field'], $option['order_by']['option']);
      }
    }

    if ($result_type == "row") {
      return $this->db->get($this->table)->row();
    } else {
      return $this->db->get($this->table)->result();
    }
  }

  function findResetDup()
  {
    $select = "";

    $this->db->where('reset_type', $this->input->post('reset_type'));
    $this->db->where('nik', $this->input->post('nik'));
    if ($this->input->post('reset_type') == 'nopol' || $this->input->post('reset_type') == 'km') {
      $this->db->where('extra', $this->input->post('extra'));
    }
    $this->db->where('status_reset', "Pengajuan");
    $this->db->order_by('user_reset_id', 'DESC');
    return $this->db->get('user_reset')->row();
  }

  function findResetFda()
  {
    $select = "";

    $this->db->where('reset_type', $this->input->post('reset_type'));
    $this->db->where('nik', $this->input->post('nik'));
    if ($this->input->post('reset_type') == 'nopol' || $this->input->post('reset_type') == 'km') {
      $this->db->where('extra', $this->input->post('extra'));
    }
    $this->db->where('status_reset', "Pengajuan");
    $this->db->order_by('user_reset_id', 'DESC');
    return $this->db->get('user_reset')->row();
  }
  function findToken($nik, $result_type)
  {
    $select = "";


    $this->db->where($this->table_firebase_token . '.nik', $nik);
    if ($result_type == "row") {
      return $this->db->get($this->table_firebase_token)->row();
    } else {
      return $this->db->get($this->table_firebase_token)->result();
    }
  }

  function getTokenAll($subunitCode, $result_type)
  {
    $select = "";


    $this->db->where($this->table_employee . '.subunit_code', $subunitCode);
    $this->db->join($this->table_employee, $this->table_firebase_token . '.nik = ' . $this->table_employee   . '.nik');
    if ($result_type == "row") {
      return $this->db->get($this->table_firebase_token)->row();
    } else {
      return $this->db->get($this->table_firebase_token)->result();
    }
  }



  function createToken($data)
  {
    if ($this->db->insert($this->table_firebase_token, $data)) {
      return true;
    } else {
      return false;
    }
  }

  function updateToken($data, $where)
  {
    if ($this->db->update($this->table_firebase_token, $data, $where)) {
      return true;
    } else {
      return false;
    }
  }

  function updateEmployee($data, $where)
  {
    if ($this->db->update($this->table_employee, $data, $where)) {
      return true;
    } else {
      return false;
    }
  }
}