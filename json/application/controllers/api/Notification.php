<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Notification extends REST_Controller
{
  private $table                      = "notification";
  private $primary_key                = "notification_id";
  private $field_list                 = array(
    'notification_id', 'user_id', 'notification_status_id',
    'application_id', 'intent_id', 'crate_time', 'title', 'message',
    'extra'
  );
  private $exception_field            = array();

  function __construct()
  {
    parent::__construct();
    $this->load->model('Mnotification');
  }



  //Send Notif with firebase
  function send_notification_post()
  {
    $title      = $this->input->post('title');
    $message    = $this->input->post('message');
    $application_id = $this->input->post('application_id');
    $intent_id    = $this->input->post('intent_id');
    $extra      = $this->input->post('extra');
    $is_order   = $this->input->post('is_order');
    // $data_token         = $this->findFirebaseToken();
    //
    // foreach ($data_token as $key) {
    // }
    $last_id = $this->Mnotification->sendToTopicAll($title, $message, $intent_id, $extra, $is_order);

    if ($last_id) {
      $this->arr_result = array(
        'jne_tsm' =>  array(
          'status' => 'success',
          'message' => 'berhasil mengirim notif',
        )
      );
    } else {
      $this->arr_result = array(
        'jne_tsm' =>  array(
          'status' => 'warning',
          'message' => 'Gagal mengirim notif'
        )
      );
    }

    $this->response($this->arr_result);
  }



  //Send Notif with firebase
  function send_notification_topic_post()
  {
    $title      = $this->input->post('title');
    $message    = $this->input->post('message');
    $application_id = $this->input->post('application_id');
    $intent_id    = $this->input->post('intent_id');
    $extra      = $this->input->post('extra');
    $is_order   = $this->input->post('is_order');

    // $data_token         = $this->findFirebaseToken();
    //
    // foreach ($data_token as $key) {
    // }
    $last_id = $this->Mnotification->sendToTopicAll($title, $message, $intent_id, $extra, $is_order);

    if ($last_id) {
      $this->arr_result = array(
        'jne_tsm' =>  array(
          'status' => 'success',
          'message' => 'berhasil mengirim notif',
        )
      );
    } else {
      $this->arr_result = array(
        'jne_tsm' =>  array(
          'status' => 'warning',
          'message' => 'Gagal mengirim notif'
        )
      );
    }

    $this->response($this->arr_result);
  }



  function findFirebaseToken()
  {
    return $this->db->get_where("firebase_token")->result();
  }
}