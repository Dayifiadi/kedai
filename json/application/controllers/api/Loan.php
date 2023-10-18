<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Loan extends REST_Controller
{

    private $arr_result         = array();

    private $field_list         = array('loan_id','loan_phone','loan_salary',
                                'loan_amount','loan_tenor','loan_max','loan_paypermonth','loan_purpose','loan_description','loan_apply','loan_status','loan_note','loan_amountapprove','loan_realization','loan_signature','loan_file','loanquota_id','nik','approveby','approvedate');
    private $required_field     = array('');
    private $md5_field          = array('');
    private $primary_key        = 'loan_id';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mloan');
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


    public function findlo_post()
    // public function findlo_post()
      {
        //   $nik                 = $this->input->post('nik');
        //   $subunitCode         = $this->input->post('subunitCode');
        //   $unitCode            = $this->input->post('unitCode');
        //   $sectionCode         = $this->input->post('sectionCode');
        //   $position            = $this->input->post('position');
        //   $branchCode          = $this->input->post('branchCode');
        //   $order_by            = 'loan_id';
        //   $ordering            = "desc";
        //   $limit               = 3;
        //   $page                = 0;

        //   if (isset($_POST['order_by'])) {
        //       $order_by = $this->input->post('order_by');
        //   }

        //   if (isset($_POST['ordering'])) {
        //       $ordering = $this->input->post('ordering');
        //   }

        //   if (isset($_POST['limit'])) {
        //       $limit = $this->input->post('limit');
        //   }

        //   if (isset($_POST['page'])) {
        //       $page = $this->input->post('page');
        //   }


        //   $option = array(
        //       'limit' => $limit,
        //       'page'  => $page,
        //       'order' => array(
        //           'order_by' => $order_by,
        //           'ordering' => $ordering,
        //       ),
        //   );
        //   if ($position == "KOORDINATOR" || $position == "HUMANCAPITAL" || $position == "JRSPV" || $position == "SPV") {
        //     // $data_loan        = $this->Mloan->findLoan($nik,$branchCode, 'result',$option);
        //     $data_loan        = $this->Mloan->findAllLoan($branchCode,'result',$option);
        //   }else {
        //     $data_loan=null;
        //   }
        //   $data_koor        = $this->Mloan->findKoordinator($subunitCode, 'row');
        //   $data_JRSPV       = $this->Mloan->findJRSPV($unitCode,$sectionCode, 'row');
        //   $data_SPV         = $this->Mloan->findSPV($sectionCode, 'row');
        //   if ($branchCode == 'BDO000') {
        //     $data_HC          = $this->Mloan->findHCbdo($sectionCode,'row');
        //   }else {
        //     $data_HC          = $this->Mloan->findHC($sectionCode,'row');
        //   }
        //   $data_approve     = $this->Mloan->findApprove($nik, 'row');
        //   $data_loan_active = $this->Mloan->findActive($nik, "result");
        //   $data_loan_now    = $this->Mloan->findLoanNow($nik, "row");

        //   $arr_result = array();
        //   if (empty($data_loan) && empty($data_approve) && empty($data_loan_active)) {
        //           $arr_result = array(
        //      'jne_ittsm' => array(
        //          'status' => 'error',
        //          'message' => 'Pinjaman tidak ditemukan!.',
        //      )
        //   );

        //   }else {
        //     $data_statusKor   = $this->Mloan->findStatus(@$data_loan_now->loan_id, 'Waiting Approval KOORDINATOR','Declined by KOORDINATOR');
        //     $data_statusjrspv = $this->Mloan->findStatus(@$data_loan_now->loan_id, 'Waiting Approval JRSPV','Declined by JRSPV');
        //     $data_statusspv   = $this->Mloan->findStatus(@$data_loan_now->loan_id, 'Waiting Approval SPV','Declined by SPV');
        //     $data_statushc    = $this->Mloan->findStatus(@$data_loan_now->loan_id, 'Waiting Approval HC','Declined by HC');
        //     $arr_result = array(
        //          'jne_ittsm' => array(
        //              'status' => 'success',
        //              'message' => 'Data pinjaman ditemukan.',
        //              'data_loan'     => $data_loan,
        //              'data_approve'     => $data_approve,
        //              'data_loan_active'     => $data_loan_active,
        //              'data_loan_now'     => $data_loan_now,
        //              'data_koor'     => $data_koor,
        //              'data_JRSPV'     => $data_JRSPV,
        //              'data_SPV'     => $data_SPV,
        //              'data_HC'     => $data_HC,
        //              'data_statusKor'     => $data_statusKor,
        //              'data_statusjrspv'     => $data_statusjrspv,
        //              'data_statusspv'     => $data_statusspv,
        //              'data_statushc'     => $data_statushc,
        //          )
        //      );
        //   }
        $arr_result = array(
            'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Anda Tidak Memiliki Akses Untuk Fitur Pinjaman, Silahkan Hubungi HUMANCAPITAL, Untuk Informasi Lebih Lanjut.!',
            )
         );
          print json_encode($arr_result);
      }

      public function findAllLoan_post()
        {
            $branchCode          = $this->input->post('branchCode');
            $order_by            = 'loan_id';
            $ordering            = "desc";
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
            $data_loan        = $this->Mloan->findAllLoan($branchCode,'result',$option);

            $arr_result = array();
            if (empty($data_loan)) {
                    $arr_result = array(
               'jne_ittsm' => array(
                   'status' => 'error',
                   'message' => 'Pinjaman tidak ditemukan!.',
               )
            );

            }else {
              $arr_result = array(
                   'jne_ittsm' => array(
                       'status' => 'success',
                       'message' => 'Data pinjaman ditemukan.',
                       'data_loan'     => $data_loan,
                   )
               );
            }

            print json_encode($arr_result);
        }


      public function addloan_post()
        {
        //     $nik  = $this->input->post('nik');
        //     $sigName = date('Ymd').$this->input->post('sigName');
        //     $sigFile = $this->input->post('sigFile');
        //     $binaryImage = base64_decode($sigFile);
        //     header('Content-Type: bitmap; charset=utf-8');
        //     //gambar akan disimpan pada folder image
        //     $fileGAMBAR = fopen('../uploads/loan/' . $sigName, 'wb');
        //     // Create File
        //     fwrite($fileGAMBAR,$binaryImage);
        //     fclose($fileGAMBAR);
        //     $binryImage = base64_decode($sigFile);
        //     $data = array(
        //       'nik' =>$this->input->post('nik')
        //   );

        //     $arr_result = array();
        //     $subunitCode=$this->input->post('subunitCode');
        //     $employee_level=$this->input->post('employeeLevel');
        //     $branchCode=$this->input->post('branchCode');
        //     $loanquota = $this->db->get_where('loan_quota', array('branch_code' => $branchCode))->row();
        //     $data['loan_phone']         = $this->input->post('loan_phone');
        //     $data['loan_salary']        = $this->input->post('loan_salary');
        //     $data['loan_amount']        = $this->input->post('loan_amount');
        //     $data['loan_tenor']         = $this->input->post('loan_tenor');
        //     $data['loan_max']           = $this->input->post('loan_max');
        //     $data['loan_paypermonth']   = $this->input->post('loan_paypermonth');
        //     $data['loan_purpose']       = $this->input->post('loan_purpose');
        //     $data['loan_description']   = $this->input->post('loan_description');
        //     $data['loan_apply']         = date('Y-m-d H:i:s');
        //     $data['loanquota_id']       = $loanquota->loanquota_id;
        //     $data['loan_signature']     = $sigName;
        //     if($this->input->post('fileName') != '')
        //     {
        //       $fileName = $this->input->post('fileName');
        //       $filePath = $this->input->post('filePath');
        //       $binaryFile = base64_decode($filePath);
        //       $filePend = fopen('../uploads/loan_file/' . $fileName, 'wb');
        //       // Create File
        //       fwrite($filePend,$binaryFile);
        //       fclose($filePend);
        //       $data['loan_file']     = $fileName;

        //     }
        //     if ($branchCode == "BDO000") {
        //       if($subunitCode == 'BPA01'){
        //           $data['application_status']        = 'Waiting Approval SPV';
        //           $loan_status_name           = 'Waiting Approval SPV';
        //       } elseif ($subunitCode == 'OAS01') {
        //           $data['application_status']        = 'Waiting Approval SPV';
        //           $loan_status_name           = 'Waiting Approval SPV';
        //       } elseif ($subunitCode == 'FIU01') {
        //           $data['application_status']        = 'Waiting Approval JRSPV';
        //           $loan_status_name           = 'Waiting Approval JRSPV';
        //       } elseif ($subunitCode == 'ACU01') {
        //           $data['application_status']        = 'Waiting Approval JRSPV';
        //           $loan_status_name           = 'Waiting Approval JRSPV';
        //       } elseif ($subunitCode == 'LEG01') {
        //           $data['application_status']        = 'Waiting Approval SPV';
        //           $loan_status_name           = 'Waiting Approval SPV';
        //       } elseif ($subunitCode == 'ITU02') {
        //           $data['application_status']        = 'Waiting Approval SPV';
        //           $loan_status_name           = 'Waiting Approval SPV';
        //       } elseif ($subunitCode == 'SCR01') {
        //           $data['application_status']        = 'Waiting Approval JRSPV';
        //           $loan_status_name           = 'Waiting Approval JRSPV';
        //       } elseif ($subunitCode == 'GSU01') {
        //           $data['application_status']        = 'Waiting Approval JRSPV';
        //           $loan_status_name           = 'Waiting Approval JRSPV';
        //       } elseif ($subunitCode == 'CSU01') {
        //           $data['application_status']        = 'Waiting Approval SPV';
        //           $loan_status_name           = 'Waiting Approval SPV';
        //       } elseif ($subunitCode == 'CSU02') {
        //           $data['application_status']        = 'Waiting Approval SPV';
        //           $loan_status_name           = 'Waiting Approval SPV';
        //       } else {
        //           if(($employee_level == 'STAFF' || $employee_level == 'STAFF (PIC)') && $subunitCode != NULL){
        //               $data['loan_status']        = 'Waiting Approval KOORDINATOR';
        //               $loan_status_name           = 'Waiting Approval KOORDINATOR';
        //           } elseif(($employee_level == 'STAFF' || $employee_level == 'STAFF (PIC)') && $subunitCode == NULL){
        //               $data['loan_status']        = 'Waiting Approval JRSPV';
        //               $loan_status_name           = 'Waiting Approval JRSPV';
        //           } elseif($employee_level == 'KOORDINATOR' || $employee_level == 'PJS KOORDINATOR' || $employee_level == 'KOORDINATOR (PIC)' || $employee_level == 'KOORDINATOR (PJS)'){
        //               $data['loan_status']        = 'Waiting Approval JRSPV';
        //               $loan_status_name           = 'Waiting Approval JRSPV';
        //           } elseif($employee_level == 'JUNIOR SUPERVISOR' || $employee_level == 'JUNIOR SUPERVISOR (PIC)' || $employee_level == 'JUNIOR SUPERVISOR (PJS)'){
        //               $data['loan_status']        = 'Waiting Approval SPV';
        //               $loan_status_name           = 'Waiting Approval SPV';
        //           } elseif($employee_level == 'SUPERVISOR' || $employee_level == 'SUPERVISOR (PJS)'){
        //               $data['loan_status']        = 'Waiting Approval HC';
        //               $loan_status_name           = 'Waiting Approval HC';
        //           }
        //       }
        //     }else {
        //       $data['loan_status']        = 'Waiting Approval HC';
        //       $loan_status_name           = 'Waiting Approval HC';
        //       $subunitCode                = 'TSM0010';
        //     }

        //     $data = $this->Mloan->create($data);
        //     if ($data) {
        //       $dataAtasan = $this->Mpengguna->getTokenAll($subunitCode,'result');
        //       foreach ($dataAtasan as $peng) {
        //         $this->Mnotification->sendToTopic($peng->token,'Pengajuan Pinjaman Baru','Pengajuan Pinjaman Baru Silahkan dicek terlebih dahulu.','3',$data);
        //       }
        //       $dataStatus = array(
        //         'loan_id' =>$data ,
        //         'nik' =>$nik ,
        //         'date_change' =>date('Y-m-d') ,
        //         'loan_status_name' =>"Pengajuan Baru" ,
        //     );
        //       $this->Mloan->createStatus($dataStatus);
        //       $arr_result = array(
        //            'jne_ittsm' => array(
        //                'status' => 'success',
        //                'message' => 'Berhasil disimpan.',
        //            )
        //        );
        //     }else {
        //       $arr_result = array(
        //            'jne_ittsm' => array(
        //                'status' => 'error',
        //                'message' => 'Gagal disimpan.',
        //            )
        //        );
        //     }

        //     print json_encode($arr_result);
        }


              public function finddetail_post()
                {
                    $loan_id  = $this->input->post('loan_id');

                    $arr_result = array();


                    $data_user=$this->Mloan->findDetail($loan_id, 'row');
                    if (empty($data_user)) {
                      $arr_result = array(
                           'jne_ittsm' => array(
                               'status' => 'error',
                               'message' => 'Gagal mengambil data.',
                           )
                       );
                    }else {
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

                public function findhistory_post()
                  {
                      $loan_id  = $this->input->post('loan_id');

                      $arr_result = array();


                      $data_user=$this->Mloan->findPembayaran($loan_id);
                      $dataDetail=$this->Mloan->findDetail($loan_id,'row');
                      $count=$this->Mloan->countPembayaran($loan_id);
                      $sum=$this->Mloan->sumPaid($loan_id);
                      if (empty($data_user)) {
                        $arr_result = array(
                             'jne_ittsm' => array(
                                 'status' => 'error',
                                 'message' => 'Tidak ada data.',
                             )
                         );
                      }else {
                        $arr_result = array(
                      'jne_ittsm' => array(
                          'status' => 'success',
                          'message' => 'Data berhasil ditemukan.',
                          'count'    =>  $count,
                          'sum'    =>  $sum,
                          'data_detail'    =>  $dataDetail,
                          'data'    =>  $data_user,
                      )
                  );
                      }


                      print json_encode($arr_result);
                  }
                  public function updateloan_post()
                    {
                    //     $loan_id  = $this->input->post('loan_id');
                    //     $nik  = $this->input->post('nik');
                    //     $nikPengajuan  = $this->input->post('nikPengajuan');
                    //     $loan_status_name  = $this->input->post('status');
                    //     $loan_note  = $this->input->post('loan_note');
                    //     $statusDetail  = $this->input->post('statusDetail');

                    //     $arr_result = array();

                    //     $dataStatus = array(
                    //       'loan_id' =>$loan_id ,
                    //       'nik' =>$nik ,
                    //       'date_change' =>date('Y-m-d') ,
                    //       'loan_status_name' =>$statusDetail ,
                    //   );
                    //     $ins=$this->Mloan->createStatus($dataStatus);
                    //     if ($ins) {
                    //       if ($loan_status_name == "Approved") {
                    //         $loan_amountapprove  = $this->input->post('loan_amountapprove');
                    //         $loan_realization  = $this->input->post('loan_realization');
                    //         $dataUpdate = array(
                    //           'loan_status' =>$loan_status_name ,
                    //           'loan_note' =>$loan_note ,
                    //           'loan_amountapprove' =>$loan_amountapprove ,
                    //           'loan_realization' =>$loan_realization ,
                    //           'approveby' =>$nik ,
                    //           'approvedate' =>date('Y-m-d') ,
                    //       );
                    //        $where = array('loan_id' =>$loan_id , );
                    //        $this->Mloan->update($dataUpdate,$where);
                    //       }else {
                    //         $dataUpdate = array(
                    //           'loan_status' =>$loan_status_name ,
                    //           'loan_note' =>$loan_note ,
                    //       );
                    //        $where = array('loan_id' =>$loan_id , );
                    //        $this->Mloan->update($dataUpdate,$where);
                    //       }

                    //        $data_token_approval=$this->db->get_where("firebase_token", array("nik" => $nik))->row();
                    //        $data_token_employe=$this->db->get_where("firebase_token", array("nik" => $nikPengajuan))->row();
                    //        $this->Mnotification->sendToTopic($data_token_approval->token,'Update Pinjaman',$loan_status_name,'3',$loan_id);
                    //        $this->Mnotification->sendToTopic($data_token_employe->token,'Update Pinjaman',$loan_status_name,'3',$loan_id);
                    //        $arr_result = array(
                    //          'jne_ittsm' => array(
                    //              'status' => 'success',
                    //              'message' => 'Data berhasil disimpan.',
                    //          )
                    //      );
                    //     }else {
                    //       $arr_result = array(
                    //            'jne_ittsm' => array(
                    //                'status' => 'error',
                    //                'message' => 'Gagal menyimpan data.',
                    //            )
                    //        );
                    //     }


                    //     print json_encode($arr_result);
                    }

}
