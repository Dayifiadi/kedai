<?php
class MBbm extends CI_Model
{
    private $table_transportation                     = "transportation";
    private $table_transportation_type                = "transportation_type";
    private $table_areakerja                          = "areakerja";
    private $table_transportation_fuel                = "transportation_fuel";
    private $table_transportation_fuel_detail         = "transportation_fuel_detail";
    private $table_stop_type                          = "stop_type";
    private $table_employee                           = "employee";
    private $table_asset                              = "asset";


    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->dbMyAsset = $this->load->database('dbMyAsset', TRUE);
        $this->load->model('Msetting');
    }


    public function cekNopol($option = null)
    {
        $select = "";

        $keyword              = $this->input->post('search');
        $branch  = $this->input->post('branch');
        $origin  = $this->input->post('origin');

        $this->dbMyAsset->select($this->table_areakerja . ".areakerja_name");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_nopol");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_name");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_fuel_name");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_keterangan_area");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_is_running");
        $this->dbMyAsset->join($this->table_transportation_type, $this->table_transportation . '.transportation_type_code = ' . $this->table_transportation_type   . '.transportation_type_code');
        $this->dbMyAsset->join($this->table_areakerja, $this->table_transportation . '.transportation_areakerja_id = ' . $this->table_areakerja   . '.areakerja_id', 'left');
        $this->dbMyAsset->order_by($this->table_transportation . ".transportation_nopol", 'DESC');
        if ($branch == 'BDO000') {
            $this->dbMyAsset->where($this->table_transportation . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation . '.origin_code', $origin);
        } elseif ($branch != '') {
            $this->dbMyAsset->where($this->table_transportation . '.branch_code', $branch);
        }
        if (trim($keyword) != "") {
            $this->dbMyAsset->like('transportation_nopol', $keyword);
        }
        if (array_key_exists('order', $option)) {
            $this->dbMyAsset->order_by($this->table_transportation . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->dbMyAsset->limit($this->table_transportation . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->dbMyAsset->order_by($this->table_transportation . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->dbMyAsset->limit($option['limit'], $page);
            }
        }
        return $this->dbMyAsset->get($this->table_transportation)->result();
    }

    public function cekNopolAsset($option = null)
    {
        $select = "";

        $keyword              = $this->input->post('search');
        $branch  = $this->input->post('branch');
        $origin  = $this->input->post('origin');

        $this->dbMyAsset->select($this->table_asset . ".asset_serialnumber");
        $this->dbMyAsset->select($this->table_asset . ".asset_detail");
        $this->dbMyAsset->select($this->table_asset . ".asset_location");
        $this->dbMyAsset->select($this->table_asset . ".asset_note");
        $this->dbMyAsset->select($this->table_asset . ".zone_code");
        $this->dbMyAsset->order_by($this->table_asset . ".asset_serialnumber", 'DESC');
        if ($branch == 'BDO000') {
            $this->dbMyAsset->where($this->table_asset . '.branch', $branch);
            // $this->dbMyAsset->where($this->table_asset . '.origin', $origin);
        } elseif ($branch != '') {
            $this->dbMyAsset->where($this->table_asset . '.branch', $branch);
        }
        // $this->dbMyAsset->where($this->table_asset . '.asset_productname', 'ARMADA');
        $ids = array('ARMADA', 'KENDARAAN');

        $this->dbMyAsset->where_in($this->table_asset . '.asset_productname', $ids);
        $this->dbMyAsset->where($this->table_asset . '.asset_serialnumber !=', "");

        if (trim($keyword) != "") {
            $this->dbMyAsset->like('asset_serialnumber', $keyword);
        }
        if (array_key_exists('order', $option)) {
            $this->dbMyAsset->order_by($this->table_asset . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->dbMyAsset->limit($this->table_asset . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->dbMyAsset->order_by($this->table_asset . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->dbMyAsset->limit($option['limit'], $page);
            }
        }
        $data_product = $this->dbMyAsset->get($this->table_asset)->result();

        $array_data   = array();
        foreach ($data_product as $product) {
            $data_start   = $this->cekZone($product->zone_code);


            $new_array  = array(
                'data' => $product,
                'data_zone'   => $data_start,
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }

    public function cekZone($zone)
    {
        $select = "";

        $this->db->where('zone_code', $zone);

        return $this->db->get('zone')->row();
    }
    public function cekNopolDet()
    {
        $select = "";
        $nopol  = $this->input->post('nopol');

        $this->dbMyAsset->select($this->table_areakerja . ".areakerja_name");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_nopol");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_name");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_fuel_name");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_keterangan_area");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_is_running");
        $this->dbMyAsset->join($this->table_transportation_type, $this->table_transportation . '.transportation_type_code = ' . $this->table_transportation_type   . '.transportation_type_code');
        $this->dbMyAsset->join($this->table_areakerja, $this->table_transportation . '.transportation_areakerja_id = ' . $this->table_areakerja   . '.areakerja_id', 'left');
        $this->dbMyAsset->order_by($this->table_transportation . ".transportation_nopol", 'DESC');
        $this->dbMyAsset->where($this->table_transportation . '.transportation_nopol', $nopol);
        return $this->dbMyAsset->get($this->table_transportation)->row();
    }
    public function findLastKmByNopol($nopol)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->join($this->table_transportation_fuel_detail, $this->table_transportation_fuel . '.transportation_fuel_id = ' . $this->table_transportation_fuel_detail   . '.transportation_fuel_id');
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id", 'DESC');
        $this->dbMyAsset->limit(1);
        $this->dbMyAsset->where($this->table_transportation_fuel . '.transportation_fuel_nopol', $nopol);
        return $this->dbMyAsset->get($this->table_transportation_fuel)->row();
    }
    public function getLastStatus($nopol)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_stop_type . ".stop_type_name");
        $this->dbMyAsset->join($this->table_stop_type, $this->table_transportation_fuel . '.stop_type_code_fuel = ' . $this->table_stop_type   . '.stop_type_code');
        $this->dbMyAsset->where($this->table_transportation_fuel . '.transportation_fuel_nopol', $nopol);
        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');
        return $this->dbMyAsset->get($this->table_transportation_fuel)->row();
    }
    public function findLastName($nopol)
    {
        $select = "";

        $this->dbMyAsset->where($this->table_transportation_fuel . '.transportation_fuel_nopol', $nopol);
        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');
        return $this->dbMyAsset->get($this->table_transportation_fuel)->row();
    }
    public function cekNopolDetAsset()
    {
        $select = "";
        $nopol  = $this->input->post('nopol');

        $this->dbMyAsset->select($this->table_asset . ".asset_serialnumber");
        $this->dbMyAsset->select($this->table_asset . ".asset_detail");
        $this->dbMyAsset->select($this->table_asset . ".asset_location");
        $this->dbMyAsset->select($this->table_asset . ".asset_note");
        $this->dbMyAsset->select($this->table_asset . ".zone_code");
        $this->dbMyAsset->order_by($this->table_asset . ".asset_serialnumber", 'DESC');
        $this->dbMyAsset->where($this->table_asset . '.asset_serialnumber', $nopol);
        return $this->dbMyAsset->get($this->table_asset)->row();
    }
    public function ambilLastKm($idNa)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id", 'DESC');
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }
    public function getLastNopol($no_plat)
    {
        $select = "";

        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');
        $this->dbMyAsset->where($this->table_transportation_fuel . '.transportation_fuel_nopol', $no_plat);
        return $this->dbMyAsset->get($this->table_transportation_fuel)->row();
    }
    public function ambilLastKmNa($idNa)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_distance");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_price");
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id", 'DESC');
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }

    public function ambilFirst($idNa)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_distance");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_price");
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id", 'ASC');
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }

    public function sumAllBBM($idNa)
    {
        $select = "";

        $this->dbMyAsset->select("SUM($this->table_transportation_fuel_detail.transportation_fuel_detail_price) as totalHarga");
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_id", 'ASC');
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }

    public function sumAllLiter($idNa)
    {
        $select = "";

        $this->dbMyAsset->select("SUM($this->table_transportation_fuel_detail.transportation_fuel_detail_bbm) as totalLiter");
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_id", 'ASC');
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }
    public function sumKmNa($idNa)
    {
        $select = "";

        $this->dbMyAsset->select("SUM($this->table_transportation_fuel_detail.transportation_fuel_detail_km) as totalKm");
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_id", 'ASC');
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }

    public function ambilIDNA($idNa)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_id");
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_detail_id', $idNa);
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
    }

    public function cekStop()
    {
        $select = "";
        $ids = array('STCMBB', 'TRST', 'STCST');

        $this->dbMyAsset->where_in($this->table_stop_type . '.stop_type_code', $ids);
        return $this->dbMyAsset->get($this->table_stop_type)->result();
    }

    public function cekStopNew()
    {
        $select = "";
        // $ids = array('STCMBB', 'TRST');
        $ids = array('STCMBB', 'TRST', 'STCST');

        $this->dbMyAsset->where_in($this->table_stop_type . '.stop_type_code', $ids);
        return $this->dbMyAsset->get($this->table_stop_type)->result();
    }
    public function stop()
    {
        $select = "";
        $ids = array('STCS', 'STCST', 'SC');

        $this->dbMyAsset->where_in($this->table_stop_type . '.stop_type_code', $ids);
        return $this->dbMyAsset->get($this->table_stop_type)->result();
    }


    public function cekStart($status, $transportation_fuel_id)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_date");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km1");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km2");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km3");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km");
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $transportation_fuel_id);
        if ($status == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.stop_type_code', "STCS");
        } else {
            $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.stop_type_code', "STCST");
        }
        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->row();
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

        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_date_created");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_id");
        $this->dbMyAsset->select($this->table_employee . ".nik");
        $this->dbMyAsset->select($this->table_employee . ".employee_name");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".stop_type_code_fuel");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_name");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_nopol");

        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');
        $this->dbMyAsset->join($this->table_employee, $this->table_transportation_fuel . '.nik = ' . $this->table_employee   . '.nik');
        $this->dbMyAsset->join($this->table_transportation, $this->table_transportation_fuel . '.transportation_fuel_nopol = ' . $this->table_transportation   . '.transportation_nopol');
        $this->dbMyAsset->join($this->table_transportation_type, $this->table_transportation . '.transportation_type_code = ' . $this->table_transportation_type   . '.transportation_type_code');

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(transportation_fuel.transportation_fuel_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->dbMyAsset->where($this->table_employee . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->dbMyAsset->where($this->table_employee . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_employee . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->dbMyAsset->where($this->table_employee . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_employee . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $karyawanNik);
        }

        if ($all == 2) {
            $ids = array('STCST');
            $this->dbMyAsset->where_in($this->table_transportation_fuel . '.stop_type_code_fuel', $ids);
        } elseif ($all == 1) {
            $ids = array('STCS', 'TRST', 'STCMBB');
            $this->dbMyAsset->where_in($this->table_transportation_fuel . '.stop_type_code_fuel', $ids);
        }

        if (array_key_exists('order', $option)) {
            $this->dbMyAsset->order_by($this->table_transportation_fuel . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->dbMyAsset->limit($this->table_transportation_fuel . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->dbMyAsset->order_by($this->table_transportation_fuel . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->dbMyAsset->limit($option['limit'], $page);
            }
        }



        $data_product = $this->dbMyAsset->get($this->table_transportation_fuel)->result();

        $array_data   = array();
        foreach ($data_product as $product) {
            $data_start   = $this->cekStart(1, $product->transportation_fuel_id);
            $data_finish  = $this->cekStart(2, $product->transportation_fuel_id);


            $new_array  = array(
                'data_bbm' => $product,
                'data_start'   => $data_start,
                'data_finish'   => $data_finish
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }



    public function findHsAsset($nik, $result_type, $option = null)
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

        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_date_created");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_id");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".nik");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".full_name as employee_name");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".stop_type_code_fuel");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_name");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_nopol");

        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');
        $this->dbMyAsset->join($this->table_transportation, $this->table_transportation_fuel . '.transportation_fuel_nopol = ' . $this->table_transportation   . '.transportation_nopol', 'left');
        $this->dbMyAsset->join($this->table_transportation_type, $this->table_transportation . '.transportation_type_code = ' . $this->table_transportation_type   . '.transportation_type_code', 'left');

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(transportation_fuel.transportation_fuel_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation_fuel . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation_fuel . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $nik);
            }
        }


        if ($karyawanNik != "") {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $karyawanNik);
        }

        if ($all == 2) {
            $ids = array('STCST');
            $this->dbMyAsset->where_in($this->table_transportation_fuel . '.stop_type_code_fuel', $ids);
        } elseif ($all == 1) {
            $ids = array('STCS', 'TRST', 'STCMBB');
            $this->dbMyAsset->where_in($this->table_transportation_fuel . '.stop_type_code_fuel', $ids);
        }

        if (array_key_exists('order', $option)) {
            $this->dbMyAsset->order_by($this->table_transportation_fuel . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->dbMyAsset->limit($this->table_transportation_fuel . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->dbMyAsset->order_by($this->table_transportation_fuel . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->dbMyAsset->limit($option['limit'], $page);
            }
        }



        $data_product = $this->dbMyAsset->get($this->table_transportation_fuel)->result();

        $array_data   = array();
        foreach ($data_product as $product) {
            $asset        = $this->cekAsset($product->transportation_fuel_nopol);
            $data_start   = $this->cekStart(1, $product->transportation_fuel_id);
            $data_finish  = $this->cekStart(2, $product->transportation_fuel_id);


            $new_array  = array(
                'data_bbm' => $product,
                'data_start'   => $data_start,
                'data_finish'   => $data_finish,
                'asset'   => $asset
            );

            array_push($array_data, $new_array);
        }
        return $array_data;
    }

    public function cekAsset($transportation_fuel_nopol)
    {
        $select = "";

        $this->dbMyAsset->select($this->table_asset . ".asset_serialnumber");
        $this->dbMyAsset->select($this->table_asset . ".asset_detail");
        $this->dbMyAsset->select($this->table_asset . ".asset_location");
        $this->dbMyAsset->where($this->table_asset . '.asset_serialnumber', $transportation_fuel_nopol);

        return $this->dbMyAsset->get($this->table_asset)->row();
    }
    public function findSt($nik, $result_type, $option = null)
    {
        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();
        $subunit_code = $dataNa->subunit_code;
        $select = "";
        $branch  = $this->input->post('branch');
        $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
        $ADMIN  = $this->input->post('ADMIN');
        $KOORDINATOR  = $this->input->post('KOORDINATOR');
        $karyawanNik  = $this->input->post('karyawanNik');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_date_created");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_id");


        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(transportation_fuel.transportation_fuel_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation_fuel . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation_fuel . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $karyawanNik);
        }
        $ids = array('STCS', 'TRST', 'STCMBB');
        $this->dbMyAsset->where_in($this->table_transportation_fuel . '.stop_type_code_fuel', $ids);

        return $this->dbMyAsset->get($this->table_transportation_fuel)->num_rows();
    }

    public function find_not_fin($nik, $result_type, $option = null)
    {
        $select = "";
        $ids = array('STCST');

        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();
        $subunit_code = $dataNa->subunit_code;
        $select = "";
        $branch  = $this->input->post('branch');
        $HUMANCAPITAL  = $this->input->post('HUMANCAPITAL');
        $ADMIN  = $this->input->post('ADMIN');
        $KOORDINATOR  = $this->input->post('KOORDINATOR');
        $karyawanNik  = $this->input->post('karyawanNik');
        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');

        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_date_created");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_id");

        $this->dbMyAsset->order_by($this->table_transportation_fuel . ".transportation_fuel_id", 'DESC');
        // $this->dbMyAsset->join($this->table_employee, $this->table_transportation_fuel . '.nik = ' . $this->table_employee   . '.nik');

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(transportation_fuel.transportation_fuel_date_created) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->dbMyAsset->where('date(' . $this->table_transportation_fuel . '.transportation_fuel_date_created)', $tanggalSearchAkhir);
        }

        if ($HUMANCAPITAL == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
        } elseif ($ADMIN == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation_fuel . '.subunit_code', $subunit_code);
        } elseif ($KOORDINATOR == 1) {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.branch_code', $branch);
            $this->dbMyAsset->where($this->table_transportation_fuel . '.subunit_code', $subunit_code);
        } else {
            if ($karyawanNik == "") {
                $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $nik);
            }
        }

        if ($karyawanNik != "") {
            $this->dbMyAsset->where($this->table_transportation_fuel . '.nik', $karyawanNik);
        }
        $this->dbMyAsset->where_in($this->table_transportation_fuel . '.stop_type_code_fuel', $ids);

        return $this->dbMyAsset->get($this->table_transportation_fuel)->num_rows();
    }

    public function findDetail()
    {
        $select = "";
        $transportation_fuel_id  = $this->input->post('transportation_fuel_id');

        $this->dbMyAsset->select($this->table_areakerja . ".areakerja_name");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_nopol");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_keterangan_area");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_name");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_fuel_cost");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_bbm");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_distance");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_price");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_km");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_date_created");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".nik");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".last_km");

        $this->dbMyAsset->select($this->table_transportation_fuel . ".keterangan");
        $this->dbMyAsset->join($this->table_transportation, $this->table_transportation_fuel . '.transportation_fuel_nopol = ' . $this->table_transportation   . '.transportation_nopol', 'left');
        $this->dbMyAsset->join($this->table_transportation_type, $this->table_transportation . '.transportation_type_code = ' . $this->table_transportation_type   . '.transportation_type_code', 'left');
        $this->dbMyAsset->join($this->table_areakerja, $this->table_transportation . '.transportation_areakerja_id = ' . $this->table_areakerja   . '.areakerja_id', 'left');
        $this->dbMyAsset->where($this->table_transportation_fuel . '.transportation_fuel_id', $transportation_fuel_id);

        return $this->dbMyAsset->get($this->table_transportation_fuel)->row();
    }


    public function findDetailAsset()
    {
        $select = "";
        $transportation_fuel_id  = $this->input->post('transportation_fuel_id');

        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_nopol as transportation_nopol");
        $this->dbMyAsset->select($this->table_transportation . ".transportation_keterangan_area");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".jenis_armada");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_id");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".posisi_kendaraan");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".posisi_kendaraan as areakerja_name");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".jenis_bahan_bakar");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_name");
        $this->dbMyAsset->select($this->table_transportation_type . ".transportation_type_fuel_cost");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_bbm");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_distance");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_price");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_total_km");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".transportation_fuel_date_created");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".nik");
        $this->dbMyAsset->select($this->table_transportation_fuel . ".last_km");

        $this->dbMyAsset->select($this->table_transportation_fuel . ".keterangan");
        $this->dbMyAsset->join($this->table_transportation, $this->table_transportation_fuel . '.transportation_fuel_nopol = ' . $this->table_transportation   . '.transportation_nopol', 'left');
        $this->dbMyAsset->join($this->table_transportation_type, $this->table_transportation . '.transportation_type_code = ' . $this->table_transportation_type   . '.transportation_type_code', 'left');
        $this->dbMyAsset->where($this->table_transportation_fuel . '.transportation_fuel_id', $transportation_fuel_id);

        return $this->dbMyAsset->get($this->table_transportation_fuel)->row();
    }

    public function findDetailTime()
    {
        $select = "";
        $transportation_fuel_id  = $this->input->post('transportation_fuel_id');

        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_date");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_bbm");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_price");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km1");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km2");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_km3");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_foto_struk");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_latitude");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_longitude");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".driverName as employee_name");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".driverSectionCode");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".driverSectionName");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".transportation_fuel_detail_areakerja_name as areakerja_name");
        $this->dbMyAsset->select($this->table_transportation_fuel_detail . ".stop_type_code");
        $this->dbMyAsset->select($this->table_stop_type . ".stop_type_name");
        $this->dbMyAsset->where($this->table_transportation_fuel_detail . '.transportation_fuel_id', $transportation_fuel_id);
        $this->dbMyAsset->join($this->table_stop_type, $this->table_transportation_fuel_detail . '.stop_type_code = ' . $this->table_stop_type   . '.stop_type_code');
        $this->dbMyAsset->order_by($this->table_transportation_fuel_detail . ".transportation_fuel_detail_id", 'ASC');

        return $this->dbMyAsset->get($this->table_transportation_fuel_detail)->result();
    }
}
