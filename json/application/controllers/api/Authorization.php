<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Authorization extends REST_Controller
{
	function __construct()
	  {
	    parent::__construct();

        // $this->load->library('session');

        // ----------------- Tidak perlu Validasi Header dikarenakan fungsi digunakan utk developer ------------------------- //
        session_start();
	  }


    function getParamsHeader_get(){

        $dt             = new DateTime(null, new DateTimeZone("UTC"));
        $timestampnow   = $dt->getTimestamp();
          $this->arr_result = array(
              'params_header' =>  array(
                'Timestamp'   =>  $timestampnow,
                'Auth'        =>  base64_encode('BDO20D05A4676:jneittsmapikey:'.$timestampnow)
              )
            );
        echo json_encode($this->arr_result);
    }

    function decodeHeader_get(){
      echo base64_decode("YWRtaW5AcHJpbHVkZS5jb206cHJpbHVkZWFwaWtleQ==");
    }

      public function encrypt64_get()
    {
        // echo md5("123123123");
        echo base64_encode("BDO20d05a4676:jneittsmapikey:");
        // echo base64_decode('MTIzZmlubmV0');
    }

      public function alert1_get(){
        $arr_result = array(
                          PRODUCT_NAME => array(
                              'status' => '01',
                              'message' => 'Header Not Complete',
                          )
                      );
                    echo json_encode($arr_result);
      }

      public function alert2_get(){
         $arr_result = array(
                            PRODUCT_NAME => array(
                                  'status' => '02',
                                  'message' => 'Waktu tidak sesuai',
                              )
                          );
                    echo json_encode($arr_result);
      }

      public function alert3_get(){
            $arr_result = array(
                            PRODUCT_NAME => array(
                                  'status' => '03',
                                  'message' => 'Authorization Failed',
                              )
                          );
                    echo json_encode($arr_result);
      }

    public function verifiy2_get(){
         // v2

        $header = apache_request_headers();
        foreach ($header as $headers => $value) {
             // echo "$headers: $value\n";
            if($headers == WAKTU){
               $timestamp  = $value;
            }
            if($headers == LOGIN){
                $Auth  = $value;
            }
            if($headers == 'Referer'){
                $urlsubmit  = $value;
            }
        }

        $dt             = new DateTime(null, new DateTimeZone("UTC"));
        $timestampnow   = $dt->getTimestamp();

        //ubah format timestamp ke ymd
        $now         = date(FORMAT_DATE, $timestampnow);
        $params_time = date(FORMAT_DATE, $timestamp);
        $tglsekarang = substr($now ,0,-9);
        $tglparams   = substr($params_time ,0,-9);

        $this->db->select(FIELD);
        $get_user = $this->db->get_where('pengguna');


// json_decode($AUTH_PARTNER)

        $key = [];
        foreach ($get_user->result() as $row)
        {
               $username = $row->username;
               $pass     = base64_decode($row->password);
               $key[]    = base64_encode($username.':jneittsmapikey:');
               // $key[]    = base64_encode(substr($username ,0,-10).':'.$pass.':'.$timestamp);
        }

        $AUTH_PARTNER   = json_encode($key);

        if(empty($timestamp) || empty($Auth)){
            //cek header
             redirect(site_url('api/Authorization/alert1'));
        }else{
            //validasi waktu
            if($tglsekarang == $tglparams){
              //Validasi Auth
                if (in_array($Auth, json_decode($AUTH_PARTNER))){
                    // $arr_result = array(
                    //         PRODUCT_NAME => array(
                    //               'status' => '00',
                    //               'message' => 'success',
                    //               'url' => $urlsubmit
                    //           )
                    //       );
                    // echo json_encode($arr_result);
                // $this->output
                //     ->set_content_type('application/json')
                //     ->set_output(json_encode(array('is_log' => 'bar')));
                     // redirect(site_url('api/Authorization/alert1'));
                }else{
                     redirect(site_url('api/Authorization/alert3'));
                }
            }else{
                 redirect(site_url('api/Authorization/alert2'));
            }
        }
    }

	public function verifiy_get(){
        error_reporting(0);
        $header = apache_request_headers();
        foreach ($header as $headers => $value) {
             // echo "$headers: $value\n";
            if($headers == WAKTU){
               $timestamp  = $value;
            }
            if($headers == LOGIN){
                $Auth  = $value;
            }
            if($headers == 'Referer'){
                $urlsubmit  = $value;
            }
        }

        $dt             = new DateTime(null, new DateTimeZone("UTC"));
        $timestampnow   = $dt->getTimestamp();

        //ubah format timestamp ke ymd
        $now         = date(FORMAT_DATE, $timestampnow);
        $params_time = date(FORMAT_DATE, $timestamp);
        $tglsekarang = substr($now ,0,-9);
        $tglparams   = substr($params_time ,0,-9);

        $this->db->select(FIELD);
        $get_user = $this->db->get_where(TABEL, array(SEBAGAI => 1 ));

        $key = [];
        foreach ($get_user->result() as $row)
        {
               $username = $row->username;
               $pass     = base64_decode($row->password);
               $key[]    = base64_encode(substr($username ,0,-10).':'.$pass.':'.$timestamp);
        }

        $AUTH_PARTNER   = json_encode($key);

        // if(empty($timestamp) || empty($Auth)){
        //     //cek header
        //      $arr_result = array(
        //                   PRODUCT_NAME => array(
        //                       'status' => '01',
        //                       'message' => 'Header Not Complete',
        //                   )
        //               );
        //             echo json_encode($arr_result);
        // }else{
            //validasi waktu
            if($tglsekarang == $tglparams){
              //Validasi Auth
                // if (in_array($Auth, json_decode($AUTH_PARTNER))){
                    // $arr_result = array(
                    //         PRODUCT_NAME => array(
                    //               'status' => '00',
                    //               'message' => 'success',
                    //           )
                    //       );
                    // echo json_encode($arr_result);
            	// return true;
            	// redirect($urlsubmit);
                // $this->session->set_flashdata('gagal', 'Password salah..');
                // $this->session->set_userdata('data', 1);

            // $this->session->sess_destroy();
                // $this->session->set_userdata('data', 1);
//                 $newdata = array(
//                 "id" => "1",
//                 "first_name" => "First",
//                 "insertion" => "",
//                 "last_name" => "Last"
// );

// $this->session->set_userdata($newdata);

// $this->session->has_userdata('some_name');

		// $user = array(
		//         "id" => "1",
		//         "first_name" => "First",
		//         "insertion" => "",
		//         "last_name" => "Last"
		//     );


        $user = array(
                "id" => "1",
                "first_name" => "First",
                "insertion" => "",
                "last_name" => "Last"
            );

		   $this->session->q = $user;

    			// echo 'sip';
             // echo   $data =$this->session->userdata();
                // echo json_encode($data);

// or access array indexes like so.
            // $post_array = $this->session->userdata('newdata');
            // echo $post_array['index'];


        // $sess_data['android_id'] = '123';
        // $this->session->set_userdata($user);
        // $this->response($sess_data,200);

                // $this->session->set_userdata('data', $user);
                // $this->session->set_flashdata('hasil','Insert Data Berhasil');

                // $session_data = $this->session->all_userdata();

                // // echo '<pre>';
                // print_r($session_data);
                // echo "log";
                redirect($urlsubmit);

                // }else{
                //      $arr_result = array(
                //             PRODUCT_NAME => array(
                //                   'status' => '03',
                //                   'message' => 'Authorization Failed',
                //               )
                //           );
                //     echo json_encode($arr_result);
                // }
            }else{
                  $arr_result = array(
                            PRODUCT_NAME => array(
                                  'status' => '02',
                                  'message' => 'Waktu tidak sesuai',
                              )
                          );
                    echo json_encode($arr_result);
            }
        // }

    }

}
?>
