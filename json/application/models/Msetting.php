<?php

if (!defined('BASEPATH')) {

    exit('No direct script access allowed');

}

class Msetting extends CI_Model

{

    private $table            = "setting";



    public function __construct()

    {

        parent::__construct();

    }



    //untuk mendapatkan setting berdasarakan pada namanya

    public function findByName($setting_name)

    {

        $where=array(

            'setting_name'=>$setting_name

        );



        $row=$this->db->get_where('setting', $where)->row();

        return $row;

    }

}
