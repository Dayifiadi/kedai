<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class BBM extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MBbm');
        $this->load->model('Mabsensi');
        $this->load->model('Maktivitas');
        $this->dbMyAsset = $this->load->database('dbMyAsset', TRUE);


        // // ----------------- Validasi Header ------------------------- //
        // $dt             = new DateTime(null, new DateTimeZone("UTC"));
        // $timestampnow   = $dt->getTimestamp();  $timestamp ='';
        // $header         = apache_request_headers();
        // foreach ($header as $headers => $value) {
        //     if($headers == WAKTU){ $timestamp   = $value; }
        //     if($headers == LOGIN){ $Auth        = $value; }
        // }
        // //ubah format timestamp ke ymd
        // $tglsekarang    = substr(date(FORMAT_DATE, $timestampnow) ,0,-9);
        // $tglparams      = substr(date(FORMAT_DATE, $timestamp) ,0,-9);
        // $get_user       = $this->db->query(AMBIL_KETIKA_VALIDASI.substr(base64_decode($Auth), 0, -26).SELESAI);
        //
        // $key = [];
        // foreach ($get_user->result() as $row){
        //        $nik      = $row->nik;
        //        $key[]    = base64_encode($nik.ENC_KEY.$timestamp);
        // }
        // $AUTH_PARTNER   = json_encode($key);
        //
        // if(empty($timestamp) || empty($Auth)){  //cek header
        //     redirect(site_url(HEADER_NOT_COMPLETE));
        // }else{
        //     if($tglsekarang == $tglparams){ //validasi waktu\
        //         if (in_array($Auth, json_decode($AUTH_PARTNER))){ //Validasi Auth
        //             // Auth Sukses, function langsung di eksekusi
        //         }else{
        //              redirect(site_url(AUTH_FAILED));
        //         }
        //     }else{
        //          redirect(site_url(TIME_DOESNT_MATCH));
        //     }
        // }
        // // +++++++++++++++ Validasi Header ++++++++++++++++++ //

    }

    public function find_nopol_post()
    {
        $arr_result = array();
        $branch  = $this->input->post('branch');
        if ($branch == 'BDO000') {
            $order_by            = 'asset_id';
        } else {
            $order_by            = 'transportation_nopol';
        }
        $ordering            = "ASC";
        $limit               = 100;
        $page                = 0;

        if (isset($_POST['order_by'])) {
            $order_by = $this->input->post('order_by');
        }

        if (isset($_POST['ordering'])) {
            $ordering = $this->input->post('ordering');
        }

        if (isset($_POST['limit'])) {
            $limit = $this->input->post('limit');
        }

        if (isset($_POST['page'])) {
            $page = $this->input->post('page');
        }


        $option = array(
            'limit' => $limit,
            'page'  => $page,
            'order' => array(
                'order_by' => $order_by,
                'ordering' => $ordering,
            ),
        );

        if ($branch == 'BDO000') {
            $data_user = $this->MBbm->cekNopolAsset($option);
        } else {
            $data_user = $this->MBbm->cekNopol($option);
        }
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }


    public function find_nopol_new_post()
    {
        $arr_result = array();
        $branch  = $this->input->post('branch');
        // if ($branch == 'BDO000') {
        $order_by            = 'asset_id';
        // } else {
        //   $order_by            = 'transportation_nopol';
        // }
        $ordering            = "ASC";
        $limit               = 100;
        $page                = 0;

        if (isset($_POST['order_by'])) {
            $order_by = $this->input->post('order_by');
        }

        if (isset($_POST['ordering'])) {
            $ordering = $this->input->post('ordering');
        }

        if (isset($_POST['limit'])) {
            $limit = $this->input->post('limit');
        }

        if (isset($_POST['page'])) {
            $page = $this->input->post('page');
        }


        $option = array(
            'limit' => $limit,
            'page'  => $page,
            'order' => array(
                'order_by' => $order_by,
                'ordering' => $ordering,
            ),
        );

        // if ($branch == 'BDO000') {
        $data_user = $this->MBbm->cekNopolAsset($option);
        // } else {
        //   $data_user = $this->MBbm->cekNopol($option);
        // }
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }


    public function findNopDet_post()
    {
        $arr_result = array();

        if ($this->input->post('branch') == 'BDO000') {
            $data_user = $this->MBbm->cekNopolDetAsset();
        } else {
            $data_user = $this->MBbm->cekNopolDet();
        }

        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }


    public function findNewNopDet_post()
    {
        $arr_result = array();
        // if ($this->input->post('branch') == 'BDO000') {
        $data_user = $this->MBbm->cekNopolDetAsset();
        $dataZone = $this->MBbm->cekZone(@$data_user->zone_code);
        // } else {
        //   $data_user = $this->MBbm->cekNopolDet();
        // }

        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  @$data_user,
                    'dataZone'    =>  @$dataZone,
                )
            );
        }


        print json_encode($arr_result);
    }

    public function find_stop_post()
    {
        $arr_result = array();

        $data_user = $this->MBbm->cekStop();
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }


    public function stp_new_post()
    {
        $arr_result = array();

        $data_user = $this->MBbm->cekStopNew();
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }

    public function stop_post()
    {
        $arr_result = array();

        $data_user = $this->MBbm->stop();
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }

    public function find_detail_post()
    {
        $arr_result = array();
        $transportation_fuel_id  = $this->input->post('transportation_fuel_id');
        $branch  = $this->input->post('branch');

        $data_user = $this->MBbm->findDetailAsset();


        $data_first = $this->MBbm->ambilFirst($transportation_fuel_id);
        $data_time = $this->MBbm->findDetailTime();
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                    'data_first'    =>  $data_first,
                    'data_time'    =>  $data_time,
                )
            );
        }


        print json_encode($arr_result);
    }

    public function detail_new_post()
    {
        $arr_result = array();
        $transportation_fuel_id  = $this->input->post('transportation_fuel_id');

        $data_user = $this->MBbm->findDetailAsset();

        $data_first = $this->MBbm->ambilFirst($transportation_fuel_id);
        $data_time = $this->MBbm->findDetailTime();
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                    'data_first'    =>  $data_first,
                    'data_time'    =>  $data_time,
                )
            );
        }


        print json_encode($arr_result);
    }
    public function delete_post()
    {
        $arr_result = array();
        $data['transportation_fuel_id'] = $this->input->post('idNa');

        $delete           = $this->db->delete('transportation_fuel', $data);
        $deleteDetail     = $this->db->delete('transportation_fuel_detail', $data);
        if ($delete && $deleteDetail) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal menghapus data.',
                )
            );
        }


        print json_encode($arr_result);
    }

    public function start_post()
    {
        $arr_result = array();

        $data_user = $this->MBbm->cekStart(1);
        if (empty($data_user)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                )
            );
        }


        print json_encode($arr_result);
    }

    public function find_history_new_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'transportation_fuel_id';
        $ordering            = "desc";
        $limit               = 15;
        $page                = 0;

        if (isset($_POST['order_by'])) {
            $order_by = $this->input->post('order_by');
        }

        if (isset($_POST['ordering'])) {
            $ordering = $this->input->post('ordering');
        }

        if (isset($_POST['limit'])) {
            $limit = $this->input->post('limit');
        }

        if (isset($_POST['page'])) {
            $page = $this->input->post('page');
        }


        $option = array(
            'limit' => $limit,
            'page'  => $page,
            'order' => array(
                'order_by' => $order_by,
                'ordering' => $ordering,
            ),
        );
        $branch  = $this->input->post('branch');
        // if ($branch == 'BDO000') {
        $data_aktivitas = $this->MBbm->findHsAsset($nik, 'result', $option);
        $data_stop = $this->MBbm->findSt($nik, 'result', $option);
        $data_not_stop = $this->MBbm->find_not_fin($nik, 'result', $option);
        // } else {
        //   $data_aktivitas = $this->MBbm->findHs($nik, 'result', $option);
        //   $data_stop = $this->MBbm->findSt($nik, 'result', $option);
        //   $data_not_stop = $this->MBbm->find_not_fin($nik, 'result', $option);
        // }

        $arr_result = array();
        if (empty($data_aktivitas)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas ditemukan.',
                    'data_aktivitas'     => $data_aktivitas,
                    'data_stop'     => $data_not_stop,
                    'data_not_stop'     => $data_stop,
                )
            );
        }

        print json_encode($arr_result);
    }

    public function get_bbm_post()
    {
        $arr_result = array();

        $harga        = $this->dbMyAsset->get_where('petrol')->result();

        if (empty($harga)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal mengambil data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $harga,
                )
            );
        }


        print json_encode($arr_result);
    }
    public function create_post()
    {
        $nik  = $this->input->post('nik');
        $nopol  = $this->input->post('nopol');
        $km  = $this->input->post('km');
        $kmYes  = $this->input->post('kmYes');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $isSudahNa  = $this->input->post('isSudahNa');
        $keterangan  = $this->input->post('keterangan');
        $keterangan_ops  = $this->input->post('keterangan_ops');
        $jenis_armada  = $this->input->post('jenis_armada');
        $posisi_kendaraan  = $this->input->post('posisi_kendaraan');
        $bbmNA  = $this->input->post('bbmNA');
        $activityKategoriId  = $this->input->post('activityKategoriId');

        $harga = '';
        // if ($bbmNA == 'PERTALITE') {
        //   $harga = '7650';
        // } elseif ($bbmNA == 'SOLAR') {
        //   $harga = '5150';
        // } else {
        //   $harga = '7650';
        // }
        // @$harga        = $this->dbMyAsset->get_where('petrol', ['petrol_name' => @$bbmNA])->row()->petrol_price;
        @$harga        = $this->dbMyAsset->get_where('petrol', ['petrol_name' => "PERTALITE"])->row()->petrol_price;

        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();
        if ($employee->subunit_code) {
            $subunit_code = $employee->subunit_code;
        } else {
            $subunit_code = '';
        }
        if ($employee->unit_code) {
            $unit_code = $employee->unit_code;
        } else {
            $unit_code = '';
        }
        $isError = false;

        if ($this->input->post('km') < $this->input->post('kmYes')) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Kilometer Dashboard yang anda inputkan tidak sesuai!.',
                )
            );
            $isError = true;
            print json_encode($arr_result);
            return TRUE;
        } else {
            $isError = false;
        }
        if ($isError == false) {
            if ($employee->branch_code == null) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki BRANCH!.',
                    )
                );
            } else {

                if ($isSudahNa == 1) {
                    $fileName = date('Ymd') . $this->input->post('fileName');
                    $fileImage = $this->input->post('fileImage');
                    $binaryImage = base64_decode($fileImage);
                    header('Content-Type: bitmap; charset=utf-8');
                    //gambar akan disimpan pada folder image
                    $fileGAMBAR = fopen('../uploads/image_aktivitas/' . $fileName, 'wb');
                    // Create File
                    fwrite($fileGAMBAR, $binaryImage);
                    fclose($fileGAMBAR);
                    $binryImage = base64_decode($fileImage);
                } else {
                    $fileName = "";
                }
                $chekjarak = $this->Maktivitas->checkJarakGlobalArea($employee->branch_code, $latitude, $longitude);
                if (empty($chekjarak)) {
                    $areakerja_id = 0;
                } else {
                    $areakerja_id = $chekjarak->areakerja_id;
                }
                if ($this->input->post('isSudahAda') == 0) {

                    $section               = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();

                    $dataAktivitas['nik']         = $nik;
                    $dataAktivitas['activity_kategory_id']         = $activityKategoriId;
                    $dataAktivitas['activity_deskripsi_aktivitas']         = $keterangan_ops;
                    $dataAktivitas['activity_date_created']         = date('Y-m-d H:i:s');
                    $dataAktivitas['activity_latitude']         = $latitude;
                    $dataAktivitas['activity_longitude']         = $longitude;
                    $dataAktivitas['activity_foto']         = $fileName;
                    $dataAktivitas['activity_areakerja']         = $areakerja_id;
                    $dataAktivitas['activity_type']         = "DAILY DRIVER";
                    $dataAktivitas['no_plat']         = $nopol;
                    $dataAktivitas['status_armada']         = $this->input->post('dataAktivitasKelayakan');
                    $dataAktivitas['km_armada']         = $this->input->post('km');
                    $dataAktivitas['full_name']         = $employee->employee_name;
                    $dataAktivitas['section_code']         = $employee->section_code;
                    $dataAktivitas['branch_code']         = $employee->branch_code;
                    $dataAktivitas['unit_code']         = @$unit_code;
                    $dataAktivitas['subunit_code']         = @$subunit_code;
                    $dataAktivitas['section_name']         = $section->section_name;
                    $dataAktivitas['areakerja_name']         = $chekjarak->areakerja_name;

                    $aktivitas = $this->Maktivitas->create($dataAktivitas);
                    $listCekDesc  = $this->input->post('listCekName');
                    $listCekId  = $this->input->post('listCekId');
                    $descCek      = explode('#', $listCekDesc, -1);
                    $idCek  = explode('#', $listCekId, -1);

                    $x = 0;
                    foreach ($idCek as $idCek) {
                        $dataCek['activity_repair_kategory_id']              = $idCek;
                        $dataCek['activity_id']                              = $aktivitas;
                        $dataCek['activity_repair_type']                     = $descCek[$x];

                        $this->dbMyAsset->insert('activity_repair', $dataCek);
                        $x++;
                    }
                } else {
                    $aktivitas  = $this->input->post('activityId');
                }


                $data['nik']      = $nik;
                $data['transportation_fuel_date_created'] = date('Y-m-d H:i:s');
                $data['transportation_fuel_nopol']        = $nopol;
                $data['transportation_fuel_total_bbm']        = 0;
                $data['transportation_fuel_total_distance']        = 0;
                $data['transportation_fuel_total_price']        = 0;
                $data['transportation_fuel_total_km']        = $km;
                $data['transportation_keterangan_area']        = @$keterangan;
                $data['keterangan']        = $keterangan_ops;
                $data['stop_type_code_fuel']      = 'STCS';
                $data['last_km'] = $km;
                $data['jenis_armada'] = @$jenis_armada;
                $data['posisi_kendaraan'] = @$posisi_kendaraan;
                $data['jenis_bahan_bakar'] = @$harga;
                $data['full_name']         = @$employee->employee_name;
                $data['section_code']         = @$employee->section_code;
                $data['branch_code']         = @$employee->branch_code;
                $data['unit_code']         = @$unit_code;
                $data['subunit_code']         = @$subunit_code;
                $data['section_name']         = @$section->section_name;



                $this->dbMyAsset->insert('transportation_fuel', $data);
                $insert_id = $this->dbMyAsset->insert_id();
                // $dataNna['transportation_is_running']      = 1;
                // $whereNa['transportation_nopol']        = $nopol;
                // $this->dbMyAsset->update('transportation', $dataNna,$whereNa);




                $dataDetail['transportation_fuel_id']      = $insert_id;
                $dataDetail['stop_type_code']      = 'STCS';
                $dataDetail['transportation_fuel_detail_date'] = date('Y-m-d H:i:s');
                $dataDetail['transportation_fuel_detail_areakerja_id'] = $areakerja_id;
                $dataDetail['transportation_fuel_detail_latitude'] = $latitude;
                $dataDetail['transportation_fuel_detail_longitude'] = $longitude;
                $dataDetail['transportation_fuel_detail_bbm'] = 0;
                $dataDetail['transportation_fuel_detail_price'] = 0;
                $dataDetail['transportation_fuel_detail_distance'] = 0;
                $dataDetail['transportation_fuel_detail_km'] = $km;
                $dataDetail['transportation_fuel_detail_foto_km'] = $fileName;
                $dataDetail['transportation_fuel_detail_foto_struk'] = @$fileNameStruk;
                $dataDetail['activity_id'] = $aktivitas;
                $dataDetail['transportation_fuel_detail_areakerja_name']         = $chekjarak->areakerja_name;
                $dataDetail['transportation_fuel_detail_jenis_bbm'] = @$bbmNA;
                $dataDetail['transportation_fuel_detail_price_jenis_bbm'] = @$harga;

                if ($this->dbMyAsset->insert('transportation_fuel_detail', $dataDetail)) {

                    $dataNna['transportation_fuel_id']      = @$insert_id;
                    $whereNa['activity_id']        = @$aktivitas;
                    $this->dbMyAsset->update('activity', $dataNna, $whereNa);

                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'success',
                            'message' => 'BBM Armada Berhasil disimpan.',
                            'idNa' => $insert_id,

                        )
                    );
                } else {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Gagal disimpan.',
                        )
                    );
                }
            }
            print json_encode($arr_result);
        }
    }

    public function update_post()
    {
        $idNa  = $this->input->post('idNa');
        $km  = $this->input->post('km');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $isSudahNa  = $this->input->post('isSudahNa');
        $totalBBM  = $this->input->post('totalBBM');
        $totalHargaBBM  = $this->input->post('totalHargaBBM');
        $totalDistance  = $this->input->post('totalDistance');
        $totalKm  = $this->input->post('totalKm');
        $nopol  = $this->input->post('nopol');
        $realLiter  = $this->input->post('realLiter');
        $stopTypeCode  = $this->input->post('stopTypeCode');
        $nik  = $this->input->post('nik');
        $rpReal  = $this->input->post('rpReal');
        $bbmNA  = $this->input->post('bbmNA');
        $activityKategoriId  = $this->input->post('activityKategoriId');
        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();
        if ($employee->subunit_code) {
            $subunit_code = $employee->subunit_code;
        } else {
            $subunit_code = '';
        }
        if ($employee->unit_code) {
            $unit_code = $employee->unit_code;
        } else {
            $unit_code = '';
        }
        if ($latitude == null || $longitude == null || $latitude == 0 || $longitude == 0  || $latitude == "null" || $longitude == "null" || $latitude == "" || $longitude == "") {
            $areakerja_id = 0;
        } else {
            if ($stopTypeCode == 'STCST') {
                $chekjarak = $this->Mabsensi->checkJarakGlobal(@$employee->branch_code, @$latitude, @$longitude);
            } else {
                $chekjarak = $this->Mabsensi->checkJarakGlobalArea(@$employee->branch_code, @$latitude, @$longitude);
            }
            if (empty($chekjarak)) {
                $areakerja_id = 0;
            } else {
                $areakerja_id = $chekjarak->areakerja_id;
            }
        }
        $harga = '';
        // if ($bbmNA == 'PERTALITE') {
        //   $harga = '7650';
        // } elseif ($bbmNA == 'SOLAR') {
        //   $harga = '5150';
        // } else {
        //   $harga = '7650';
        // }
        $harga        = $this->dbMyAsset->get_where('petrol', ['petrol_name' => $bbmNA])->row()->petrol_price;
        $isError = 0;
        if ($isSudahNa == 1) {
            if ($this->input->post('fileName') != null && $this->input->post('fileImage') != null) {
                $fileName = date('Ymd') . $this->input->post('fileName');
                $fileImage = $this->input->post('fileImage');
                $binaryImage = base64_decode(@$fileImage);
                header('Content-Type: bitmap; charset=utf-8');
                //gambar akan disimpan pada folder image
                $fileGAMBAR = fopen('../uploads/image_aktivitas/' . @$fileName, 'wb');
                // Create File
                fwrite($fileGAMBAR, $binaryImage);
                fclose($fileGAMBAR);
                $isError = 0;
            } else {
                $fileName = "";
                $isError = 1;
            }
            $fileNameStruk = "";
        } elseif ($isSudahNa == 2) {
            if ($this->input->post('fileName') != null && $this->input->post('fileImage') != null) {
                $fileName = date('Ymd') . $this->input->post('fileName');
                $fileImage = $this->input->post('fileImage');
                $binaryImage = base64_decode(@$fileImage);
                header('Content-Type: bitmap; charset=utf-8');
                //gambar akan disimpan pada folder image
                $fileGAMBAR = fopen('../uploads/image_aktivitas/' . @$fileName, 'wb');
                // Create File
                fwrite($fileGAMBAR, $binaryImage);
                fclose($fileGAMBAR);
                $isError = 0;
            } else {
                $fileName = "";
                $isError = 1;
            }

            if ($this->input->post('fileNameStruk') != null && $this->input->post('fileImageStruk') != null) {
                $fileNameStruk = date('Ymd') . $this->input->post('fileNameStruk');
                $fileImageStruk = $this->input->post('fileImageStruk');
                $binaryImage2 = base64_decode($fileImageStruk);
                header('Content-Type: bitmap; charset=utf-8');
                //gambar akan disimpan pada folder image
                $fileGAMBAR2 = fopen('../uploads/image_aktivitas/' . $fileNameStruk, 'wb');
                // Create File
                fwrite($fileGAMBAR2, $binaryImage2);
                fclose($fileGAMBAR2);
                $isError = 0;
            } else {
                $isError = 2;
                $fileNameStruk = "";
            }
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Harap Upload Foto Kilometer pada Dashboard Mobil & Struk BBM Anda!.',
                )
            );
        }

        if ($isError == 1) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Harap Upload Foto Kilometer pada Dashboard Mobil Anda!.',
                )
            );
        } elseif ($isError == 2) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Harap Upload Foto Struk BBM Anda!.',
                )
            );
        } else {
            if ($stopTypeCode == null) {
                $stopTypeCode = 'STCMBB';
            }
            if ($stopTypeCode == null) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Silahkan pilih lagi jenis pemberhentian!.',
                    )
                );
            } else {
                if ($this->input->post('isSudahAda') == 1) {

                    $section               = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();

                    $dataAktivitas['nik']         = $nik;
                    $dataAktivitas['activity_kategory_id']         = $activityKategoriId;
                    $dataAktivitas['activity_date_created']         = date('Y-m-d H:i:s');
                    $dataAktivitas['activity_latitude']         = $latitude;
                    $dataAktivitas['activity_longitude']         = $longitude;
                    $dataAktivitas['activity_foto']         = $fileName;
                    $dataAktivitas['activity_areakerja']         = $areakerja_id;
                    $dataAktivitas['activity_type']         = "DAILY DRIVER";
                    $dataAktivitas['no_plat']         = $nopol;
                    $dataAktivitas['status_armada']         = $this->input->post('dataAktivitasKelayakan');
                    $dataAktivitas['km_armada']         = $this->input->post('km');
                    $dataAktivitas['full_name']         = $employee->employee_name;
                    $dataAktivitas['section_code']         = $employee->section_code;
                    $dataAktivitas['branch_code']         = $employee->branch_code;
                    $dataAktivitas['unit_code']         = @$unit_code;
                    $dataAktivitas['subunit_code']         = @$subunit_code;
                    $dataAktivitas['section_name']         = $section->section_name;
                    $dataAktivitas['areakerja_name']         = $chekjarak->areakerja_name;

                    $aktivitas = $this->Maktivitas->create($dataAktivitas);
                    $listCekDesc  = $this->input->post('listCekName');
                    $listCekId  = $this->input->post('listCekId');
                    $descCek      = explode('#', $listCekDesc, -1);
                    $idCek  = explode('#', $listCekId, -1);

                    $x = 0;
                    foreach ($idCek as $idCek) {
                        $dataCek['activity_repair_kategory_id']              = $idCek;
                        $dataCek['activity_id']                              = $aktivitas;
                        $dataCek['activity_repair_type']                     = $descCek[$x];

                        $this->dbMyAsset->insert('activity_repair', $dataCek);
                        $x++;
                    }
                } else {
                    $aktivitas  = $this->input->post('activityId');
                }

                if ($stopTypeCode == 'STCST') {
                    $harga_bensin = 0;
                    $kmNa = 0;
                    $lastKM = $this->MBbm->ambilLastKmNa(@$idNa);
                    $firstKM = $this->MBbm->ambilFirst($idNa);
                    if (empty($lastKM)) {
                        $kmNa = 0;
                    } else {
                        $kmNa = $lastKM->transportation_fuel_detail_km;
                    }
                    $kmNow = $km - $kmNa;
                    $realLiter = 0;
                    $dataNna['transportation_is_running']      = 0;
                    $whereNa['transportation_nopol']        = $nopol;
                    $this->db->update('transportation', $dataNna, $whereNa);
                } elseif ($stopTypeCode == 'STCMBB') {
                    $lastKM = $this->MBbm->ambilLastKmNa(@$idNa);
                    $firstKM = $this->MBbm->ambilFirst(@$idNa);
                    if (empty($lastKM)) {
                        $kmNa = 0;
                    } else {
                        $kmNa = $lastKM->transportation_fuel_detail_km;
                    }
                    if ($totalBBM == null) {
                        $totalBBM = 0;
                    }
                    if ($totalHargaBBM == null) {
                        $totalHargaBBM = 0;
                    }

                    $kmNow = $km - $kmNa;
                    if ($rpReal == null) {
                        $rpReal = 0;
                    }
                    $harga_bensin = @$rpReal;
                    $realLiter = @$realLiter;
                } else {
                    $harga_bensin = 0;
                    $kmNa = 0;
                    $lastKM = $this->MBbm->ambilLastKmNa(@$idNa);
                    $firstKM = $this->MBbm->ambilFirst($idNa);
                    if (empty($lastKM)) {
                        $kmNa = 0;
                    } else {
                        $kmNa = $lastKM->transportation_fuel_detail_km;
                    }
                    $kmNow = $km - $kmNa;
                    $realLiter = 0;
                    $dataNna['transportation_is_running']      = 0;
                    $whereNa['transportation_nopol']        = $nopol;
                    $this->db->update('transportation', $dataNna, $whereNa);
                }
                $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

                if ($employee->branch_code == null) {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Anda tidak memiliki BRANCH!.',
                        )
                    );
                } else {
                    $data['transportation_fuel_total_bbm']             = @$realLiter + @$totalBBM;
                    // $data['transportation_fuel_total_distance']        = @$lastKM->transportation_fuel_detail_distance + @$kmNow;
                    // $data['transportation_fuel_total_distance']        = @$lastKM->transportation_fuel_detail_distance - @$firstKM->transportation_fuel_detail_distance;
                    $data['transportation_fuel_total_distance']              = @$km - @$firstKM->transportation_fuel_detail_km;
                    $data['transportation_fuel_total_km']              = @$km - @$firstKM->transportation_fuel_detail_km;
                    $data['transportation_fuel_total_price']           = @$harga_bensin + @$totalHargaBBM;
                    $data['last_km'] = @$km;
                    // $data['transportation_fuel_total_km']              = @$totalKm + @$km;
                    $data['stop_type_code_fuel']                       = @$stopTypeCode;
                    $where['transportation_fuel_id']                   = @$idNa;
                    $this->db->update('transportation_fuel', $data, $where);

                    // @$areakerja_id = 0;


                    $dataDetail['transportation_fuel_id']      = @$idNa;
                    $dataDetail['stop_type_code']      = @$stopTypeCode;
                    $dataDetail['transportation_fuel_detail_date'] = date('Y-m-d H:i:s');
                    $dataDetail['transportation_fuel_detail_areakerja_id'] = @$areakerja_id;
                    $dataDetail['transportation_fuel_detail_latitude'] = @$latitude;
                    $dataDetail['transportation_fuel_detail_longitude'] = @$longitude;
                    $dataDetail['transportation_fuel_detail_bbm'] = @$realLiter;
                    $dataDetail['transportation_fuel_detail_price'] = @$harga_bensin;
                    $dataDetail['transportation_fuel_detail_km'] = @$km;
                    $dataDetail['transportation_fuel_detail_foto_km'] = @$fileName;
                    $dataDetail['transportation_fuel_detail_foto_struk'] = @$fileNameStruk;
                    $dataDetail['transportation_fuel_detail_distance'] = @$kmNow;
                    $dataDetail['transportation_fuel_detail_jenis_bbm'] = @$bbmNA;
                    $dataDetail['transportation_fuel_detail_price_jenis_bbm'] = @$harga;
                    if ($this->input->post('isSudahAda') == 1) {
                        $dataDetail['activity_id'] = $aktivitas;
                        $dataNna['transportation_fuel_id']      = @$idNa;
                        $whereNa['activity_id']        = @$aktivitas;
                        $this->dbMyAsset->update('activity', $dataNna, $whereNa);
                    }
                    $dataDetail['transportation_fuel_detail_areakerja_name']         = $chekjarak->areakerja_name;
                    $dataDetail['transportation_fuel_detail_jenis_bbm'] = @$bbmNA;
                    $dataDetail['transportation_fuel_detail_price_jenis_bbm'] = @$harga;


                    if ($this->db->insert('transportation_fuel_detail', $dataDetail)) {
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'success',
                                'message' => 'BBM Armada Berhasil disimpan.',
                            )
                        );
                    } else {
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'error',
                                'message' => 'Gagal disimpan.',
                            )
                        );
                    }
                }
            }
        }






        print json_encode($arr_result);
    }

    public function strk_post()
    {
        if ($this->input->post('fileNameStruk') != null && $this->input->post('fileImageStruk') != null) {
            $fileNameStruk = date('Ymd') . $this->input->post('fileNameStruk');
            $fileImageStruk = $this->input->post('fileImageStruk');
            $binaryImage2 = base64_decode($fileImageStruk);
            header('Content-Type: bitmap; charset=utf-8');
            //gambar akan disimpan pada folder image
            $fileGAMBAR2 = fopen('../uploads/image_aktivitas/' . $fileNameStruk, 'wb');
            // Create File
            fwrite($fileGAMBAR2, $binaryImage2);
            fclose($fileGAMBAR2);
            $isError = 0;
        } else {
            $isError = 1;
            $fileNameStruk = "";
        }
        if ($isError == 1) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Harap Upload Foto Struk BBM Anda!.',
                )
            );
        } else {
            $transportationFuelId  = $this->input->post('transportationFuelId');

            $data['transportation_fuel_detail_foto_struk']     = @$fileNameStruk;
            $where['transportation_fuel_detail_id']                   = @$transportationFuelId;
            if ($this->db->update('transportation_fuel_detail', $data, $where)) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => 'Foto Struk berhasil disimpan.',
                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal disimpan.',
                    )
                );
            }
        }
        print json_encode($arr_result);
    }



    public function km_uptd_post()
    {

        if ($this->input->post('fileName') != null && $this->input->post('fileImage') != null) {
            $fileName = date('Ymd') . $this->input->post('fileName');
            $fileImage = $this->input->post('fileImage');
            $binaryImage = base64_decode(@$fileImage);
            header('Content-Type: bitmap; charset=utf-8');
            //gambar akan disimpan pada folder image
            $fileGAMBAR = fopen('../uploads/image_aktivitas/' . @$fileName, 'wb');
            // Create File
            fwrite($fileGAMBAR, $binaryImage);
            fclose($fileGAMBAR);
            $isError = 0;
        } else {
            $fileName = "";
            $isError = 1;
        }
        if ($isError == 1) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Harap Upload Foto Kilometer pada Dashboard Mobil.',
                )
            );
        } else {
            $transportationFuelId  = $this->input->post('transportationFuelId');

            $data['transportation_fuel_detail_foto_km']        = @$fileName;
            $where['transportation_fuel_detail_id']                   = @$transportationFuelId;
            if ($this->db->update('transportation_fuel_detail', $data, $where)) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => 'Foto KM berhasil disimpan.',
                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal disimpan.',
                    )
                );
            }
        }
        print json_encode($arr_result);
    }

    public function update_new_post()
    {
        $idNa  = $this->input->post('idNa');
        $km  = $this->input->post('km');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $totalBBM  = $this->input->post('totalBBM');
        $totalHargaBBM  = $this->input->post('totalHargaBBM');
        $totalDistance  = $this->input->post('totalDistance');
        $totalKm  = $this->input->post('totalKm');
        $nopol  = $this->input->post('nopol');
        $realLiter  = $this->input->post('realLiter');
        $stopTypeCode  = $this->input->post('stopTypeCode');
        $nik  = $this->input->post('nik');
        $rpReal  = $this->input->post('rpReal');
        $isError = 0;
        $bbmNA  = $this->input->post('bbmNA');
        if ($totalHargaBBM == NUlL) {
            $totalHargaBBM = 0;
        }

        $activityKategoriId  = $this->input->post('activityKategoriId');
        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();
        if ($employee->subunit_code) {
            $subunit_code = $employee->subunit_code;
        } else {
            $subunit_code = '';
        }
        if ($employee->unit_code) {
            $unit_code = $employee->unit_code;
        } else {
            $unit_code = '';
        }
        if ($latitude == null || $longitude == null || $latitude == 0 || $longitude == 0  || $latitude == "null" || $longitude == "null" || $latitude == "" || $longitude == "") {
            $areakerja_id = 0;
        } else {
            if ($stopTypeCode == 'STCST') {
                $chekjarak = $this->Mabsensi->checkJarakGlobal(@$employee->branch_code, @$latitude, @$longitude);
            } else {
                $chekjarak = $this->Mabsensi->checkJarakGlobalArea(@$employee->branch_code, @$latitude, @$longitude);
            }
            if (empty($chekjarak)) {
                $areakerja_id = 0;
            } else {
                $areakerja_id = $chekjarak->areakerja_id;
            }
        }
        if ($stopTypeCode == null) {
            $stopTypeCode = 'STCMBB';
        }
        if ($stopTypeCode == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Silahkan pilih lagi jenis pemberhitan!.',
                )
            );
        } else {
            if ($this->input->post('isSudahAda') == 1) {

                $section               = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();
                $dataAktivitas['nik']         = $nik;
                $dataAktivitas['activity_kategory_id']         = $activityKategoriId;
                $dataAktivitas['activity_date_created']         = date('Y-m-d H:i:s');
                $dataAktivitas['activity_latitude']         = $latitude;
                $dataAktivitas['activity_longitude']         = $longitude;
                $dataAktivitas['activity_foto']         = @$this->input->post('fileName');
                $dataAktivitas['activity_foto1']         = @$this->input->post('fileNameStruk');
                $dataAktivitas['activity_areakerja']         = $areakerja_id;
                $dataAktivitas['activity_type']         = "DAILY DRIVER";
                $dataAktivitas['no_plat']         = @$nopol;
                $dataAktivitas['status_armada']         = @$this->input->post('dataKelayakan');
                $dataAktivitas['km_armada']         = @$this->input->post('km');
                $dataAktivitas['full_name']         = @$employee->employee_name;
                $dataAktivitas['section_code']         = @$employee->section_code;
                $dataAktivitas['branch_code']         = @$employee->branch_code;
                $dataAktivitas['unit_code']         = @$unit_code;
                $dataAktivitas['subunit_code']         = @$subunit_code;
                $dataAktivitas['section_name']         = @$section->section_name;
                $dataAktivitas['areakerja_name']         = @$chekjarak->areakerja_name;

                $aktivitas = $this->Maktivitas->create($dataAktivitas);
                $listCekDesc  = $this->input->post('listCekName');
                $listCekId  = $this->input->post('listCekId');
                $descCek      = explode('#', $listCekDesc, -1);
                $idCek  = explode('#', $listCekId, -1);

                $x = 0;
                foreach ($idCek as $idCek) {
                    $dataCek['activity_repair_kategory_id']              = $idCek;
                    $dataCek['activity_id']                              = $aktivitas;
                    $dataCek['activity_repair_type']                     = $descCek[$x];

                    $this->dbMyAsset->insert('activity_repair', $dataCek);
                    $x++;
                }
            } else {
                $aktivitas  = $this->input->post('activityId');
            }
            if ($stopTypeCode == 'STCST') {
                $harga_bensin = 0;
                $kmNa = 0;
                $lastKM = $this->MBbm->ambilLastKmNa(@$idNa);
                $firstKM = $this->MBbm->ambilFirst($idNa);
                if (empty($lastKM)) {
                    $kmNa = 0;
                } else {
                    $kmNa = $lastKM->transportation_fuel_detail_km;
                }
                $kmNow = $km - $kmNa;
                $isError = 0;
                $realLiter = 0;
                $dataNna['transportation_is_running']      = 0;
                $whereNa['transportation_nopol']        = $nopol;
                $this->dbMyAsset->update('transportation', $dataNna, $whereNa);
            } elseif ($stopTypeCode == 'STCMBB') {
                $lastKM = $this->MBbm->ambilLastKmNa(@$idNa);
                $firstKM = $this->MBbm->ambilFirst(@$idNa);
                $harga        = $this->dbMyAsset->get_where('petrol', ['petrol_name' => $bbmNA])->row()->petrol_price;

                if (empty($lastKM)) {
                    $kmNa = 0;
                } else {
                    $kmNa = $lastKM->transportation_fuel_detail_km;
                }
                if ($totalBBM == null) {
                    $totalBBM = 0;
                }
                $kmNow = $km - $kmNa;
                $harga_bensin = @$rpReal;
                if ($harga_bensin == null) {
                    $isError = 1;
                    $harga_bensin = 0;
                }
                if ($realLiter == null) {
                    $realLiter = 0;
                }
            } else {
                $harga_bensin = 0;
                $kmNa = 0;
                $lastKM = $this->MBbm->ambilLastKmNa(@$idNa);
                $firstKM = $this->MBbm->ambilFirst($idNa);
                if (empty($lastKM)) {
                    $kmNa = 0;
                } else {
                    $kmNa = $lastKM->transportation_fuel_detail_km;
                }
                $kmNow = $km - $kmNa;
                $realLiter = 0;
                $isError = 0;
                $dataNna['transportation_is_running']      = 0;
                $whereNa['transportation_nopol']        = $nopol;
                $this->dbMyAsset->update('transportation', $dataNna, $whereNa);
            }

            if ($employee->branch_code == null) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki BRANCH!.',
                    )
                );
            } else {
                if ($isError == 1) {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Jangan Kosongkan Harga BBM!.',
                        )
                    );
                } else {
                    $data['transportation_fuel_total_bbm']             = @$realLiter + @$totalBBM;
                    $data['transportation_fuel_total_distance']              = @$km - @$firstKM->transportation_fuel_detail_km;
                    $data['transportation_fuel_total_km']              = @$km - @$firstKM->transportation_fuel_detail_km;
                    $data['transportation_fuel_total_price']           = @$harga_bensin + @$totalHargaBBM;
                    $data['last_km'] = @$km;
                    $data['stop_type_code_fuel']                       = @$stopTypeCode;
                    $where['transportation_fuel_id']                   = @$idNa;
                    $this->dbMyAsset->update('transportation_fuel', $data, $where);


                    $dataDetail['transportation_fuel_id']      = @$idNa;
                    $dataDetail['stop_type_code']      = @$stopTypeCode;
                    $dataDetail['transportation_fuel_detail_date'] = date('Y-m-d H:i:s');
                    $dataDetail['transportation_fuel_detail_areakerja_id'] = @$areakerja_id;
                    $dataDetail['transportation_fuel_detail_latitude'] = @$latitude;
                    $dataDetail['transportation_fuel_detail_longitude'] = @$longitude;
                    $dataDetail['transportation_fuel_detail_bbm'] = @$realLiter;
                    $dataDetail['transportation_fuel_detail_price'] = @$harga_bensin;
                    $dataDetail['transportation_fuel_detail_km'] = @$km;
                    $dataDetail['transportation_fuel_detail_distance'] = @$kmNow;
                    $dataDetail['transportation_fuel_detail_foto_km'] = @$this->input->post('fileName');
                    $dataDetail['transportation_fuel_detail_foto_struk'] = @$this->input->post('fileNameStruk');
                    $dataDetail['transportation_fuel_detail_jenis_bbm'] = @$bbmNA;
                    $dataDetail['transportation_fuel_detail_price_jenis_bbm'] = @$harga;
                    if ($this->input->post('isSudahAda') == 1) {
                        $dataDetail['activity_id'] = @$aktivitas;
                        $dataNnaBaru['transportation_fuel_id']      = @$idNa;
                        $whereNaBaru['activity_id']        = @$aktivitas;
                        $this->dbMyAsset->update('activity', $dataNnaBaru, $whereNaBaru);
                    }
                    if ($this->dbMyAsset->insert('transportation_fuel_detail', $dataDetail)) {
                        $insert_id = $this->dbMyAsset->insert_id();
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'success',
                                'message' => 'BBM Armada Berhasil disimpan.',
                                'transportation_fuel_id' => $insert_id,
                            )
                        );
                    } else {
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'error',
                                'message' => 'Gagal disimpan.',
                            )
                        );
                    }
                }
            }
        }


        print json_encode($arr_result);
    }
}
