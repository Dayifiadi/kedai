<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Mnotification extends CI_Model
{
    private $exception_field    = array();

    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
    }


    public function sendToTopic($token, $title, $message, $intent_id, $extra)
    {

        $payload = array();
        $res = array();
        $res['data']['title'] = $title;
        $res['data']['is_background'] = true;
        $res['data']['message'] = $message;
        //id_order
        $res['data']['extra'] = $extra;
        $res['data']['intent_id'] = $intent_id;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');


        $fields = array(
            'to' => $token,
            'data' => $res,
        );
        if ($this->sendPushNotification($fields)) {
            return true;
        } else {
            return false;
        }
    }

    public function sendToTopicAll($title, $message, $intent_id, $extra)
    {

        $payload = array();
        $res = array();
        $res['data']['title'] = $title;
        $res['data']['is_background'] = true;
        $res['data']['message'] = $message;
        //id_order
        $res['data']['extra'] = $extra;
        $res['data']['intent_id'] = $intent_id;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');


        $fields = array(
            'to' => '/topics/global_development',
            'data' => $res,
        );
        if ($this->sendPushNotification($fields)) {
            return true;
        } else {
            return false;
        }
    }
    public function findFirebaseToken($user_id)
    {
        return $this->db->get_where("firebase_token", array("user_id" => $user_id))->result();
    }

    private function sendPushNotification($fields)
    {

        $api_key = KEY_FIREBASE;

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $api_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }
}