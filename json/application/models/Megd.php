<?php
class Megd extends CI_Model
{
    private $table   = "egd_attendance";
    private $table_egd_participants   = "egd_participants";


    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->load->model('Msetting');

    }



        public function findHistory($nik, $result_type,$option = null)
        {
            $select = "";

            $dateNow=date('Y-m-d');

            $this->db->join($this->table_egd_participants   , $this->table . '.egdattendance_id = ' . $this->table_egd_participants    . '.egdattendance_id');
            $this->db->where($this->table_egd_participants  . '.nik', $nik);
            $this->db->order_by($this->table.".egdattendance_id",'DESC');


            if (array_key_exists('order', $option)) {
                $this->db->order_by($this->table . "." . $option['order']['order_by'], $option['order']['ordering']);
            }
            if ($option != null) {
              if (array_key_exists('limit', $option)) {
                  $this->db->limit($this->table . "." . $option['limit']);
              }

              if (array_key_exists('order_by', $option)) {
                  $this->db->order_by($this->table . "." . $option['order_by']['field'], $option['order_by']['option']);
              }

              if (array_key_exists('page', $option)) {
                  $page = $option['page'] * $option['limit'];
                  $this->db->limit($option['limit'], $page);
              }
          }

            if ($result_type == "row") {
                return $this->db->get($this->table)->row();
            } else {
                return $this->db->get($this->table)->result();
            }
        }



}
