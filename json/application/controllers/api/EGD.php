<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class EGD extends REST_Controller
{

    private $arr_result         = array();


    private $required_field     = array('');
    private $md5_field          = array('');
    private $primary_key        = 'activity_id';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Megd');

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




      public function find_history_post()
        {
            $nik  = $this->input->post('nik');
            $order_by            = 'egdattendance_id';
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
            $data_aktivitas = $this->Megd->findHistory($nik, 'result',$option);

            $arr_result = array();
            if (empty($data_aktivitas)) {
                    $arr_result = array(
               'jne_ittsm' => array(
                   'status' => 'error',
                   'message' => 'Egd tidak ditemukan!.',
               )
            );
            }else {
              $arr_result = array(
                   'jne_ittsm' => array(
                       'status' => 'success',
                       'message' => 'Data egd ditemukan.',
                       'data_egd'     => $data_aktivitas,
                   )
               );
            }

            print json_encode($arr_result);
        }


        public function create_post()
          {
            $nik  = $this->input->post('nik');
            $cek_egd = $this->db->get_where('egd_attendance', array('egdattendance_token' => $this->input->post('id_pic')))->row()->egdattendance_id;

            $data['egdparticipants_id']      = $cek_egd . '-' . $this->input->post('nik');
            $data['egdparticipants_clockin'] = date('Y-m-d H:i:s');
            $data['egdattendance_id']        = $cek_egd;
            $data['nik']                     = $this->input->post('nik');

            $cek = $this->db->get_where('egd_attendance', array('egdattendance_id' => $cek_egd))->row()->egdattendance_token;

            if($cek == $this->input->post('id_pic')){
                $duplicate = $this->db->get_where('egd_participants', array('egdparticipants_id' => $data['egdparticipants_id']))->num_rows();
                if($duplicate == 0){
                    $this->db->insert('egd_participants', $data);
                    $arr_result = array(
                         'jne_ittsm' => array(
                             'status' => 'success',
                             'message' => 'EGD Attendance Berhasil disimpan.',
                         )
                     );
                } else {
                  $arr_result = array(
                       'jne_ittsm' => array(
                           'status' => 'error',
                           'message' => 'Data anda duplicate.',
                       )
                   );
                }
            } else {
              $arr_result = array(
                   'jne_ittsm' => array(
                       'status' => 'error',
                       'message' => 'Gagal disimpan.',
                   )
               );
            }

              print json_encode($arr_result);
          }






}
