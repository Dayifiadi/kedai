<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class PenggunaAwal extends REST_Controller
{

  private $arr_result         = array();

  private $field_list         = array(
    'user_id', 'user_password',
    'user_status', 'user_application', 'user_type',
    'user_deviceid', 'nik'
  );
  private $required_field     = array('nik');
  private $md5_field          = array('user_password');
  private $primary_key        = 'user_id';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Mpengguna');
  }


  public function login_post()
  {
    $nik = $this->post('nik');
    $password = md5($this->post('password'));
    $device_id = md5($this->post('device_id'));

    $option = array(
      'limit'     =>  1
    );

    $user_data = $this->Mpengguna->Log($nik, $password, 'row', $option);

    if (empty($user_data)) {
      $this->arr_result = array(
        'jne_ittsm' =>  array(
          'status'  =>  'logout',
          'message' =>  'Nik dan Password anda tidak cocok, silahkan coba lagi !'
        )
      );
    } else {
      //          if (@$user_data->employee_version_app=="" || @$user_data->employee_version_app=="null" || @$user_data->employee_version_app==0) {
      //              $this->arr_result = array(
      //     'jne_ittsm' => array(
      //         'status' => 'logout',
      //         'message' => 'Aplikasi Android anda belum UPDATE silahkan diupdate terlebih dahulu!.'
      //     )
      // );
      //          }else {
      if ($user_data->branch_code == "TSM000") {
        // if (@$user_data->employee_version_app == "" || @$user_data->employee_version_app == "null" || @$user_data->employee_version_app != 3) {
        //   $this->arr_result = array(
        //     'jne_ittsm' => array(
        //       'status' => 'logout',
        //       'message' => 'Aplikasi Android anda belum UPDATE silahkan diupdate terlebih dahulu!.'
        //     )
        //   );
        // } else {
        // pengecekan devideid ditiadakan
        if ($user_data->user_deviceid == "" || $user_data->user_deviceid == $device_id || $user_data->user_deviceid == "null") {
          $nik   = $user_data->nik;

          $dataNa = array(
            'user_deviceid' => $device_id,
          );
          $whereNa = array(
            'nik' => $nik
          );
          $this->Mpengguna->update($dataNa, $whereNa);
          $this->arr_result = array(
            'jne_ittsm' =>  array(
              'status'       => 'success',
              'message'      => 'Data pengguan tersedia',
              'data'         => $user_data,
            )
          );
        } else {
          $this->arr_result = array(
            'jne_ittsm' => array(
              'status' => 'logout',
              'message' => 'Login tidak dapat di lakukan karena akun ini masih di gunakan pada device yang lain. Silahkan hubungi HC untuk prosess reset device anda.'
            )
          );
        }
        // }
      } else {
        // pengecekan devideid ditiadakan
        if ($user_data->user_deviceid == "" || $user_data->user_deviceid == $device_id || $user_data->user_deviceid == "null") {
          $nik   = $user_data->nik;

          $dataNa = array(
            'user_deviceid' => $device_id,
          );
          $whereNa = array(
            'nik' => $nik
          );
          $this->Mpengguna->update($dataNa, $whereNa);
          $this->arr_result = array(
            'jne_ittsm' =>  array(
              'status'       => 'success',
              'message'      => 'Data pengguan tersedia',
              'data'         => $user_data,
            )
          );
        } else {
          $this->arr_result = array(
            'jne_ittsm' => array(
              'status' => 'logout',
              'message' => 'Login tidak dapat di lakukan karena akun ini masih di gunakan pada device yang lain. Silahkan hubungi HC untuk prosess reset device anda.'
            )
          );
        }
      }

      // }

    }
    print json_encode($this->arr_result);
  }
}