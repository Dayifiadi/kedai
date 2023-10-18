
<?php
class Mversionandroid extends CI_Model
{
  private $table                                    = "version_android";



  function __construct()
  {
    parent::__construct();
    date_default_timezone_set("Asia/Jakarta");
    // $this->load->model('Msetting');
  }


  function Log()
  {
    $select = "";

    $this->db->limit(1);
    $this->db->order_by($this->table.".version_android_id",'DESC');

    return $this->db->get($this->table)->row();

  }


}

?>
