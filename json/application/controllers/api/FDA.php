<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class FDA extends REST_Controller
{

    private $arr_result         = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MFDA');
        $this->load->model('Mpengguna');
        $this->load->model('Mnotification');

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



    public function find_category_detail_post()
    {

        $data = $this->MFDA->findCategoryDetail();

        $arr_result = array();
        if (empty($data)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Category fda tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data category fda ditemukan.',
                    'data'     => $data,
                )
            );
        }

        print json_encode($arr_result);
    }
    public function find_category_post()
    {

        $data = $this->MFDA->findCategory();

        $arr_result = array();
        if (empty($data)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Category fda tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data category fda ditemukan.',
                    'data'     => $data,
                )
            );
        }

        print json_encode($arr_result);
    }
    public function check_fda_post()
    {

        $data = $this->MFDA->checkFDA();

        $arr_result = array();
        if ($data > 0) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Data FDA sudah ada, silahkan ganti tanggal FDA!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data category fda ditemukan.',
                )
            );
        }

        print json_encode($arr_result);
    }


    public function batalkan_post()
    {
        $idNa  = $this->input->post('idNa');
        $nik  = $this->input->post('nik');

        $where = array('fda_request_id' => $idNa,);
        $data['fda_request_status']            = 'Dibatalkan';
        if ($this->db->update('fda_request', $data, $where)) {

            $dataDetail['fda_request_id']                                = $idNa;
            $dataDetail['fda_request_history_created_date']              = date('Y-m-d H:i:s');
            $dataDetail['fda_request_status']                            = 'Dibatalkan';
            $dataDetail['fda_category_id']                               = '';
            $dataDetail['nik']                                           = $nik;

            if ($this->db->insert('fda_request_history', $dataDetail)) {
                $dataRe['status_reset'] = 'Dibatalkan';
                $whereRe['nik'] = $nik;
                $whereRe['reset_type'] = 'fda';
                $whereRe['extra'] = $idNa;

                $this->db->update('user_reset', $dataRe, $whereRe);

                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => ' Berhasil disimpan.',

                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Gagal disimpan!.',
                    )
                );
            }
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan!.',
                )
            );
        }
        print json_encode($arr_result);
    }


    public function req_fda_post()
    {
        $nik  = $this->input->post('nik');
        $isSudahNa  = $this->input->post('isSudahNa');
        $keterangan  = $this->input->post('keterangan');
        $lisTanggalSend  = $this->input->post('lisTanggalSend');
        $listIDSend  = $this->input->post('listIDSend');
        $listIDDESCSend  = $this->input->post('listIDDESCSend');

        $tanggalFda  = explode('#', $lisTanggalSend, -1);
        $idCategory  = explode('#', $listIDSend, -1);
        $idDesc      = explode('#', $listIDDESCSend, -1);


        $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();

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
                $fileGAMBAR = fopen('../uploads/image_absensi/' . $fileName, 'wb');
                // Create File
                fwrite($fileGAMBAR, $binaryImage);
                fclose($fileGAMBAR);
                $binryImage = base64_decode($fileImage);
            } else {
                $fileName = "";
            }
            $i = 0;
            $total = count($tanggalFda);
            foreach ($tanggalFda as $tanggalFda) {
                // $this->db->where('date(fda_request_date)', $tanggalFda);
                // $this->db->where('fda_request_nik', $nik);
                // $this->db->where('fda_request_status', "Open");
                // $this->db->order_by('fda_request_id', 'DESC');
                // $dataFDA = $this->db->get('fda_request')->row();

                $data['fda_request_nik']               = $nik;
                $data['fda_request_created_date']      = date('Y-m-d H:i:s');
                $data['fda_request_date']              = $tanggalFda;
                $data['fda_request_image']             = $fileName;
                $data['fda_request_desciprtian']       = $keterangan;
                $data['fda_request_status']            = 'Open';
                $data['fda_category_id']               = $idCategory[$i];

                // if (empty($dataFDA)) {
                $this->db->insert('fda_request', $data);
                @$insert_id = $this->db->insert_id();
                // } else {
                //     $where['fda_request_id']       = $dataFDA->fda_request_id;

                //     $this->db->update('fda_request', $data, $where);
                //     @$insert_id = $dataFDA->fda_request_id;
                // }



                $dataDetail['fda_request_id']                                = $insert_id;
                $dataDetail['fda_request_history_created_date']              = date('Y-m-d H:i:s');
                $dataDetail['fda_request_status']                            = 'Open';
                $dataDetail['fda_category_id']                               = $idCategory[$i];
                $dataDetail['nik']                                           = $nik;

                if (empty($dataFDA)) {
                    $this->db->insert('fda_request_history', $dataDetail);
                } else {
                    $whereDetail['fda_request_id']       = $dataFDA->fda_request_id;
                    $this->db->update('fda_request_history', $dataDetail, $whereDetail);
                }


                $dataRe['nik'] = $nik;
                $dataRe['reason_reset'] = 'FDA - ' . $idDesc[$i];
                $dataRe['keterangan'] = $keterangan;
                $dataRe['status_reset'] = 'Pengajuan';
                $dataRe['reset_type'] = 'fda';
                $dataRe['extra'] = $insert_id;
                $dataRe['date_created'] = date('Y-m-d H:i;s');
                $dataRe['nik_approval'] = '';
                $dataRe['approval_date'] = '';
                $dataRe['data_update'] = '';

                // if (empty($dataFDA)) {
                $this->db->insert('user_reset', $dataRe);
                // } else {
                //     $whereReset['fda_request_id']       = $dataFDA->fda_request_id;
                //     $whereReset['reset_type']       = 'fda';
                //     $this->db->update('user_reset', $dataRe, $whereReset);
                // }


                $i++;
            }

            // if (empty($dataFDA)) {
            //     $dataNAA = $this->getTokenAll($employee->branch_code, $employee->section_code, $nik, $insert_id);
            //     foreach (@$dataNAA as $peng) {
            //         $this->Mnotification->sendToTopic(@$peng->token, 'Pengajuan Approval FDA Baru', 'Pengajuan Dari ' . $employee->employee_name . ' Approval FDA Baru Silahkan dicek terlebih dahulu.', 9, $insert_id);
            //     }
            // }


            if ($i == $total - 1) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => ' Berhasil disimpan.',

                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => ' Berhasil disimpan.',

                    )
                );
            }
        }


        print json_encode($arr_result);
    }


    public function getTokenAll($branch_code, $section_code, $nik, $insert_id)
    {
        $query = array(
            'nik' => $nik,
        );
        $data_user = $this->Mpengguna->findMyhc($query, 'result');
        $no = 0;
        foreach ($data_user as $user) {
            if ($no == 0) {
                $select = "";
                $queryK = array(
                    'nik' => $nik,
                    'user_type' => 'KOORDINATOR',
                );
                $dataK = $this->Mpengguna->findMyhcNew($queryK, 'result');
                if (!empty($dataK)) {
                    $ids = array('JRSPV');
                    $this->db->where('ep.branch_code', $branch_code);
                    $this->db->where('ep.section_code', $section_code);
                    $this->db->where('ep.unit_code', $user->unit_code);
                    $this->db->where('ep.subunit_code', $user->subunit_code);
                    $nikNEW = array($nik);
                    $this->db->select("ft.token");
                    $this->db->select("ep.employee_name");
                    $this->db->where_in('user.user_type', $ids);
                    $this->db->where_not_in('user.nik', $nikNEW);
                    $this->db->where('user.user_application', 'MYHC');
                    $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                    $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                    $this->db->group_by("user.nik");
                    return $this->db->get('user')->result();
                } else {

                    $queryK = array(
                        'nik' => $nik,
                        'user_type' => 'JRSPV',
                    );
                    $dataK = $this->Mpengguna->findMyhcNew($queryK, 'result');
                    if (!empty($dataK)) {
                        $ids = array('SPV');
                        $this->db->where('ep.branch_code', $branch_code);
                        $this->db->where('ep.section_code', $section_code);
                        $this->db->where('ep.unit_code', $user->unit_code);
                        $this->db->where('ep.subunit_code', $user->subunit_code);
                        $nikNEW = array($nik);
                        $this->db->select("ft.token");
                        $this->db->select("ep.employee_name");
                        $this->db->where_in('user.user_type', $ids);
                        $this->db->where_not_in('user.nik', $nikNEW);
                        $this->db->where('user.user_application', 'MYHC');
                        $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                        $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                        $this->db->group_by("user.nik");
                        return $this->db->get('user')->result();
                    } else {
                        $queryK = array(
                            'nik' => $nik,
                            'user_type' => 'SPV',
                        );
                        $dataK = $this->Mpengguna->findMyhcNew($queryK, 'result');
                        if (!empty($dataK)) {
                            $ids = array('SPV');
                            $this->db->where('ep.branch_code', $branch_code);
                            $this->db->where('ep.section_code', $section_code);
                            $this->db->where('ep.unit_code', $user->unit_code);
                            $this->db->where('ep.subunit_code', $user->subunit_code);
                            $nikNEW = array($nik);
                            $this->db->select("ft.token");
                            $this->db->select("ep.employee_name");
                            $this->db->where_in('user.user_type', $ids);
                            $this->db->where_not_in('user.nik', $nikNEW);
                            $this->db->where('user.user_application', 'MYHC');
                            $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                            $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                            $this->db->group_by("user.nik");
                            return $this->db->get('user')->result();
                        } else {
                            $ids = array('KOORDINATOR');
                            $this->db->where('ep.branch_code', $branch_code);
                            $this->db->where('ep.section_code', $section_code);
                            $this->db->where('ep.subunit_code', $user->subunit_code);
                            $nikNEW = array($nik);
                            $this->db->select("ft.token");
                            $this->db->select("ep.employee_name");
                            $this->db->where_in('user.user_type', $ids);
                            $this->db->where_not_in('user.nik', $nikNEW);
                            $this->db->where('user.user_application', 'MYHC');
                            $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                            $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                            $this->db->group_by("user.nik");
                            $dataEm = $this->db->get('user')->result();
                            if (empty($dataEm)) {
                                $this->db->where('ep.branch_code', $branch_code);
                                $this->db->where('ep.section_code', $section_code);
                                $nikNEW = array($nik);
                                $this->db->select("ft.token");
                                $this->db->select("ep.employee_name");
                                $this->db->where_not_in('user.nik', $nikNEW);
                                $this->db->where('user.user_application', 'MYHC');
                                $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                                $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                                $this->db->group_by("user.nik");
                                return $dataEm = $this->db->get('user')->result();
                            } else {
                                return $dataEm;
                            }
                        }
                    }
                }
            }
            $no = 1;
        }
    }


    public function export_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'fda_request_id';
        $ordering            = "desc";

        if (isset($_POST['order_by'])) {
            $order_by = $this->input->post('order_by');
        }

        if (isset($_POST['ordering'])) {
            $ordering = $this->input->post('ordering');
        }



        $option = array(
            'order' => array(
                'order_by' => $order_by,
                'ordering' => $ordering,
            ),
        );
        $data_aktivitas = $this->MFDA->findExport($nik, 'result', $option);

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
        $order_by            = 'fda_request_id';
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
        $data_aktivitas = $this->MFDA->findHs($nik, 'result', $option);
        $data_all = $this->MFDA->totalFDA("ALL");
        $data_open = $this->MFDA->totalFDA("Open");
        $data_reject = $this->MFDA->totalFDA("Ditolak");
        $data_canceled = $this->MFDA->totalFDA("Dibatalkan");
        $data_approved = $this->MFDA->totalFDA("Approved");

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
                    'data_all'     => $data_all,
                    'data_open'     => $data_open,
                    'data_reject'     => $data_reject,
                    'data_canceled'     => $data_canceled,
                    'data_approved'     => $data_approved,
                )
            );
        }

        print json_encode($arr_result);
    }
}