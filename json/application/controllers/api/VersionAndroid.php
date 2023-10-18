<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class VersionAndroid extends REST_Controller
{

    private $arr_result         = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mversionandroid');

    }


    public function check_post()
    {
        @$versionCode = $this->post('versionCode');

        $user_data = $this->Mversionandroid->Log();

        if (empty($user_data)) {
            $this->arr_result = array(
                'jne_ittsm' =>  array(
                  'status'  =>  'warning',
                  'message' =>  'Data Update tidak tersedia !'
                )
              );
        } else {
          if (@$versionCode < @$user_data->version_android_code) {
            if (@$user_data->version_android_is_urgent == 1) {
              $this->arr_result = array(
                            'jne_ittsm' =>  array(
                              'status'       => 'success',
                              'message'      => 'Data update tersedia',
                              'data'         => $user_data,
                            )
                          );
            }else {
              $this->arr_result = array(
                  'jne_ittsm' =>  array(
                    'status'  =>  'warning',
                    'message' =>  'Data Update tidak tersedia !'
                  )
                );
            }

          }else {
            $this->arr_result = array(
                'jne_ittsm' =>  array(
                  'status'  =>  'warning',
                  'message' =>  'Data Update tidak tersedia !'
                )
              );
          }

        }

        $this->response($this->arr_result);
    }


}
