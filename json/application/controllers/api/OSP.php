<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class OSP extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MOsp');
        $this->load->model('Mabsensi');

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

    public function find_osp_post()
    {
        $arr_result = array();
        $order_by            = 'order_sheet_ritase_id';
        $ordering            = "ASC";
        $limit               = 50;
        $page                = 0;

        if (isset($_POST['order_by'])) {
            $order_by = $this->input->post('order_by');
        }

        if (isset($_POST['ordering'])) {
            $ordering = $this->input->post('ordering');
        }

        // if (isset($_POST['limit'])) {
        //     $limit = $this->input->post('limit');
        // }

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

        $data_user = $this->MOsp->getRitase($option);

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


    public function create_post()
    {
        $nik  = $this->input->post('nik');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $isSudahNa  = $this->input->post('isSudahNa');
        $keterangan_ops  = $this->input->post('keterangan_ops');
        $nameAgen  = $this->input->post('nameAgen');
        $jumlah_barang  = $this->input->post('jumlah_barang');
        $idRitase  = $this->input->post('idRitase');
        $ritase  = $this->input->post('ritase');

        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

        if ($employee->branch_code == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki BRANCH!.',
                )
            );
        } else {
            $data['nik']                             = $nik;
            $data['order_sheet_ritase_type']         = $ritase;
            $data['order_sheet_pickup_date_created'] = date('Y-m-d H:i:s');
            $data['order_sheet_pickup_status']       = 'STCS';


            if ($this->db->insert('order_sheet_pickup', $data)) {
                $insert_id = $this->db->insert_id();

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

                $dataDetail['order_sheet_pickup_id']                    = $insert_id;
                $dataDetail['order_sheet_pickup_detail_status']         = 'STCS';
                $dataDetail['order_sheet_pickup_detail_created_date']   = date('Y-m-d H:i:s');
                $dataDetail['order_sheet_pickup_detail_id_ritase']      = $idRitase;
                $dataDetail['order_sheet_pickup_detail_jumlah']         = $jumlah_barang;
                $dataDetail['order_sheet_pickup_detail_foto']           = $fileName;
                $dataDetail['order_sheet_pickup_detail_keterangan']     = $keterangan_ops;
                $dataDetail['latitude']                                 = $latitude;
                $dataDetail['longitude']                                = $longitude;
                $dataDetail['nama_agen']                                = $nameAgen;

                if ($this->db->insert('order_sheet_pickup_detail', $dataDetail)) {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'success',
                            'message' => 'Order Sheet Pickup Berhasil disimpan.',
                            'idNa' => $insert_id,

                        )
                    );
                } else {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Order Sheet Pickup Detail Gagal disimpan.',
                        )
                    );
                }
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal Menambahkan Data Order Sheet Pickup!.',
                    )
                );
            }
        }


        print json_encode($arr_result);
    }



    public function buat_post()
    {
        $nik  = $this->input->post('nik');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $isSudahNa  = $this->input->post('isSudahNa');
        $keterangan_ops  = $this->input->post('keterangan_ops');
        $nameAgen  = $this->input->post('nameAgen');
        $jumlah_barang  = $this->input->post('jumlah_barang');
        $jumlah_barang_hvs  = $this->input->post('jumlah_barang_hvs');
        $jumlah_barang_non_hvs  = $this->input->post('jumlah_barang_non_hvs');
        $idRitase  = $this->input->post('idRitase');
        $ritase  = $this->input->post('ritase');

        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

        if ($employee->branch_code == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki BRANCH!.',
                )
            );
        } else {
            $data['nik']                             = $nik;
            $data['order_sheet_ritase_type']         = $ritase;
            $data['order_sheet_pickup_date_created'] = date('Y-m-d H:i:s');
            $data['order_sheet_pickup_status']       = 'STCS';


            if ($this->db->insert('order_sheet_pickup', $data)) {
                $insert_id = $this->db->insert_id();

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


                $dataDetail['order_sheet_pickup_id']                    = $insert_id;
                $dataDetail['order_sheet_pickup_detail_status']         = 'STCS';
                $dataDetail['order_sheet_pickup_detail_created_date']   = date('Y-m-d H:i:s');
                $dataDetail['order_sheet_pickup_detail_id_ritase']      = $idRitase;
                $dataDetail['order_sheet_pickup_detail_jumlah']         = $jumlah_barang;
                $dataDetail['order_sheet_pickup_detail_jumlah_hvs']     = $jumlah_barang_hvs;
                $dataDetail['order_sheet_pickup_detail_jumlah_total']   = $jumlah_barang_non_hvs;
                $dataDetail['order_sheet_pickup_detail_foto']           = $fileName;
                $dataDetail['order_sheet_pickup_detail_keterangan']     = $keterangan_ops;
                $dataDetail['latitude']                                 = $latitude;
                $dataDetail['longitude']                                = $longitude;
                $dataDetail['nama_agen']                                = $nameAgen;

                if ($this->db->insert('order_sheet_pickup_detail', $dataDetail)) {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'success',
                            'message' => 'Order Sheet Pickup Berhasil disimpan.',
                            'idNa' => $insert_id,

                        )
                    );
                } else {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Order Sheet Pickup Detail Gagal disimpan.',
                        )
                    );
                }
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal Menambahkan Data Order Sheet Pickup!.',
                    )
                );
            }
        }


        print json_encode($arr_result);
    }


    public function edit_post()
    {
        $nik  = $this->input->post('nik');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $isSudahNa  = $this->input->post('isSudahNa');
        $keterangan_ops  = $this->input->post('keterangan_ops');
        $nameAgen  = $this->input->post('nameAgen');
        $jumlah_barang  = $this->input->post('jumlah_barang');
        $jumlah_barang_hvs  = $this->input->post('jumlah_barang_hvs');
        $jumlah_barang_non  = $this->input->post('jumlah_barang_non');
        $idRitase  = $this->input->post('idRitase');
        $stopTypeCode  = $this->input->post('stopTypeCode');
        $idNa  = $this->input->post('idNa');

        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

        if ($employee->branch_code == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki BRANCH!.',
                )
            );
        } elseif ($stopTypeCode == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Anda tidak memilih jenis pemberhentian.',
                )
            );
        } else {
            $where['order_sheet_pickup_id']                             = $idNa;
            $data['order_sheet_pickup_status']       = $stopTypeCode;


            if ($this->db->update('order_sheet_pickup', $data, $where)) {

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
                $areakerja_id = 0;
                if ($stopTypeCode == 'STCST') {
                    if ($latitude == null || $longitude == null || $latitude == 0 || $longitude == 0  || $latitude == "null" || $longitude == "null" || $latitude == "" || $longitude == "") {
                        $areakerja_id = 0;
                    } else {
                        $chekjarak = $this->Mabsensi->checkJarakGlobalAreaNew(@$employee->branch_code, @$latitude, @$longitude);
                        if (empty($chekjarak)) {
                            $areakerja_id = 0;
                        } else {
                            $areakerja_id = $chekjarak->areakerja_id;
                        }
                    }
                }

                $dataDetail['order_sheet_pickup_id']                    = $idNa;
                $dataDetail['order_sheet_pickup_detail_status']         = $stopTypeCode;
                $dataDetail['order_sheet_pickup_detail_created_date']   = date('Y-m-d H:i:s');
                $dataDetail['order_sheet_pickup_detail_id_ritase']      = $idRitase;
                $dataDetail['order_sheet_pickup_detail_jumlah']         = $jumlah_barang;
                $dataDetail['order_sheet_pickup_detail_jumlah_hvs']     = $jumlah_barang_hvs;
                $dataDetail['order_sheet_pickup_detail_jumlah_total']   = $jumlah_barang_non;
                $dataDetail['order_sheet_pickup_detail_foto']           = $fileName;
                $dataDetail['order_sheet_pickup_detail_keterangan']     = $keterangan_ops;
                $dataDetail['latitude']                                 = $latitude;
                $dataDetail['longitude']                                = $longitude;
                $dataDetail['area_kerja']                               = $areakerja_id;
                $dataDetail['nama_agen']                                = $nameAgen;

                if ($this->db->insert('order_sheet_pickup_detail', $dataDetail)) {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'success',
                            'message' => 'Order Sheet Pickup Berhasil disimpan.',

                        )
                    );
                } else {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Order Sheet Pickup Detail Gagal disimpan.',
                        )
                    );
                }
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal Menambahkan Data Order Sheet Pickup!.',
                    )
                );
            }
        }


        print json_encode($arr_result);
    }

    public function update_post()
    {
        $nik  = $this->input->post('nik');
        $latitude  = $this->input->post('latitude');
        $longitude  = $this->input->post('longitude');
        $isSudahNa  = $this->input->post('isSudahNa');
        $keterangan_ops  = $this->input->post('keterangan_ops');
        $nameAgen  = $this->input->post('nameAgen');
        $jumlah_barang  = $this->input->post('jumlah_barang');
        $idRitase  = $this->input->post('idRitase');
        $stopTypeCode  = $this->input->post('stopTypeCode');
        $idNa  = $this->input->post('idNa');

        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

        if ($employee->branch_code == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki BRANCH!.',
                )
            );
        } elseif ($stopTypeCode == null) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Anda tidak memilih jenis pemberhentian.',
                )
            );
        } else {
            $where['order_sheet_pickup_id']                             = $idNa;
            $data['order_sheet_pickup_status']       = $stopTypeCode;


            if ($this->db->update('order_sheet_pickup', $data, $where)) {

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
                $areakerja_id = 0;
                if ($stopTypeCode == 'STCST') {
                    if ($latitude == null || $longitude == null || $latitude == 0 || $longitude == 0  || $latitude == "null" || $longitude == "null" || $latitude == "" || $longitude == "") {
                        $areakerja_id = 0;
                    } else {
                        $chekjarak = $this->Mabsensi->checkJarakGlobalAreaNew(@$employee->branch_code, @$latitude, @$longitude);
                        if (empty($chekjarak)) {
                            $areakerja_id = 0;
                        } else {
                            $areakerja_id = $chekjarak->areakerja_id;
                        }
                    }
                }

                $dataDetail['order_sheet_pickup_id']                    = $idNa;
                $dataDetail['order_sheet_pickup_detail_status']         = $stopTypeCode;
                $dataDetail['order_sheet_pickup_detail_created_date']   = date('Y-m-d H:i:s');
                $dataDetail['order_sheet_pickup_detail_id_ritase']      = $idRitase;
                $dataDetail['order_sheet_pickup_detail_jumlah']         = $jumlah_barang;
                $dataDetail['order_sheet_pickup_detail_foto']           = $fileName;
                $dataDetail['order_sheet_pickup_detail_keterangan']     = $keterangan_ops;
                $dataDetail['latitude']                                 = $latitude;
                $dataDetail['longitude']                                = $longitude;
                $dataDetail['area_kerja']                               = $areakerja_id;
                $dataDetail['nama_agen']                                = $nameAgen;

                if ($this->db->insert('order_sheet_pickup_detail', $dataDetail)) {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'success',
                            'message' => 'Order Sheet Pickup Berhasil disimpan.',

                        )
                    );
                } else {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'error',
                            'message' => 'Order Sheet Pickup Detail Gagal disimpan.',
                        )
                    );
                }
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal Menambahkan Data Order Sheet Pickup!.',
                    )
                );
            }
        }


        print json_encode($arr_result);
    }


    public function find_history_new_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'order_sheet_pickup_id';
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
        $data_aktivitas = $this->MOsp->findHs($nik, 'result', $option);
        $data_stop = $this->MOsp->findSt($nik, 'result', $option);
        $data_not_stop = $this->MOsp->find_not_fin($nik, 'result', $option);

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


    public function export_post()
    {
        $nik  = $this->input->post('nik');

        $data_aktivitas = $this->MOsp->findExp($nik);

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


    public function dataDetail_post()
    {
        $data_aktivitas = $this->MOsp->findDetailHs();
        $data_list = $this->MOsp->findList();

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
                    'data_list'     => $data_list,
                )
            );
        }

        print json_encode($arr_result);
    }
}