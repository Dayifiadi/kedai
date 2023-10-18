<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Pengguna extends REST_Controller
{

    private $arr_result         = array();

    private $field_list         = array(
        'nik', 'nama_lengkap',
        'foto_profile', 'dept_id', 'status_karyawan_id',
        'modul_id', 'device_id'
    );
    private $required_field     = array('nik', 'nama_lengkap', 'foto_profile');
    private $md5_field          = array('password');
    private $primary_key        = 'id_pengguna';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mpengguna');
        $this->load->model('Mnotification');
        $this->load->model('MBbm');
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

    public function findLogin_post()
    {
        $nik  = $this->input->post('nik');
        $modul_id = $this->input->post('modul_id');
        $device_id = md5($this->input->post('device_id'));

        $arr_result = array();
        if ($nik == "0") {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'logout',
                    'message' => 'Silahkan Coba Lagi'
                )
            );
            // } elseif ($modul_id == 0) {
            //     $arr_result = array(
            //     'jne_ittsm' => array(
            //         'status' => 'error',
            //         'message' => 'Silahkan Coba Lagi'
            //     )
            // );
        } else {
            $query = array(
                'nik' => $nik,
            );
            $data_user = $this->Mpengguna->find($query, 'row');
            if (empty($data_user)) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'logout',
                        'message' => 'Data tidak ditemukan.'
                    )
                );
            } else {
                //          if (@$data_user->employee_version_app=="" || @$data_user->employee_version_app=="null" || @$data_user->employee_version_app==0) {
                //            $arr_result = array(
                //     'jne_ittsm' => array(
                //         'status' => 'logout',
                //         'message' => 'Aplikasi Android anda belum UPDATE silahkan diupdate terlebih dahulu!.'
                //     )
                // );
                //          }else {
                if (@$data_user->user_deviceid == "" || @$data_user->user_deviceid == "null") {
                    $arr_result = array(
                        'jne_ittsm' => array(
                            'status' => 'logout',
                            'message' => 'Anda sudah otomatis logout, akun anda sudah login di device lain.'
                        )
                    );
                } else {
                    if ($data_user->branch_code == "TSM000") {
                        // if (@$data_user->employee_version_app == "" || @$data_user->employee_version_app == "null" || @$data_user->employee_version_app != 3) {
                        //     $arr_result = array(
                        //         'jne_ittsm' => array(
                        //             'status' => 'logout',
                        //             'message' => 'Aplikasi Android anda belum UPDATE silahkan diupdate terlebih dahulu!.'
                        //         )
                        //     );
                        // } else {
                        if (@$data_user->user_deviceid == $device_id) {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'success',
                                    'message' => 'Data berhasil ditemukan.',
                                    'data'    =>  $data_user,
                                )
                            );
                        } else {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'logout',
                                    'message' => 'Anda sudah otomatis logout, akun anda sudah login di device lain.'
                                )
                            );
                        }
                        // }
                    } else {
                        if (@$data_user->user_deviceid == $device_id) {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'success',
                                    'message' => 'Data berhasil ditemukan.',
                                    'data'    =>  $data_user,
                                )
                            );
                        } else {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'logout',
                                    'message' => 'Anda sudah otomatis logout, akun anda sudah login di device lain.'
                                )
                            );
                        }
                    }
                }
                // }

            }
        }

        print json_encode($arr_result);
    }

    public function findNew_post()
    {
        $nik  = $this->input->post('nik');

        $arr_result = array();
        if ($nik == "0") {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Silahkan Coba Lagi'
                )
            );
        } else {
            $query = array(
                'nik' => $nik,
            );
            $data_user = $this->Mpengguna->find($query, 'row');
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

    public function reset_pengajuan_post()
    {
        $nik  = $this->input->post('nik');

        $arr_result = array();
        if ($nik == "0") {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Silahkan Coba Lagi'
                )
            );
        } else {
            $query = array(
                'nik' => $nik,
            );
            $data_user = $this->Mpengguna->find($query, 'row');
            if (empty($data_user)) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Nik/Username tidak ditemukan!'
                    )
                );
            } else {
                $reset_type  = $this->input->post('reset_type');
                if ($reset_type == 'nopol') {
                    $data['nik'] = $nik;
                    $data['reason_reset'] = 'Reset Armada BBM';
                    $data['keterangan'] = 'Reset Armada BBM, Karena Armada belum di STOP oleh pengguna.';
                    $data['status_reset'] = 'Pengajuan';
                    $data['reset_type'] = $this->input->post('reset_type');
                    $data['extra'] = $this->input->post('extra');
                    $data['date_created'] = date('Y-m-d H:i;s');
                    $data['nik_approval'] = '';
                    $data['approval_date'] = '';
                    $data['data_update'] = '';
                } elseif ($reset_type == 'device') {
                    $data['nik'] = $nik;
                    $data['reason_reset'] = $this->input->post('reason');
                    $data['keterangan'] = $this->input->post('keterangan');
                    $data['reset_type'] = $this->input->post('reset_type');
                    $data['extra'] = '';
                    $data['status_reset'] = 'Pengajuan';
                    $data['date_created'] = date('Y-m-d H:i;s');
                    $data['nik_approval'] = '';
                    $data['approval_date'] = '';
                    $data['data_update'] = '';
                } elseif ($reset_type == 'km') {
                    $data['nik'] = $nik;
                    $data['reason_reset'] = 'Update Data BBM Armada';
                    $data['keterangan'] = 'Update Data BBM Armada, Karena salah pengisian Kilometer/Rupiah BBM.';
                    $data['status_reset'] = 'Pengajuan';
                    $data['reset_type'] = $this->input->post('reset_type');
                    $data['extra'] = $this->input->post('extra');
                    $data['data_update'] = $this->input->post('data_update');
                    $data['date_created'] = date('Y-m-d H:i;s');
                    $data['nik_approval'] = '';
                    $data['approval_date'] = '';
                }

                // $dataNAA = $this->getTokenAll($data_user->branch_code, $data_user->section_code, $nik);
                // // foreach (@$dataNAA as $peng) {
                // //     $this->Mnotification->sendToTopic(@$peng->token, 'Pengajuan Approval Update Baru', 'Pengajuan Approval Update Baru Silahkan dicek terlebih dahulu.', '9', $insert_id);
                // // }
                // $arr_result = array(
                //     'jne_ittsm' => array(
                //         'status' => 'success',
                //         'message' => 'Data berhasil ditambahkan.',
                //         'data' => $dataNAA,
                //     )
                // );
                // print json_encode($arr_result);
                // die();

                $dataReset = $this->Mpengguna->findResetDup();
                if (empty($dataReset)) {
                    if ($this->db->insert('user_reset', $data)) {
                        $insert_id = $this->db->insert_id();

                        // $dataNAA = $this->getTokenAll($data_user->branch_code, $data_user->section_code, $nik, $insert_id, $data_user);
                        // foreach (@$dataNAA as $peng) {
                        //     $this->Mnotification->sendToTopic(@$peng->token, 'Pengajuan Approval Update Baru', 'Pengajuan Dari ' . $data_user->employee_name . ' Approval Update Baru Silahkan dicek terlebih dahulu.', 9, $insert_id);
                        // }
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'success',
                                'message' => 'Data berhasil ditambahkan.',
                            )
                        );
                    } else {
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'error',
                                'message' => 'Silahkan Coba Lagi'
                            )
                        );
                    }
                } else {
                    $where['user_reset_id'] = $dataReset->user_reset_id;
                    if ($this->db->update('user_reset', $data, $where)) {

                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'success',
                                'message' => 'Data berhasil ditambahkan.',
                            )
                        );
                    } else {
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'error',
                                'message' => 'Silahkan Coba Lagi'
                            )
                        );
                    }
                }
            }
        }

        print json_encode($arr_result);
    }

    public function getTokenAll($branch_code, $section_code, $nik, $insert_id, $employee)
    {
        $query = array(
            'nik' => $nik,
        );
        $data_user = $this->Mpengguna->findMyhc($query, 'result');
        $no = 0;
        foreach ($data_user as $user) {
            if ($no == 0) {
                $select = "";
                if ($branch_code == 'BDO000') {
                    $ids = array('HUMANCAPITAL');
                    $this->db->where('ep.branch_code', $branch_code);
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
                    $queryH = array(
                        'nik' => $nik,
                        'user_type' => 'HUMANCAPITAL',
                    );

                    $dataH = $this->Mpengguna->findMyhcNew($queryH, 'result');

                    if (!empty($dataH)) {
                        $ids = array('HUMANCAPITAL', 'ADMIN', 'KOORDINATOR');
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
                            'user_type' => 'KOORDINATOR',
                        );

                        $dataK = $this->Mpengguna->findMyhcNew($queryK, 'result');
                        if (!empty($dataK)) {


                            $ids = array('HUMANCAPITAL');
                            $this->db->where('ep.branch_code', $branch_code);
                            $nikNEW = array($nik);
                            $this->db->select("ft.token");
                            $this->db->select("ep.employee_name");
                            $this->db->where_in('user.user_type', $ids);
                            $this->db->where_not_in('user.nik', $nikNEW);
                            $this->db->where('user.user_application', 'MYHC');
                            $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                            $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                            $this->db->group_by("user.nik");
                            $dataNAA = $this->db->get('user')->result();
                            foreach (@$dataNAA as $peng) {
                                $this->Mnotification->sendToTopic(@$peng->token, 'Pengajuan Approval Update Baru', 'Pengajuan Dari ' . $employee->employee_name . ' Approval Update Baru Silahkan dicek terlebih dahulu.', '9', $insert_id);
                            }

                            $ids = array('SPV', 'JRSPV');
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


                            $ids = array('HUMANCAPITAL');
                            $this->db->where('ep.branch_code', $branch_code);
                            $nikNEW = array($nik);
                            // $nikNEW = array('TSM21E27M2930');

                            $this->db->select("ft.token");
                            $this->db->select("ep.employee_name");
                            $this->db->where_in('user.user_type', $ids);
                            $this->db->where_not_in('user.nik', $nikNEW);
                            // $this->db->where_in('user.nik', $nikNEW);
                            $this->db->where('user.user_application', 'MYHC');
                            $this->db->join('employee ep', 'user.nik = ep.nik', 'left');
                            $this->db->join('firebase_token ft', 'user.nik = ft.nik');
                            $this->db->group_by("user.nik");
                            $dataNAA = $this->db->get('user')->result();
                            foreach (@$dataNAA as $peng) {
                                $this->Mnotification->sendToTopic(@$peng->token, 'Pengajuan Approval Update Baru', 'Pengajuan Dari ' . $employee->employee_name . ' Approval Update Baru Silahkan dicek terlebih dahulu.', '9', $insert_id);
                            }


                            $ids = array('ADMIN', 'KOORDINATOR');
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
                        }
                    }
                }
            }
            $no = 1;
        }
    }
    public function approveReset_post()
    {
        $nik  = $this->input->post('nik');

        $arr_result = array();
        if ($nik == "0") {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Silahkan Coba Lagi'
                )
            );
        } else {
            $query = array(
                'nik' => $nik,
            );
            $data_user = $this->Mpengguna->find($query, 'row');
            if (empty($data_user)) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'error',
                        'message' => 'Nik/Username tidak ditemukan!'
                    )
                );
            } else {
                $this->db->where('user_reset_id', $this->input->post('idNa'));
                $dataNa = $this->db->get('user_reset')->row();
                $this->db->where('nik', $this->input->post('nikApprove'));
                $userApproval = $this->db->get('employee')->row();

                if ($this->input->post('status') == 'tolak') {
                    $where['user_reset_id'] = $this->input->post('idNa');
                    $data['status_reset'] = 'Ditolak';
                    $data['nik_approval'] = $this->input->post('nikApprove');
                    $data['approval_date'] = date('Y-m-d H:i:s');

                    if ($this->db->update('user_reset', $data, $where)) {
                        if ($dataNa->reset_type == 'fda') {
                            $whereFda = array('fda_request_id' => $dataNa->extra,);
                            $dataFda['fda_request_status']            = 'Ditolak';
                            $dataFda['fda_request_nik_approval']      = $this->input->post('nikApprove');
                            $this->db->update('fda_request', $dataFda, $whereFda);

                            $dataDetail['fda_request_id']                                = $dataNa->extra;
                            $dataDetail['fda_request_history_created_date']              = date('Y-m-d H:i:s');
                            $dataDetail['fda_request_status']                            = 'Ditolak';
                            $dataDetail['fda_category_id']                               = '';
                            $dataDetail['nik']                                           = $this->input->post('nikApprove');
                            $this->db->insert('fda_request_history', $dataDetail);
                            $dataNAA = $this->getTokenAllRow($nik);
                            $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan FDA Ditolak', 'Pengajuan FDA Ditolak, Oleh ' . $userApproval->employee_name . '.', '11', '0');
                        } else {
                            if ($dataNa->reset_type == 'device') {
                                $dataNAA = $this->getTokenAllRow($nik);
                                $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan Approval Update Ditolak', 'Pengajuan Approval Update Ditolak, Oleh ' . $userApproval->employee_name . '.', '9', '0');
                            } else {
                                $dataNAA = $this->getTokenAllRow($nik);
                                $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan Approval Update Ditolak', 'Pengajuan Approval Update Ditolak, Oleh ' . $userApproval->employee_name . '.', '0', '0');
                            }
                        }

                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'success',
                                'message' => 'Data berhasil ditambahkan.',
                            )
                        );
                    } else {
                        $arr_result = array(
                            'jne_ittsm' => array(
                                'status' => 'error',
                                'message' => 'Silahkan Coba Lagi'
                            )
                        );
                    }
                } else {
                    if ($dataNa->reset_type == 'device') {
                        if ($dataNa->reason_reset == 'Reset Keduanya') {
                            $dataUser['user_password']  = md5('123456');
                            $dataUser['user_deviceid']  = '';
                        } elseif ($dataNa->reason_reset == 'Reset Device ID') {
                            $dataUser['user_deviceid']  = '';
                        } elseif ($dataNa->reason_reset == 'Reset Password') {
                            $dataUser['user_password']  = md5('123456');
                        }

                        $whereUser['nik'] = $this->input->post('nik');
                        $where['user_reset_id'] = $this->input->post('idNa');
                        $data['status_reset'] = 'Approved';
                        $data['nik_approval'] = $this->input->post('nikApprove');
                        $data['approval_date'] = date('Y-m-d H:i:s');

                        if ($this->db->update('user_reset', $data, $where)) {
                            $this->db->update('user', $dataUser, $whereUser);
                            $dataNAA = $this->getTokenAllRow($nik);
                            $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan Approval Update Approved', 'Pengajuan Approval Approved, Oleh ' . $userApproval->employee_name . '.', '9', '0');
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'success',
                                    'message' => 'Data berhasil ditambahkan.',
                                )
                            );
                        } else {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Silahkan Coba Lagi'
                                )
                            );
                        }
                    } elseif ($dataNa->reset_type == 'nopol') {
                        if ($dataNa->reason_reset == 'Reset Armada BBM') {
                            $dataTrans['transportation_is_running']  = '0';
                            $whereTrans['transportation_nopol'] = $dataNa->extra;
                        }

                        $where['user_reset_id'] = $this->input->post('idNa');
                        $data['status_reset'] = 'Approved';
                        $data['nik_approval'] = $this->input->post('nikApprove');
                        $data['approval_date'] = date('Y-m-d H:i:s');

                        if ($this->db->update('user_reset', $data, $where)) {
                            $this->db->update('transportation', $dataTrans, $whereTrans);
                            $dataNAA = $this->getTokenAllRow($nik);
                            $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan Approval Update Approved', 'Pengajuan Approval Approved, Oleh ' . $userApproval->employee_name . '.', '0', '0');
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'success',
                                    'message' => 'Data berhasil ditambahkan.',
                                )
                            );
                        } else {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Silahkan Coba Lagi'
                                )
                            );
                        }
                    } elseif ($dataNa->reset_type == 'km') {
                        if ($dataNa->reason_reset == 'Update Data BBM Armada') {
                            $dataUpdateNa = $dataNa->data_update;
                            $dataNaSlice  = explode('#', $dataUpdateNa, 2);
                            $idBBM = $this->MBbm->ambilIDNA($dataNa->extra);

                            $nopolNa        = $this->db->get_where('transportation_fuel', ['transportation_fuel_id' => $idBBM->transportation_fuel_id])->row();
                            $codeNopol        = $this->db->get_where('transportation', ['transportation_nopol' => $nopolNa->transportation_fuel_nopol])->row();
                            $literBBMNa        = $this->db->get_where('transportation_type', ['transportation_type_code' => $codeNopol->transportation_type_code])->row();
                            $literNUMB = number_format(@$dataNaSlice[1] / $literBBMNa->transportation_type_fuel_cost, 1, '.', '.');
                            $dataDetail['transportation_fuel_detail_km'] = @$dataNaSlice[0];
                            $dataDetail['transportation_fuel_detail_price'] = @$dataNaSlice[1];
                            $dataDetail['transportation_fuel_detail_bbm'] = @$literNUMB;
                            // $dataDetail['transportation_fuel_detail_distance'] = @$kmNow;
                            $whereDetail['transportation_fuel_detail_id'] = $dataNa->extra;

                            $this->db->update('transportation_fuel_detail', $dataDetail, $whereDetail);

                            $lastKM = $this->MBbm->ambilLastKmNa($idBBM->transportation_fuel_id);
                            $firstKM = $this->MBbm->ambilFirst($idBBM->transportation_fuel_id);
                            $sum = $this->MBbm->sumAllBBM($idBBM->transportation_fuel_id);
                            $sumLiter = $this->MBbm->sumAllLiter($idBBM->transportation_fuel_id);
                            $sumKM = $this->MBbm->sumKmNa($idBBM->transportation_fuel_id);

                            if (empty($lastKM)) {
                                $kmNa = 0;
                                $kmDiscLast = 0;
                            } else {
                                if ($lastKM->transportation_fuel_detail_km == '') {
                                    $kmNa = 0;
                                } else {
                                    $kmNa = $lastKM->transportation_fuel_detail_km;
                                }
                                if ($lastKM->transportation_fuel_detail_distance == '') {
                                    $kmDiscLast = 0;
                                } else {
                                    $kmDiscLast = $lastKM->transportation_fuel_detail_distance;
                                }
                            }
                            if (empty($firstKM)) {
                                $kmFirst = 0;
                                $kmDiscFirst = 0;
                            } else {
                                if ($firstKM->transportation_fuel_detail_km == '') {
                                    $kmFirst = 0;
                                } else {
                                    $kmFirst = $firstKM->transportation_fuel_detail_km;
                                }
                                if ($firstKM->transportation_fuel_detail_distance == '') {
                                    $kmDiscFirst = 0;
                                } else {
                                    $kmDiscFirst = $firstKM->transportation_fuel_detail_distance;
                                }
                            }

                            // echo $idBBM->transportation_fuel_id . "<br>";
                            // echo $kmNa . "<br>";
                            // echo $kmDiscLast . "<br>";
                            // echo $kmFirst . "<br>";
                            // echo $kmDiscFirst . "<br>";
                            // die();


                            $dataFuel['transportation_fuel_total_distance']        = @$kmDiscLast - @$kmDiscFirst;
                            $dataFuel['transportation_fuel_total_km']        = @$sumKM->totalKm;
                            $dataFuel['transportation_fuel_total_bbm']        = number_format(@$sumLiter->totalLiter, 1, '.', '.');
                            $dataFuel['transportation_fuel_total_price']        = @$sum->totalHarga;
                            $dataFuel['last_km']        = @$lastKM->transportation_fuel_detail_km;

                            $whereFuel['transportation_fuel_id']             = $idBBM->transportation_fuel_id;
                        }

                        $where['user_reset_id'] = $this->input->post('idNa');
                        $data['status_reset'] = 'Approved';
                        $data['nik_approval'] = $this->input->post('nikApprove');
                        $data['approval_date'] = date('Y-m-d H:i:s');
                        if ($this->db->update('user_reset', $data, $where)) {
                            $this->db->update('transportation_fuel', $dataFuel, $whereFuel);
                            $dataNAA = $this->getTokenAllRow($nik);
                            $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan Approval Update Approved', 'Pengajuan Approval Approved, Oleh ' . $userApproval->employee_name . '.', '0', '0');
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'success',
                                    'message' => 'Data berhasil ditambahkan.',
                                )
                            );
                        } else {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Silahkan Coba Lagi'
                                )
                            );
                        }
                    } else if ($dataNa->reset_type == 'fda') {
                        $whereUser['nik'] = $this->input->post('nik');
                        $where['user_reset_id'] = $this->input->post('idNa');
                        $data['status_reset'] = 'Approved';
                        $data['nik_approval'] = $this->input->post('nikApprove');
                        $data['approval_date'] = date('Y-m-d H:i:s');

                        if ($this->db->update('user_reset', $data, $where)) {
                            $whereFda = array('fda_request_id' => $dataNa->extra,);
                            $dataFda['fda_request_status']                  = 'Approved';
                            $dataFda['fda_request_nik_approval']            = $this->input->post('nikApprove');
                            $dataFda['fda_request_approval_date']           = date('Y-m-d');

                            $this->db->update('fda_request', $dataFda, $whereFda);

                            $dataDetail['fda_request_id']                                = $dataNa->extra;
                            $dataDetail['fda_request_history_created_date']              = date('Y-m-d H:i:s');
                            $dataDetail['fda_request_status']                            = 'Approved';
                            $dataDetail['fda_category_id']                               = '';
                            $dataDetail['nik']                                           = $this->input->post('nikApprove');
                            $this->db->insert('fda_request_history', $dataDetail);

                            $fda        = $this->db->get_where('fda_request', array('fda_request_id' => $dataNa->extra))->row();
                            $fdaC       = $this->db->get_where('fda_category', array('fda_category_id' => $fda->fda_category_id))->row();
                            $whereAbsensi['absensikaryawan_date_masuk']                   = $fda->fda_request_date;
                            $whereAbsensi['nik']                                          = $fda->fda_request_nik;
                            $absensi    = $this->db->get_where('absensi_karyawan', $whereAbsensi)->row();

                            $dataAbsensi['status_absen_masuk']                            = $fdaC->fda_category_name;
                            $dataAbsensi['status_absen_keluar']                           = $fdaC->fda_category_name;
                            $dataAbsensi['keterangan_absen_masuk']                        = $fda->fda_request_desciprtian;
                            $dataAbsensi['keterangan_absen_keluar']                       = $fda->fda_request_desciprtian;
                            $dataAbsensi['foto_status']                                   = $fda->fda_request_image;
                            $dataAbsensi['fda_request_id']                                = $dataNa->extra;
                            if (empty($absensi)) {
                                $dataAbsensi['absensikaryawan_date_masuk']                = $fda->fda_request_date;
                                $dataAbsensi['absensikaryawan_date_keluar']               = $fda->fda_request_date;
                                $dataAbsensi['nik']                                       = $fda->fda_request_nik;
                                $this->db->insert('absensi_karyawan', $dataAbsensi);
                            } else {
                                $this->db->update('absensi_karyawan', $dataAbsensi, $whereAbsensi);
                            }

                            $dataNAA = $this->getTokenAllRow($nik);
                            $this->Mnotification->sendToTopic(@$dataNAA->token, 'Pengajuan FDA Approved', 'Pengajuan FDA Approved, Oleh ' . $userApproval->employee_name . '.', '11', '0');
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'success',
                                    'message' => 'Data berhasil ditambahkan.',
                                )
                            );
                        } else {
                            $arr_result = array(
                                'jne_ittsm' => array(
                                    'status' => 'error',
                                    'message' => 'Silahkan Coba Lagi'
                                )
                            );
                        }
                    }
                }
            }
        }

        print json_encode($arr_result);
    }

    public function getTokenAllRow($nik)
    {
        $this->db->where('nik', $nik);
        return $this->db->get('firebase_token')->row();
    }
    public function findApplication_post()
    {
        $nik  = $this->input->post('nik');

        $arr_result = array();
        if ($nik == "0") {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Silahkan Coba Lagi'
                )
            );
            // } elseif ($modul_id == 0) {
            //     $arr_result = array(
            //     'jne_ittsm' => array(
            //         'status' => 'error',
            //         'message' => 'Silahkan Coba Lagi'
            //     )
            // );
        } else {
            $query = array(
                'nik' => $nik,
            );
            $data_user = $this->Mpengguna->find($query, 'result');
            $data_type = $this->Mpengguna->findUserType($nik, 'result');
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditemukan.',
                    'data'    =>  $data_user,
                    'data_type'    =>  $data_type,
                )
            );
        }

        print json_encode($arr_result);
    }

    public function findUserTypeAll_post()
    {
        $nik  = $this->input->post('nik');

        $arr_result = array();
        if ($nik == "0") {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Silahkan Coba Lagi'
                )
            );
            // } elseif ($modul_id == 0) {
            //     $arr_result = array(
            //     'jne_ittsm' => array(
            //         'status' => 'error',
            //         'message' => 'Silahkan Coba Lagi'
            //     )
            // );
        } else {
            $query = array(
                'nik' => $nik,
            );
            $data_user = $this->Mpengguna->findTypeAll($query, 'result');
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

    public function findAllReset_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'user_reset_id';
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

        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();

        $data_all = $this->Mpengguna->findReset($nik, $dataNa->subunit_code, 'result', $option);
        $data_penajuan = $this->Mpengguna->findSt($nik, $dataNa->subunit_code, 'result', $option);
        $data_berhasil = $this->Mpengguna->find_not_fin($nik, $dataNa->subunit_code, 'result', $option);

        $arr_result = array();
        if (empty($data_all)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Reset Device tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data Reset Device ditemukan.',
                    'data_all'     => $data_all,
                    'data_penajuan'     => $data_penajuan,
                    'data_berhasil'     => $data_berhasil,

                )
            );
        }

        print json_encode($arr_result);
    }
    public function findAllDakar_post()
    {
        $nik  = $this->input->post('nik');
        $order_by            = 'employee_name';
        $ordering            = "ASC";
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

        $this->db->where('nik', $this->input->post('nik'));
        $dataNa = $this->db->get('employee')->row();

        $data_all = $this->Mpengguna->findEmployee($nik, $dataNa->subunit_code, 'result', $option);

        $arr_result = array();
        if (empty($data_all)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Dakar tidak ditemukan!.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Dakar ditemukan.',
                    'data_all'     => $data_all,

                )
            );
        }

        print json_encode($arr_result);
    }
    public function update_password_post()
    {
        $option = array(
            'limit'     =>  1
        );
        $query = array(
            'nik' => $this->input->post('nik'),
            'user_password' => md5($this->input->post('password_old')),
        );

        $user_data = $this->Mpengguna->find($query, 'row', $option);
        if (empty($user_data)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Password lama anda tidak cocok, silahkan coba lagi!.'
                )
            );
        } else {
            $data = array(
                'user_password' => md5($this->input->post('password_new')),
            );
            $where = array('nik' => $this->input->post('nik'),);

            $arr_result = array();

            if ($this->Mpengguna->update($data, $where)) {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => 'Data diupdate.',
                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'warning',
                        'message' => 'Data Tidak diupdate.'
                    )
                );
            }
        }


        print json_encode($arr_result);
    }


    public function setToken_post()
    {
        $nik  = $this->input->post('nik');

        $arr_result = array();
        $data_token = $this->Mpengguna->findToken($nik, 'row');
        $data = array(
            'nik' => $nik,
            'token' => $this->input->post('token'),
        );
        if (empty($data_token)) {
            if ($this->Mpengguna->createToken($data)) {
                $where = array('nik' => $this->input->post('nik'),);
                $dataE = array(
                    'employee_version_app' => $this->input->post('versionCode'),
                );
                $this->Mpengguna->updateEmployee($dataE, $where);
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => 'Data diupdate.',
                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'warning',
                        'message' => 'Data Tidak diupdate.'
                    )
                );
            }
        } else {
            $where = array('nik' => $this->input->post('nik'),);
            if ($this->Mpengguna->updateToken($data, $where)) {
                $dataE = array(
                    'employee_version_app' => $this->input->post('versionCode'),
                );
                $this->Mpengguna->updateEmployee($dataE, $where);
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'success',
                        'message' => 'Data diupdate.',
                    )
                );
            } else {
                $arr_result = array(
                    'jne_ittsm' => array(
                        'status' => 'warning',
                        'message' => 'Data Tidak diupdate.'
                    )
                );
            }
        }


        print json_encode($arr_result);
    }
}