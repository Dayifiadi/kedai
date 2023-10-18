<?php
class Mabsensi extends CI_Model
{
    private $table   = "absensi_karyawan";
    private $table_area_kerja                          = "areakerja";
    private $table_employee_areakerja                          = "employee_areakerja";
    private $table_area_kerja_keluar                          = "areakerja ak";
    private $table_pengguna                           = "user";
    private $table_employee                           = "employee";
    private $table_absensi_jadwal                          = "absensi_jadwal";

    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->load->model('Msetting');
    }

    public function findNow($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        // $this->db->or_where('DATE('.$this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findNewNewNa($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');

        $date3 = date('Y-m-d', strtotime(' +1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_date_masuk", 'DESC');
        $this->db->limit(1);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }



    public function findNewNewNaDUlu($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');

        $date3 = date('Y-m-d', strtotime(' +1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".fda_request_id");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        // $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        // $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');
        $this->db->limit(1);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }



    public function findNewNewNaDUluNow($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');

        $date3 = date('Y-m-d', strtotime(' +1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".fda_request_id");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        // $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        // $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');
        $this->db->limit(1);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findSYNC($date, $result_type)
    {
        $select = "";

        $this->db->select($this->table . ".*");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table_employee . ".employee_name");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table_employee . '.branch_code', 'TSM000');
        $this->db->where($this->table . '.nik', $this->input->post('nik'));
        // $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        // $this->db->where($this->table . '.absensikaryawan_id', $date);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findNewNewNaYesNa($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');

        $date3 = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date3);
        $this->db->order_by($this->table . ".absensikaryawan_date_masuk", 'DESC');
        $this->db->limit(1);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }



    public function findNewNewNaKeluarYesNa($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');

        $date3 = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_date_keluar", 'DESC');
        $this->db->limit(1);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findNewNewNaKeluarYesNaBaru($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');

        $date3 = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where($this->table . '.absensi_jadwal_id', NULL);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date3);
        $this->db->order_by($this->table . ".absensikaryawan_date_keluar", 'DESC');
        $this->db->limit(1);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }


    public function findNewNewNaNow($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d');


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }


    public function findByShifting($absensi_jadwal_id)
    {
        $select = "";

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->where($this->table . '.absensi_jadwal_id', $absensi_jadwal_id);
        return $this->db->get($this->table)->row();
    }


    public function findKelYes($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }


    public function findNewNewNaYes($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".fda_request_id");

        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findShiftingById($absensiJadwalId)
    {

        $this->db->where($this->table_absensi_jadwal . '.absensi_jadwal_id', $absensiJadwalId);

        return $this->db->get($this->table_absensi_jadwal)->row();
    }

    public function findShiftingMasuk($nik, $result_type)
    {
        $date = date('Y-m-d');
        // $date = date('2021-12-21');


        $this->db->where($this->table_absensi_jadwal . '.nik', $nik);
        $this->db->where('DATE(' . $this->table_absensi_jadwal . '.absensi_jadwal_tanggal)', $date);

        if ($result_type == "row") {
            return $this->db->get($this->table_absensi_jadwal)->row();
        } else {
            return $this->db->get($this->table_absensi_jadwal)->result();
        }
    }

    public function findShiftingYes($nik, $result_type)
    {
        $date = date('Y-m-d', strtotime(' -1 day'));
        // $date = date('2021-12-21');


        $this->db->where($this->table_absensi_jadwal . '.nik', $nik);
        $this->db->where('DATE(' . $this->table_absensi_jadwal . '.absensi_jadwal_tanggal)', $date);

        if ($result_type == "row") {
            return $this->db->get($this->table_absensi_jadwal)->row();
        } else {
            return $this->db->get($this->table_absensi_jadwal)->result();
        }
    }
    public function findShiftingYesAll($result_type)
    {
        $date = date('Y-m-d', strtotime(' -1 day'));
        $dateNew = date('Y-m-d', strtotime(' -3 day'));

        // $this->db->where('DATE(' . $this->table_absensi_jadwal . '.axbsensi_jadwal_tanggal) <', $date);
        $this->db->where('date(absensi_jadwal.absensi_jadwal_tanggal) between "' . $dateNew . '" and "' . $date . '"');

        if ($result_type == "row") {
            return $this->db->get($this->table_absensi_jadwal)->row();
        } else {
            return $this->db->get($this->table_absensi_jadwal)->result();
        }
    }
    public function findNow1($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findNow2($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');
        $date2 = date('Y-m-d', strtotime(' -1 day'));


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date2);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findNow3($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');
        $date2 = date('Y-m-d', strtotime(' -1 day'));


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date2);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findNowNew($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', null);
        // $this->db->or_where('DATE('.$this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findYesNew($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', null);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findKelNew($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d');

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', null);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findKelYesNew($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', null);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findNowKelaur($nik, $result_type)
    {
        $select = "";
        $day = date('N');
        $date = date('Y-m-d');
        // $date=date('2021-06-20');


        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');


        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findYes($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));
        // $date=date('2021-06-19', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        // $this->db->or_where('DATE('.$this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findYesMasuk($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_masuk)', $date);
        // $this->db->or_where('DATE('.$this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }

    public function findYesKel($nik, $result_type)
    {
        $select = "";
        $date = date('Y-m-d', strtotime(' -1 day'));

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".foto_status");

        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where('DATE(' . $this->table . '.absensikaryawan_date_keluar)', $date);
        $this->db->order_by($this->table . ".absensikaryawan_id", 'DESC');

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }


    public function findHistory($nik, $result_type, $option = null)
    {
        $select = "";

        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $tanggalSearchAkhir   = $this->input->post('tanggalSearchAkhir');
        $karyawanNik   = $this->input->post('karyawanNik');

        $this->db->select($this->table . ".absensikaryawan_areakerja_masuk");
        $this->db->select($this->table . ".absensikaryawan_areakerja_keluar");
        $this->db->select($this->table . ".absensikaryawan_id");
        $this->db->select("ak.areakerja_name as nama_area_keluar");
        $this->db->select($this->table_area_kerja . ".areakerja_name as nama_area_masuk");
        $this->db->select($this->table . ".absensikaryawan_date_masuk as tanggal_absensi");
        $this->db->select($this->table . ".absensikaryawan_date_keluar as tanggal_absensi_keluar");
        $this->db->select($this->table . ".absensikaryawan_time_masuk");
        $this->db->select($this->table . ".absensikaryawan_time_keluar");
        $this->db->select($this->table . ".absensikaryawan_ket_masuk");
        $this->db->select($this->table . ".absensikaryawan_ket_keluar");
        $this->db->select($this->table . ".absensikaryawan_photo_masuk");
        $this->db->select($this->table . ".absensikaryawan_photo_keluar");
        $this->db->select($this->table . ".absensikaryawan_latitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_longitude_masuk");
        $this->db->select($this->table . ".absensikaryawan_latitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".absensikaryawan_longitude_keluar");
        $this->db->select($this->table . ".status_absen_masuk");
        $this->db->select($this->table . ".status_absen_keluar");
        $this->db->select($this->table . ".keterangan_absen_masuk");
        $this->db->select($this->table . ".keterangan_absen_keluar");
        $this->db->select($this->table . ".foto_status");
        $this->db->select($this->table . ".absensi_jadwal_id");
        $this->db->select($this->table . ".fda_request_id");
        $this->db->select($this->table_employee . ".nik");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->join($this->table_area_kerja, $this->table . '.absensikaryawan_areakerja_masuk = ' . $this->table_area_kerja   . '.areakerja_id', 'left');
        $this->db->join($this->table_area_kerja_keluar, $this->table . '.absensikaryawan_areakerja_keluar = ak.areakerja_id', 'left');
        $this->db->join($this->table_pengguna, $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->join($this->table_employee, $this->table . '.nik = ' . $this->table_employee   . '.nik');
        $this->db->order_by($this->table . ".absensikaryawan_date_masuk", 'DESC');
        $this->db->group_by($this->table . ".absensikaryawan_id");

        if ($tanggalSearchAwal != null && $tanggalSearchAkhir != null) {
            $this->db->where('date(absensi_karyawan.absensikaryawan_date_masuk) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
            // $this->db->or_where('date(absensi_karyawan.absensikaryawan_date_keluar) between "' . $tanggalSearchAwal . '" and "' . $tanggalSearchAkhir . '"');
        } elseif ($tanggalSearchAwal != null) {
            $this->db->where('date(' . $this->table . '.absensikaryawan_date_masuk)', $tanggalSearchAwal);
            // $this->db->or_where('date('.$this->table . '.absensikaryawan_date_keluar)', $tanggalSearchAwal);
        } elseif ($tanggalSearchAkhir != null) {
            $this->db->where('date(' . $this->table . '.absensikaryawan_date_masuk)', $tanggalSearchAkhir);
            // $this->db->or_where('date('.$this->table . '.absensikaryawan_date_keluar)', $tanggalSearchAkhir);
        }
        if ($karyawanNik != '') {
            if ($karyawanNik == 'All Karyawan') {
            } else {
                $this->db->where($this->table . '.nik', $karyawanNik);
            }
        } else {
            $this->db->where($this->table . '.nik', $nik);
        }
        if (array_key_exists('order', $option)) {
            $this->db->order_by($this->table . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->db->limit($this->table . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->db->order_by($this->table . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->db->limit($option['limit'], $page);
            }
        }

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }


    public function find_all_jadwal($nik, $result_type, $option = null)
    {
        $select = "";

        $tanggalSearchAwal   = $this->input->post('tanggalSearchAwal');
        $karyawanNik   = $this->input->post('karyawanNik');


        $this->db->select($this->table_absensi_jadwal . ".*");
        $this->db->select($this->table_employee . ".nik");
        $this->db->select($this->table_employee . ".employee_name");
        $this->db->join($this->table_employee, $this->table_absensi_jadwal . '.nik = ' . $this->table_employee   . '.nik', 'left');

        if ($karyawanNik != '') {
            if ($karyawanNik == 'All Karyawan') {
            } else {
                $this->db->where($this->table_absensi_jadwal . '.nik', $karyawanNik);
            }
        } else {
            $this->db->where($this->table_absensi_jadwal . '.nik', $nik);
        }
        if (array_key_exists('order', $option)) {
            $this->db->order_by($this->table_absensi_jadwal . "." . $option['order']['order_by'], $option['order']['ordering']);
        }
        if ($option != null) {
            if (array_key_exists('limit', $option)) {
                $this->db->limit($this->table_absensi_jadwal . "." . $option['limit']);
            }

            if (array_key_exists('order_by', $option)) {
                $this->db->order_by($this->table_absensi_jadwal . "." . $option['order_by']['field'], $option['order_by']['option']);
            }

            if (array_key_exists('page', $option)) {
                $page = $option['page'] * $option['limit'];
                $this->db->limit($option['limit'], $page);
            }
        }

        if ($result_type == "row") {
            return $this->db->get($this->table_absensi_jadwal)->row();
        } else {
            return $this->db->get($this->table_absensi_jadwal)->result();
        }
    }

    public function checkGlobal($nik, $branch, $isGlobal)
    {
        $select = "";

        $this->db->select($this->table_area_kerja . ".areakerja_id");
        $this->db->select($this->table_area_kerja . ".is_global");
        $this->db->select($this->table_area_kerja . ".branch_code");
        $this->db->join($this->table_area_kerja, $this->table_employee_areakerja . '.areakerja_id = ' . $this->table_area_kerja   . '.areakerja_id');
        $this->db->where($this->table_employee_areakerja . '.nik', $nik);
        $this->db->where($this->table_area_kerja . '.is_global', $isGlobal);
        $this->db->where($this->table_area_kerja . '.branch_code', $branch);
        return $this->db->get($this->table_employee_areakerja)->row();
    }

    public function create($data)
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
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
    public function checkJarak($latitude_user, $longitude_user, $nik)
    {
        $query = "SELECT ae.areakerja_id,ae.areakerja_name,ae.max_distance,(6371 * acos( cos( radians(" . $latitude_user . ") ) * cos( radians( ae.areakerja_latitude ) ) *
            cos( radians( ae.areakerja_longitude ) - radians(" . $longitude_user . ") ) + sin( radians(" . $latitude_user . ") ) *
            sin( radians( ae.areakerja_latitude ) ) ) ) AS distance
   FROM areakerja ae LEFT JOIN employee_areakerja eae
  ON eae.areakerja_id=ae.areakerja_id WHERE eae.nik='" . $nik . "' having distance < ae.max_distance";
        $result = $this->db->query($query)->row();
        return $result;
    }

    public function checkJarakGlobal($branch, $latitude_user, $longitude_user)
    {
        $query = "SELECT ae.areakerja_id,ae.areakerja_name,ae.max_distance,(6371 * acos( cos( radians(" . $latitude_user . ") ) * cos( radians( ae.areakerja_latitude ) ) *
        cos( radians( ae.areakerja_longitude ) - radians(" . $longitude_user . ") ) + sin( radians(" . $latitude_user . ") ) *
        sin( radians( ae.areakerja_latitude ) ) ) ) AS distance
FROM areakerja ae WHERE ae.is_global=1 and ae.branch_code='" . $branch . "'";
        $result = $this->db->query($query)->row();
        return $result;
    }

    public function checkJarakGlobalArea($branch, $latitude_user, $longitude_user)
    {
        $query = "SELECT ae.areakerja_id,ae.areakerja_name,ae.max_distance,(6371 * acos( cos( radians(" . $latitude_user . ") ) * cos( radians( ae.areakerja_latitude ) ) *
        cos( radians( ae.areakerja_longitude ) - radians(" . $longitude_user . ") ) + sin( radians(" . $latitude_user . ") ) *
        sin( radians( ae.areakerja_latitude ) ) ) ) AS distance
FROM areakerja ae where ae.branch_code='" . $branch . "' having distance < ae.max_distance";
        $result = $this->db->query($query)->row();
        return $result;
    }

    public function checkJarakGlobalAreaNew($branch, $latitude_user, $longitude_user)
    {
        $query = "SELECT ae.areakerja_id,ae.areakerja_name,ae.max_distance,(6371 * acos( cos( radians(" . $latitude_user . ") ) * cos( radians( ae.areakerja_latitude ) ) *
        cos( radians( ae.areakerja_longitude ) - radians(" . $longitude_user . ") ) + sin( radians(" . $latitude_user . ") ) *
        sin( radians( ae.areakerja_latitude ) ) ) ) AS distance
FROM areakerja ae where ae.branch_code='" . $branch . "' order by distance ASC";
        $result = $this->db->query($query)->row();
        return $result;
    }



    public function checkJarakPic($latitude_user, $longitude_user)
    {
        $query = "SELECT ae.areakerja_id,ae.areakerja_name,ae.max_distance,(6371 * acos( cos( radians(" . $latitude_user . ") ) * cos( radians( ae.areakerja_latitude ) ) *
        cos( radians( ae.areakerja_longitude ) - radians(" . $longitude_user . ") ) + sin( radians(" . $latitude_user . ") ) *
        sin( radians( ae.areakerja_latitude ) ) ) ) AS distance
FROM areakerja ae having distance < ae.max_distance";
        $result = $this->db->query($query)->row();
        return $result;
    }
}
