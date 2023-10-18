<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Log extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

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

    public function create_post()
    {
        $nik  = $this->input->post('nik');
        $where  = $this->input->post('where');
        $error  = $this->input->post('error');
        $data['log_android_nik']      = $nik;
        $data['log_android_error']      = $error;
        $data['log_android_where']      = $where;
        $data['log_android_created_date'] = date('Y-m-d H:i:s');


        $data = $this->db->insert('log_android', $data);

        if (empty($data)) {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal menambahkan data.',
                )
            );
        } else {
            $arr_result = array(
                'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Data berhasil ditambahkan.',
                )
            );
        }


        print json_encode($arr_result);
    }
}
