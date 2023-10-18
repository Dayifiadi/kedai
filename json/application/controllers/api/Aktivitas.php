<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Aktivitas extends REST_Controller
{

    private $arr_result         = array();

    private $field_list         = array(
        'activity_id', 'nik', 'activity_nama_aktivitas',
        'activity_deskripsi_aktivitas', 'activity_latitude', 'activity_longitude', 'activity_date_created', 'activity_foto'
    );
    private $required_field     = array('');
    private $md5_field          = array('');
    private $primary_key        = 'activity_id';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Maktivitas');
        $this->load->model('Mpengguna');
        $this->load->model('Mnotification');
        $this->load->model('MBbm');
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

    public function simpan_foto_post()
    {
        $fileName = date('YmdHis') . $this->input->post('fileName');
        $fileImage = $this->input->post('fileImage');
        $binaryImage = base64_decode($fileImage);
        header('Content-Type: bitmap; charset=utf-8');
        //gambar akan disimpan pada folder image
        $fileGAMBAR = fopen('../uploads/image_aktivitas/' . $fileName, 'wb');
        // Create File
        fwrite($fileGAMBAR, $binaryImage);
        fclose($fileGAMBAR);
        $binryImage = base64_decode($fileImage);
        $arr_result = array(
            'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Berhasil disimpan.',
                'fileName' => $fileName,
            )
        );
        print json_encode($arr_result);
    }

    public function delete_foto_post()
    {
        $fileName = $this->input->post('fileName');
        $fileGAMBAR = '../uploads/image_aktivitas/' . $fileName;
        if (file_exists($fileGAMBAR)) {
            unlink($fileGAMBAR);
        }
        $arr_result = array(
            'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Berhasil disimpan.',
            )
        );
        print json_encode($arr_result);
    }


    public function create_post()
    {
        $nik  = $this->input->post('nik');
        $keterangan  = $this->input->post('keterangan');
        $longitude_user  = $this->input->post('longitude_user');
        $latitude_user  = $this->input->post('latitude_user');
        $activityKategoriId  = $this->input->post('activityKategoriId');
        $activity_deskripsi_aktivitas  = $this->input->post('activity_deskripsi_aktivitas');
        $no_plat  = $this->input->post('no_plat');
        $isSudahNa  = $this->input->post('isSudahNa');
        $isQrCode  = $this->input->post('isQrCode');
        $isLayak  = $this->input->post('isLayak');
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

        $chekjarak = $this->Maktivitas->checkJarakGlobalArea($employee->branch_code, $latitude_user, $longitude_user);
        $areakerja_id = $chekjarak->areakerja_id;


        if ($activityKategoriId == null || $activityKategoriId == 0) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Mohon Pilih Jenis Aktivitas Anda!.',
                )
            );
        } else {
            $isError = false;
            // if ($employee->branch_code == 'TSM000') {
            //     if ($activity_deskripsi_aktivitas == "") {
            //         $arr_result = array(
            //             'jne_ittsm' => array(
            //                 'status' => 'error',
            //                 'message' => 'Mohon Masukkan Deskripsi Aktivitas Anda!.',
            //             )
            //         );
            //         $isError = true;
            //     } else {
            //         $isError = false;
            //     }
            // } else {
            //     $isError = false;
            // }
            if ($this->input->post('aktivitasType') != "CEK & REPAIR" && $this->input->post('aktivitasType') != "BBM ARMADA" && $this->input->post('aktivitasType') != "DAILY DRIVER") {
                if ($employee->branch_code == 'TSM000') {
                    if ($isQrCode == "0") {
                        if ($isSudahNa == "1") {
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
                            $isError = false;
                        } else {
                            $isError = true;
                            $fileName = "";
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Anda Belum Memfoto Aktivitas Anda!.',
                                )
                            );
                            print json_encode($arr_result);
                            return TRUE;
                        }
                        if ($activity_deskripsi_aktivitas == "") {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Mohon Masukkan Deskripsi Aktivitas Anda!.',
                                )
                            );
                            $isError = true;

                            print json_encode($arr_result);
                            return TRUE;
                        } else {
                            $isError = false;
                        }
                    } else {
                        $isError = false;
                        $fileName = "";
                    }
                } else {
                    if ($isSudahNa == "1") {
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
                        $isError = true;
                        $fileName = "";
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'error',
                                'message' => 'Anda Belum Memfoto Aktivitas Anda!.',
                            )
                        );
                        print json_encode($arr_result);
                        return TRUE;
                    }
                }
            }
            if ($this->input->post('aktivitasType') == "CEK & REPAIR" || $this->input->post('aktivitasType') == "BBM ARMADA" || $this->input->post('aktivitasType') == "DAILY DRIVER") {
                if ($no_plat == "") {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Mohon Pilih No Plat Mobil!.',
                        )
                    );
                    $isError = true;
                    print json_encode($arr_result);
                    return TRUE;
                } else {
                    // if ($this->input->post('dataKelayakan') == NULL && $this->input->post('aktivitasType') == "CEK & REPAIR") {
                    //     $arr_result = array(
                    //         'jne_ittsm' => array(
                    //             'status' => 'error',
                    //             'message' => 'Mohon Pilih Jenis Kelayakan!.',
                    //         )
                    //     );
                    //     $isError = true;
                    //     print json_encode($arr_result);
                    //     return TRUE;
                    // } else {
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
                        if ($this->input->post('driverNik') == NULL && $this->input->post('aktivitasType') == "BBM ARMADA") {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Silihkan pilih pengemudi!.',
                                )
                            );
                            $isError = true;
                            print json_encode($arr_result);
                            return TRUE;
                        } else {
                            $isError = false;
                        }
                    }
                    // }
                }
            }
            if ($isError == false) {
                if ($this->input->post('aktivitasType') == "CEK & REPAIR") {
                    $listFotoName  = $this->input->post('listFotoName');
                    $listFoto  = explode('#', $listFotoName, -1);
                    $jumlahFoto = count($listFoto);
                    if ($jumlahFoto == 4) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];

                        $data['activity_foto2']         = $listFoto[2];

                        $data['activity_foto3']         = $listFoto[3];
                    } elseif ($jumlahFoto == 3) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];

                        $data['activity_foto2']         = $listFoto[2];
                    } elseif ($jumlahFoto == 2) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];
                    } elseif ($jumlahFoto == 1) {
                        $data['activity_foto']         = $listFoto[0];
                    }
                    $section               = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();
                    @$employeeDriver        = $this->db->get_where('employee', ['nik' => @$this->input->post('driverNik')])->row();
                    @$sectionDriver               = $this->db->get_where('section', ['section_code' => @$employeeDriver->section_code])->row();


                    $data['nik']         = $nik;
                    $data['activity_kategory_id']         = $activityKategoriId;
                    $data['activity_deskripsi_aktivitas']         = $activity_deskripsi_aktivitas;
                    $data['activity_date_created']         = date('Y-m-d H:i:s');
                    $data['activity_latitude']         = $latitude_user;
                    $data['activity_longitude']         = $longitude_user;
                    $data['activity_areakerja']         = $areakerja_id;
                    $data['activity_type']         = $this->input->post('aktivitasType');
                    $data['driverNik']         = $this->input->post('driverNik');
                    $data['no_plat']         = $no_plat;
                    $data['status_armada']         = $isLayak;
                    $data['km_armada']         = $this->input->post('km');
                    $data['full_name']         = $employee->employee_name;
                    $data['section_code']         = $employee->section_code;
                    $data['section_name']         = $section->section_name;
                    $data['branch_code']         = $employee->branch_code;
                    $data['unit_code']         = @$unit_code;
                    $data['subunit_code']         = @$subunit_code;
                    $data['areakerja_name']         = $chekjarak->areakerja_name;
                    $data['driverName']            = @$employeeDriver->employee_name;
                    $data['driverSectionCode']         = @$employeeDriver->section_code;
                    $data['driverSectionName']         = @$sectionDriver->section_name;
                } elseif ($this->input->post('aktivitasType') == "DAILY DRIVER") {
                    $listFotoName  = $this->input->post('listFotoName');
                    $listFoto  = explode('#', $listFotoName, -1);
                    $jumlahFoto = count($listFoto);
                    if ($jumlahFoto == 4) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];

                        $data['activity_foto2']         = $listFoto[2];

                        $data['activity_foto3']         = $listFoto[3];
                    } elseif ($jumlahFoto == 3) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];

                        $data['activity_foto2']         = $listFoto[2];
                    } elseif ($jumlahFoto == 2) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];
                    } elseif ($jumlahFoto == 1) {
                        $data['activity_foto']         = $listFoto[0];
                    }
                    $section               = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();

                    $data['nik']         = $nik;
                    $data['activity_kategory_id']         = $activityKategoriId;
                    $data['activity_deskripsi_aktivitas']         = $activity_deskripsi_aktivitas;
                    $data['activity_date_created']         = date('Y-m-d H:i:s');
                    $data['activity_latitude']         = $latitude_user;
                    $data['activity_longitude']         = $longitude_user;
                    $data['activity_areakerja']         = $areakerja_id;
                    $data['activity_type']         = $this->input->post('aktivitasType');
                    $data['no_plat']         = $no_plat;
                    $data['status_armada']         = $this->input->post('dataKelayakan');
                    $data['km_armada']         = $this->input->post('km');
                    $data['full_name']         = $employee->employee_name;
                    $data['section_code']         = $employee->section_code;
                    $data['branch_code']         = $employee->branch_code;
                    $data['unit_code']         = @$unit_code;
                    $data['subunit_code']         = @$subunit_code;
                    $data['section_name']         = $section->section_name;
                    $data['areakerja_name']         = $chekjarak->areakerja_name;
                } elseif ($this->input->post('aktivitasType') == "BBM ARMADA") {
                    $listFotoName  = $this->input->post('listFotoName');
                    $listFoto  = explode('#', $listFotoName, -1);
                    $jumlahFoto = count($listFoto);
                    if ($jumlahFoto == 4) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];

                        $data['activity_foto2']         = $listFoto[2];

                        $data['activity_foto3']         = $listFoto[3];
                    } elseif ($jumlahFoto == 3) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];

                        $data['activity_foto2']         = $listFoto[2];
                    } elseif ($jumlahFoto == 2) {
                        $data['activity_foto']         = $listFoto[0];

                        $data['activity_foto1']         = $listFoto[1];
                    } elseif ($jumlahFoto == 1) {
                        $data['activity_foto']         = $listFoto[0];
                    }
                    $section               = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();
                    @$employeeDriver        = $this->db->get_where('employee', ['nik' => @$this->input->post('driverNik')])->row();
                    @$sectionDriver               = $this->db->get_where('section', ['section_code' => @$employeeDriver->section_code])->row();

                    $data['nik']         = $nik;
                    $data['activity_kategory_id']         = $activityKategoriId;
                    $data['activity_deskripsi_aktivitas']         = $activity_deskripsi_aktivitas;
                    $data['activity_date_created']         = date('Y-m-d H:i:s');
                    $data['activity_latitude']         = $latitude_user;
                    $data['activity_longitude']         = $longitude_user;
                    $data['activity_areakerja']         = $areakerja_id;
                    $data['activity_type']         = $this->input->post('aktivitasType');
                    $data['driverNik']         = $this->input->post('driverNik');
                    $data['no_plat']         = $no_plat;
                    $data['status_armada']         = $isLayak;
                    $data['km_armada']         = $this->input->post('km');
                    $data['full_name']         = $employee->employee_name;
                    $data['section_code']         = $employee->section_code;
                    $data['section_name']         = $section->section_name;
                    $data['branch_code']         = $employee->branch_code;
                    $data['unit_code']         = @$unit_code;
                    $data['subunit_code']         = @$subunit_code;
                    $data['areakerja_name']         = $chekjarak->areakerja_name;
                    $data['driverName']            = @$employeeDriver->employee_name;
                    $data['driverSectionCode']         = @$employeeDriver->section_code;
                    $data['driverSectionName']         = @$sectionDriver->section_name;
                } else {
                    $section        = $this->db->get_where('section', ['section_code' => $employee->section_code])->row();

                    $data = array(
                        'nik' => $nik,
                        'activity_kategory_id' => $activityKategoriId,
                        'activity_deskripsi_aktivitas' => $activity_deskripsi_aktivitas,
                        'activity_date_created' => date('Y-m-d H:i:s'),
                        'activity_foto' => $fileName,
                        'activity_latitude' => $latitude_user,
                        'activity_longitude' => $longitude_user,
                        'activity_areakerja' => $areakerja_id,
                        'no_plat' => $no_plat,
                        'full_name' => $employee->employee_name,
                        'section_code' => $employee->section_code,
                        'unit_code' => @$unit_code,
                        'subunit_code' => @$subunit_code,
                        'branch_code' => $employee->branch_code,
                        'section_name' => $section->section_name,
                        'areakerja_name' => $chekjarak->areakerja_name,

                    );
                }
                $aktivitas = $this->Maktivitas->create($data);
                if ($aktivitas) {

                    if ($this->input->post('aktivitasType') == "CEK & REPAIR") {
                        // $listReapirName  = $this->input->post('listReapirName');
                        $listCekDesc  = $this->input->post('listCekName');
                        $listCekId  = $this->input->post('listCekId');
                        // $listCekName  = $this->input->post('listCekName');
                        // $listCekDesc  = $this->input->post('listCekDesc');

                        // $nameRepair  = explode('#', $listReapirName, -1);
                        $descCek      = explode('#', $listCekDesc, -1);

                        $idCek  = explode('#', $listCekId, -1);
                        // $nameCek  = explode('#', $listCekName, -1);
                        // $descCek      = explode('#', $listCekDesc, -1);

                        $x = 0;
                        foreach ($idCek as $idCek) {
                            $dataCek['activity_repair_kategory_id']              = $idCek;
                            $dataCek['activity_id']                              = $aktivitas;
                            $dataCek['activity_repair_type']                     = $descCek[$x];

                            $this->dbMyAsset->insert('activity_repair', $dataCek);
                            $x++;
                        }
                    } elseif ($this->input->post('aktivitasType') == "DAILY DRIVER") {
                        // $listReapirName  = $this->input->post('listReapirName');
                        $listCekDesc  = $this->input->post('listCekName');
                        $listCekId  = $this->input->post('listCekId');
                        // $listCekName  = $this->input->post('listCekName');
                        // $listCekDesc  = $this->input->post('listCekDesc');

                        // $nameRepair  = explode('#', $listReapirName, -1);
                        $descCek      = explode('#', $listCekDesc, -1);

                        $idCek  = explode('#', $listCekId, -1);
                        // $nameCek  = explode('#', $listCekName, -1);
                        // $descCek      = explode('#', $listCekDesc, -1);

                        $x = 0;
                        foreach ($idCek as $idCek) {
                            $dataCek['activity_repair_kategory_id']              = $idCek;
                            $dataCek['activity_id']                              = $aktivitas;
                            $dataCek['activity_repair_type']                     = $descCek[$x];

                            $this->dbMyAsset->insert('activity_repair', $dataCek);
                            $x++;
                        }
                    } elseif ($this->input->post('aktivitasType') == "BBM ARMADA") {
                        $listCekId  = $this->input->post('listCekId');
                        $listCekDesc  = $this->input->post('listCekName');

                        $idCek  = explode('#', $listCekId, -1);
                        $descCek      = explode('#', $listCekDesc, -1);

                        $x = 0;
                        foreach ($idCek as $idCek) {
                            $dataCek['activity_repair_kategory_id']              = $idCek;
                            $dataCek['activity_id']                              = $aktivitas;
                            $dataCek['activity_repair_type']                     = $descCek[$x];

                            $this->dbMyAsset->insert('activity_repair', $dataCek);
                            $x++;
                        }
                        $bbmNA  = $this->input->post('bbmNA');

                        $harga = '';
                        // if ($bbmNA == 'PERTALITE') {
                        //     $harga = '7650';
                        // } elseif ($bbmNA == 'SOLAR') {
                        //     $harga = '5150';
                        // } else {
                        //     $harga = '7650';
                        // }
                        @$harga        = $this->dbMyAsset->get_where('petrol', ['petrol_name' => @$bbmNA])->row()->petrol_price;

                        $employeeDriver        = $this->db->get_where('employee', ['nik' => $this->input->post('driverNik')])->row();
                        $sectionDriver               = $this->db->get_where('section', ['section_code' => $employeeDriver->section_code])->row();


                        if ($this->input->post('reasonNa') == 'STCST' || $this->input->post('reasonNa') == 'SC') {
                            $km = $this->input->post('km');
                            $harga_bensin = 0;
                            $kmNa = 0;
                            $lastNopol = $this->MBbm->getLastNopol(@$no_plat);
                            $lastKM = $this->MBbm->ambilLastKmNa(@$lastNopol->transportation_fuel_id);
                            $firstKM = $this->MBbm->ambilFirst(@$lastNopol->transportation_fuel_id);
                            if (empty($lastKM)) {
                                $kmNa = 0;
                            } else {
                                $kmNa = $lastKM->transportation_fuel_detail_km;
                            }
                            $kmNow = $km - $kmNa;
                            $realLiter = 0;
                            // $dataNna['transportation_is_running']      = 0;
                            // $whereNa['transportation_nopol']        = $no_plat;
                            // $this->dbMyAsset->update('transportation', $dataNna, $whereNa);

                            $dataFuel['transportation_fuel_total_distance']              = @$km - @$firstKM->transportation_fuel_detail_km;
                            $dataFuel['transportation_fuel_total_km']              = @$km - @$firstKM->transportation_fuel_detail_km;
                            $dataFuel['last_km'] = @$km;
                            $dataFuel['stop_type_code_fuel']                       = $this->input->post('reasonNa');
                            $where['transportation_fuel_id']                   = @$lastNopol->transportation_fuel_id;
                            $this->dbMyAsset->update('transportation_fuel', $dataFuel, $where);

                            $dataDetail['transportation_fuel_id']      = @$lastNopol->transportation_fuel_id;
                            $dataDetail['stop_type_code']      = $this->input->post('reasonNa');
                            $dataDetail['transportation_fuel_detail_date'] = date('Y-m-d H:i:s');
                            $dataDetail['transportation_fuel_detail_areakerja_id'] = @$areakerja_id;
                            $dataDetail['transportation_fuel_detail_latitude'] = @$latitude_user;
                            $dataDetail['transportation_fuel_detail_longitude'] = @$longitude_user;
                            $dataDetail['transportation_fuel_detail_bbm'] = @$realLiter;
                            $dataDetail['transportation_fuel_detail_km'] = @$km;
                            $dataDetail['transportation_fuel_detail_areakerja_name'] = @$chekjarak->areakerja_name;
                            $dataDetail['driverName'] = @$employeeDriver->employee_name;
                            $dataDetail['driverSectionCode'] = @$employeeDriver->section_code;
                            $dataDetail['driverSectionName'] = @$sectionDriver->section_name;


                            $listFotoName  = $this->input->post('listFotoName');
                            $listFoto  = explode('#', $listFotoName, -1);
                            $jumlahFoto = count($listFoto);
                            if ($jumlahFoto == 4) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                                $dataDetail['transportation_fuel_detail_foto_km1']         = $listFoto[1];
                                $dataDetail['transportation_fuel_detail_foto_km2']         = $listFoto[2];
                                $dataDetail['transportation_fuel_detail_foto_km3']         = $listFoto[3];
                            } elseif ($jumlahFoto == 3) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                                $dataDetail['transportation_fuel_detail_foto_km1']         = $listFoto[1];
                                $dataDetail['transportation_fuel_detail_foto_km2']         = $listFoto[2];
                            } elseif ($jumlahFoto == 2) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                                $dataDetail['transportation_fuel_detail_foto_km1']         = $listFoto[1];
                            } elseif ($jumlahFoto == 1) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                            }
                            $dataDetail['transportation_fuel_detail_price'] = 0;
                            $dataDetail['transportation_fuel_detail_distance'] = @$kmNow;
                            $dataDetail['activity_id'] = @$aktivitas;
                            $dataDetail['nik_security'] = @$nik;
                            $this->dbMyAsset->insert('transportation_fuel_detail', $dataDetail);

                            $dataNAA = $this->getTokenAllRow($this->input->post('driverNik'));
                            $dataUser = $this->getTokenAllRow(@$nik);
                            if ($this->input->post('reasonNa') == 'SC'){
                                $this->Mnotification->sendToTopic(@$dataNAA->token, 'BBM Armada', 'Armada telah dilakukan security check, Oleh ' . $dataUser->employee_name . '.', '5', @$lastNopol->transportation_fuel_id);
                            } else {
                                $this->Mnotification->sendToTopic(@$dataNAA->token, 'BBM Armada', 'Armada telah di STOP, Oleh ' . $dataUser->employee_name . '.', '5', @$lastNopol->transportation_fuel_id);
                            }
                            
                        } else {
                            $dataNna['transportation_is_running']      = 0;
                            $whereNa['transportation_nopol']        = $no_plat;
                            $this->dbMyAsset->update('transportation', $dataNna, $whereNa);
                            $employeeDriver        = $this->db->get_where('employee', ['nik' => $this->input->post('driverNik')])->row();
                            if ($employeeDriver->subunit_code) {
                                $subunit_code_driver = $employeeDriver->subunit_code;
                            } else {
                                $subunit_code_driver = '';
                            }
                            if ($employeeDriver->unit_code) {
                                $unit_code_driver = $employeeDriver->unit_code;
                            } else {
                                $unit_code_driver = '';
                            }
                            $sectionDriver               = $this->db->get_where('section', ['section_code' => $employeeDriver->section_code])->row();

                            $dataBBM['nik']                              = $this->input->post('driverNik');
                            $dataBBM['transportation_fuel_date_created'] = date('Y-m-d H:i:s');
                            $dataBBM['transportation_fuel_nopol']        = $no_plat;
                            $dataBBM['transportation_fuel_total_bbm']        = 0;
                            $dataBBM['transportation_fuel_total_distance']        = 0;
                            $dataBBM['transportation_fuel_total_price']        = 0;
                            $dataBBM['transportation_fuel_total_km']        = $this->input->post('km');
                            $dataBBM['keterangan']        = $keterangan;
                            $dataBBM['stop_type_code_fuel']      = 'STCS';
                            $dataBBM['last_km'] = $this->input->post('km');
                            $dataBBM['jenis_armada'] = @$this->input->post('jenis_armada');
                            $dataBBM['posisi_kendaraan'] = @$this->input->post('posisi_kendaraan');
                            $dataBBM['jenis_bahan_bakar'] = @$harga;
                            $dataBBM['full_name']         = $employeeDriver->employee_name;
                            $dataBBM['section_code']         = $employeeDriver->section_code;
                            $dataBBM['branch_code']         = $employeeDriver->branch_code;
                            $dataBBM['unit_code']         = @$unit_code_driver;
                            $dataBBM['subunit_code']         = @$subunit_code_driver;
                            $dataBBM['section_name']         = $sectionDriver->section_name;

                            $this->dbMyAsset->insert('transportation_fuel', $dataBBM);
                            $bbmID = $this->dbMyAsset->insert_id();

                            $dataNnaACTIVITY['transportation_fuel_id']      = $bbmID;
                            $whereNaACTIVITY['activity_id']        = $aktivitas;
                            $this->dbMyAsset->update('activity', $dataNnaACTIVITY, $whereNaACTIVITY);

                            $dataDetail['transportation_fuel_id']      = $bbmID;
                            $dataDetail['stop_type_code']      = 'STCS';
                            $dataDetail['transportation_fuel_detail_date'] = date('Y-m-d H:i:s');
                            $dataDetail['transportation_fuel_detail_areakerja_id'] = $areakerja_id;
                            $dataDetail['transportation_fuel_detail_latitude'] = $latitude_user;
                            $dataDetail['transportation_fuel_detail_longitude'] = $longitude_user;
                            $dataDetail['transportation_fuel_detail_bbm'] = 0;
                            $dataDetail['transportation_fuel_detail_price'] = 0;
                            $dataDetail['transportation_fuel_detail_distance'] = 0;
                            $dataDetail['transportation_fuel_detail_km'] = $this->input->post('km');
                            $dataDetail['nik_security'] = $nik;
                            $dataDetail['activity_id'] = $aktivitas;
                            $dataDetail['transportation_fuel_detail_foto_struk'] = '';
                            $dataDetail['transportation_fuel_detail_areakerja_name'] = @$chekjarak->areakerja_name;
                            $dataDetail['driverName'] = @$employeeDriver->employee_name;
                            $dataDetail['driverSectionCode'] = @$employeeDriver->section_code;
                            $dataDetail['driverSectionName'] = @$sectionDriver->section_name;
                            $dataDetail['transportation_fuel_detail_jenis_bbm'] = @$bbmNA;
                            $dataDetail['transportation_fuel_detail_price_jenis_bbm'] = @$harga;

                            $listFotoName  = $this->input->post('listFotoName');
                            $listFoto  = explode('#', $listFotoName, -1);
                            $jumlahFoto = count($listFoto);
                            if ($jumlahFoto == 4) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                                $dataDetail['transportation_fuel_detail_foto_km1']         = $listFoto[1];
                                $dataDetail['transportation_fuel_detail_foto_km2']         = $listFoto[2];
                                $dataDetail['transportation_fuel_detail_foto_km3']         = $listFoto[3];
                            } elseif ($jumlahFoto == 3) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                                $dataDetail['transportation_fuel_detail_foto_km1']         = $listFoto[1];
                                $dataDetail['transportation_fuel_detail_foto_km2']         = $listFoto[2];
                            } elseif ($jumlahFoto == 2) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                                $dataDetail['transportation_fuel_detail_foto_km1']         = $listFoto[1];
                            } elseif ($jumlahFoto == 1) {
                                $dataDetail['transportation_fuel_detail_foto_km']         = $listFoto[0];
                            }

                            $this->dbMyAsset->insert('transportation_fuel_detail', $dataDetail);

                            $dataNAA = $this->getTokenAllRow($this->input->post('driverNik'));
                            $dataUser = $this->getTokenAllRow($nik);
                            $this->Mnotification->sendToTopic(@$dataNAA->token, 'BBM Armada', 'Armada telah di START, Oleh ' . $dataUser->employee_name . '.', '5', $bbmID);
                        }
                    }

                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'success',
                            'message' => 'Berhasil disimpan.',
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




        print json_encode($arr_result);
    }


    public function getTokenAllRow($nik)
    {
        $this->db->select("employee.employee_name");
        $this->db->select("firebase_token.token");
        $this->db->select("employee.nik");
        $this->db->where('employee.nik', $nik);
        $this->db->join('firebase_token', 'employee.nik = firebase_token.nik', 'left');
        return $this->db->get('employee')->row();
    }

    public function create_new_post()
    {
        $nik  = $this->input->post('nik');
        $keterangan  = $this->input->post('keterangan');
        $longitude_user  = $this->input->post('longitude_user');
        $latitude_user  = $this->input->post('latitude_user');
        $activityKategoriId  = $this->input->post('activityKategoriId');
        $activity_deskripsi_aktivitas  = $this->input->post('activity_deskripsi_aktivitas');
        $isSudahNa  = $this->input->post('isSudahNa');
        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

        $chekjarak = $this->Maktivitas->checkJarakGlobalArea($employee->branch_code, $latitude_user, $longitude_user);
        $areakerja_id = $chekjarak->areakerja_id;
        if ($isSudahNa == 1) {
            $fileName = date('Ymd') . $this->input->post('fileName');
            $filePath = $this->input->post('filePath');
            $binaryFile = base64_decode($filePath);
            $fileGAMBAR = fopen('../uploads/image_aktivitas/' . $fileName, 'wb');
            // Create File
            fwrite($fileGAMBAR, $fileName);
            fclose($fileGAMBAR);
        } else {
            $fileName = "";
        }

        if ($activityKategoriId == null || $activityKategoriId == 0) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Mohon Pilih Jenis Aktivitas Anda!.',
                )
            );
        } else {
            $data = array(
                'nik' => $nik,
                'activity_kategory_id' => $activityKategoriId,
                'activity_deskripsi_aktivitas' => $activity_deskripsi_aktivitas,
                'activity_date_created' => date('Y-m-d H:i:s'),
                'activity_foto' => $fileName,
                'activity_latitude' => $latitude_user,
                'activity_longitude' => $longitude_user,
                'activity_areakerja' => $areakerja_id,
            );
            $aktivitas = $this->Maktivitas->create($data);
            if ($aktivitas) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => 'Berhasil disimpan.',
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

    public function find_history_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'activity_id';
        $ordering            = "desc";
        $limit               = 10;
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
        $data_aktivitas = $this->Maktivitas->findHistory($nik, 'result', $option);

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
                )
            );
        }

        print json_encode($arr_result);
    }


    public function find_history_new_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'activity_id';
        $ordering            = "desc";
        $limit               = 10;
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
        $data_aktivitas = $this->Maktivitas->findHs($nik, 'result', $option);

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
                )
            );
        }

        print json_encode($arr_result);
    }



    public function find_kat_post()
    {
        $employeePosition  = $this->input->post('employeePosition');

        $post = $this->Maktivitas->findPos($employeePosition);
        $data_aktivitas = $this->Maktivitas->findKat(@$post->position_group);

        $arr_result = array();
        if (empty($data_aktivitas)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas Kategori ditemukan.',
                    'data_aktivitas'     => $data_aktivitas,
                )
            );
        }

        print json_encode($arr_result);
    }

    public function find_kat_repair_type_by_id_post()
    {
        $transportationFuelId                       = $this->input->post('transportationFuelId');
        $type                       = $this->input->post('type');
        if ($type == NULL) {
            $dataLastRepair            = $this->Maktivitas->findRepairYesByFuelId(@$transportationFuelId);
        } else {
            $dataLastRepair            = $this->Maktivitas->findRepairYesByFuelId(@$transportationFuelId, $type);
        }
        // $dataLastRepair            = $this->Maktivitas->findRepairYes('49236');

        $arr_result = array();
        if (empty($dataLastRepair)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Repair Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas repair Kategori ditemukan.',
                    'dataLastRepair'     => $dataLastRepair,
                )
            );
        }

        print json_encode($arr_result);
    }

    public function find_kat_repair_type_post()
    {
        $nik                       = $this->input->post('nik');
        $platNo                    = $this->input->post('platNo');
        $activity_kategory_id      = $this->input->post('activity_kategory_id');
        $employeePosition          = $this->input->post('employeePosition');
        $branch_code          = $this->input->post('branch');

        $dataLastActivity          = $this->Maktivitas->findLastActivity($nik, $platNo);
        $dataLastRepair            = $this->Maktivitas->findRepairYes(@$dataLastActivity->activity_id);
        // $dataLastRepair            = $this->Maktivitas->findRepairYes('49236');
        $post                      = $this->Maktivitas->findPos($employeePosition);
        $data_aktivitas            = $this->Maktivitas->findKatRepairType(@$post->position_group, $activity_kategory_id, $branch_code);

        $arr_result = array();
        if (empty($data_aktivitas) && empty($dataLastActivity)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Repair Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas repair Kategori ditemukan.',
                    'dataLastActivity'     => $dataLastActivity,
                    'dataLastRepair'     => $dataLastRepair,
                    'data'     => $data_aktivitas,
                )
            );
        }

        print json_encode($arr_result);
    }

    public function find_bbm_post()
    {
        $employeePosition  = $this->input->post('employeePosition');
        $post = $this->Maktivitas->findPos($employeePosition);
        $data_aktivitas = $this->Maktivitas->findByType(@$post->position_group);

        $arr_result = array();
        if (empty($data_aktivitas)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas Kategori ditemukan.',
                    'data_aktivitas'     => $data_aktivitas,
                )
            );
        }

        print json_encode($arr_result);
    }


    public function id_aktivitas_post()
    {
        $employeePosition  = $this->input->post('employeePosition');
        $activityKategoriId  = $this->input->post('activityKategoriId');
        $post = $this->Maktivitas->findPos($employeePosition);
        $data_aktivitas = $this->Maktivitas->findKatId(@$activityKategoriId, @$post->position_group);

        $arr_result = array();
        if (empty($data_aktivitas)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas Kategori ditemukan.',
                    'data_aktivitas'     => $data_aktivitas,
                )
            );
        }

        print json_encode($arr_result);
    }


    public function find_kat_repair_post()
    {
        $activity_kategory_id  = $this->input->post('activity_kategory_id');

        $data = $this->Maktivitas->findKatRepair(@$activity_kategory_id);

        $arr_result = array();
        if (empty($data)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Repair Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas repair Kategori ditemukan.',
                    'data'     => $data,
                )
            );
        }

        print json_encode($arr_result);
    }


    public function find_daily_post()
    {
        $idNa  = $this->input->post('idNa');

        $transNa               = $this->dbMyAsset->get_where('transportation_fuel', ['transportation_fuel_id' => $idNa])->row();
        $employee               = $this->db->get_where('employee', ['nik' => $transNa->nik])->row();

        $post = $this->Maktivitas->findPos(@$employee->employee_position);
        $data = $this->Maktivitas->findDaily(@$post->position_group);

        $arr_result = array();
        if (empty($data)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Repair Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas repair Kategori ditemukan.',
                    'data'     => $data,
                )
            );
        }

        print json_encode($arr_result);
    }
    public function find_kat_repair_yes_post()
    {
        $activity_kategory_id  = $this->input->post('activity_kategory_id');
        $platNo  = $this->input->post('platNo');

        $dataSecurity = $this->Maktivitas->findKatRepairNew(@$activity_kategory_id, $platNo, 'BBM');
        $dataMekanik = $this->Maktivitas->findKatRepairNew(@$activity_kategory_id, $platNo, 'CEK');
        $dataDriver = $this->Maktivitas->findKatRepairNew(@$activity_kategory_id, $platNo, 'DRIVER');
        $dataStatusStop   = $this->Maktivitas->findKatRepairNewStop(@$dataSecurity->transportation_fuel_detail_km);


        $dataCekMekanik   = $this->Maktivitas->findKatRepairYes(@$dataMekanik->activity_id, $platNo);
        $dataCekDriver   = $this->Maktivitas->findKatRepairYes(@$dataDriver->activity_id, $platNo);
        $dataCekSecurity   = $this->Maktivitas->findKatRepairYes(@$dataSecurity->activity_id, $platNo);
        $dataStatus   = $this->MBbm->getLastStatus($platNo);
        $dataLastKM   = $this->MBbm->findLastKmByNopol($platNo);
        $dataLastName   = $this->MBbm->findLastName($platNo);
        $arrayKM = array(
            @$dataSecurity->km_armada,
            @$dataMekanik->km_armada,
            @$dataDriver->km_armada,
            @$dataLastKM->transportation_fuel_detail_km,
        );
        if (max(@$arrayKM) == null) {
            $maxKMna = 0;
        } else {
            $maxKMna = max(@$arrayKM);
        }
        if (@$dataStatus->stop_type_name == NULL) {
            $stopName = 'STOP';
        } else {
            $stopName = @$dataStatus->stop_type_name;
        }

        $arr_result = array();
        // if (empty($dataSecurity) && empty($dataMekanik) && empty($dataDriver)) {
        //     $arr_result = array(
        //         'jne_ittsm' => array(
        //             'status' => 'error',
        //             'message' => 'Aktivitas Repair Kategori tidak ditemukan!.',
        //         )
        //     );
        // } else {
        $arr_result = array(
            'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data aktivitas repair Kategori ditemukan.',
                'dataMekanik'     => $dataMekanik,
                'dataCekMekanik'     => $dataCekMekanik,
                'dataDriver'     => $dataDriver,
                'dataCekDriver'     => $dataCekDriver,
                'dataSecurity'     => $dataSecurity,
                'dataCekSecurity'     => $dataCekSecurity,
                'dataStatusStop'     => $dataStatusStop,
                'lastKM'     => $maxKMna,
                'lastStatusNopol'     => @$stopName,
                'dataLastName'     => @$dataLastName,
            )
        );
        // }

        print json_encode($arr_result);
    }
    public function find_kat_repair_yes_detail_post()
    {
        $activity_id  = $this->input->post('activity_id');
        $platNo  = $this->input->post('platNo');

        $data_bar   = $this->Maktivitas->findKatRepairYes($activity_id, $platNo);
        $dataRepair   = $this->Maktivitas->findKatCekYes($activity_id, $platNo);

        $arr_result = array();
        if (empty($data_bar) && empty($dataRepair)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Aktivitas Repair Kategori tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data aktivitas repair Kategori ditemukan.',
                    'dataCek'     => $data_bar,
                    'dataRepair'     => $dataRepair,
                )
            );
        }

        print json_encode($arr_result);
    }
}
