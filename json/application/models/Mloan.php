<?php
class Mloan extends CI_Model
{
    private $table                  = "loan";
    private $table_loan_status      = "loan_status";
    private $table_loan_detail      = "loan_detail";
    private $table_pengguna         = "employee";
    private $table_user             = "user";
    private $table_section                            = "section";

    private $exception_field = array('');
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Bangkok");
        $this->load->model('Msetting');

    }

    public function findLoan($nik, $result_type,$option = null)
    {
        $select = "";

        $this->db->select($this->table.".loan_id");
        $this->db->select($this->table.".loan_amount");
        $this->db->select($this->table.".loan_status");
        $this->db->select($this->table.".nik");
        $this->db->select($this->table_user.".user_type");
        $this->db->where($this->table . '.nik', $nik);
        $this->db->select($this->table_pengguna.".employee_name");
        $this->db->join($this->table_pengguna  , $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->join($this->table_user  , $this->table_pengguna . '.nik = ' . $this->table_user   . '.nik');
        $this->db->order_by($this->table.".loan_id",'DESC');
        $this->db->group_by($this->table.".nik");

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

    public function findAllLoan($branchCode,$result_type,$option = null)
    {
        $select = "";

        $this->db->select($this->table.".loan_id");
        $this->db->select($this->table.".loan_amount");
        $this->db->select($this->table.".loan_status");
        $this->db->select($this->table.".nik");
        $this->db->select($this->table_pengguna.".employee_name");
        $this->db->select($this->table_user.".user_type");
        $this->db->where($this->table_pengguna . '.branch_code', $branchCode);
        $this->db->join($this->table_pengguna  , $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->join($this->table_user  , $this->table_pengguna . '.nik = ' . $this->table_user   . '.nik');
        $this->db->order_by($this->table.".loan_id",'DESC');
        $this->db->group_by($this->table.".nik");

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

    public function findDetail($loan_id, $result_type)
    {
        $select = "";

        $this->db->select($this->table.".loan_id");
        $this->db->select($this->table.".loan_amount");
        $this->db->select($this->table.".loan_status");
        $this->db->select($this->table.".loan_salary");
        $this->db->select($this->table.".loan_phone");
        $this->db->select($this->table.".loan_apply");
        $this->db->select($this->table.".loan_amount");
        $this->db->select($this->table.".loan_amountapprove");
        $this->db->select($this->table.".loan_tenor");
        $this->db->select($this->table.".loan_max");
        $this->db->select($this->table.".loan_purpose");
        $this->db->select($this->table.".loan_description");
        $this->db->select($this->table.".loan_file");
        $this->db->select($this->table.".nik");
        $this->db->select($this->table_pengguna.".employee_name");
        $this->db->select($this->table_pengguna.".employee_level");
        $this->db->select($this->table_pengguna.".employee_status");
        $this->db->select($this->table_pengguna.".branch_code");
        $this->db->select($this->table_section.".section_name");
        $this->db->where($this->table . '.loan_id', $loan_id);
        $this->db->join($this->table_pengguna  , $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->join($this->table_user  , $this->table_pengguna . '.nik = ' . $this->table_user   . '.nik');
        $this->db->join($this->table_section  , $this->table_pengguna . '.section_code = ' . $this->table_section   . '.section_code','left ');
        $this->db->order_by($this->table.".loan_id",'DESC');
        $this->db->group_by($this->table.".nik");



        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }


public function findApprove($nik, $result_type)
{
    $select = "";

    $this->db->select($this->table.".loan_id");
    $this->db->select($this->table.".loan_amountapprove");
    $this->db->where($this->table . '.nik', $nik);
    $this->db->where($this->table . '.loan_status', 'Approved');
    $this->db->group_by($this->table.".loan_id");


    if ($result_type == "row") {
        return $this->db->get($this->table)->row();
    } else {
        return $this->db->get($this->table)->result();
    }
}

public function findKoordinator($subunitCode, $result_type)
{
    $select = "";

    $this->db->select($this->table_pengguna.".nik");
    $this->db->select($this->table_pengguna.".employee_name");
    $this->db->join($this->table_pengguna  , $this->table_user . '.nik = ' . $this->table_pengguna   . '.nik');
    $this->db->where($this->table_pengguna . '.subunit_code', $subunitCode);
    $this->db->where($this->table_user . '.user_type', 'KOORDINATOR');

    if ($result_type == "row") {
        return $this->db->get($this->table_user)->row();
    } else {
        return $this->db->get($this->table_user)->result();
    }
}
public function findJRSPV($unitCode,$sectionCode, $result_type)
{
    $select = "";

    $this->db->select($this->table_pengguna.".nik");
    $this->db->select($this->table_pengguna.".employee_name");
    $this->db->join($this->table_pengguna  , $this->table_user . '.nik = ' . $this->table_pengguna   . '.nik');
    $this->db->where($this->table_user . '.user_type', 'JRSPV');
    $this->db->where($this->table_pengguna . '.section_code', $sectionCode);
    if ($result_type == "row") {
        return $this->db->get($this->table_user)->row();
    } else {
        return $this->db->get($this->table_user)->result();
    }
}
public function findSPV($sectionCode, $result_type)
{
    $select = "";

    $this->db->select($this->table_pengguna.".nik");
    $this->db->select($this->table_pengguna.".employee_name");
    $this->db->join($this->table_pengguna  , $this->table_user . '.nik = ' . $this->table_pengguna   . '.nik');
    $this->db->where($this->table_pengguna . '.section_code', $sectionCode);
    $this->db->where($this->table_user . '.user_type', 'SPV');

    if ($result_type == "row") {
        return $this->db->get($this->table_user)->row();
    } else {
        return $this->db->get($this->table_user)->result();
    }
}

public function findHC($sectionCode,$result_type)
{
    $select = "";

    $this->db->select($this->table_pengguna.".nik");
    $this->db->select($this->table_pengguna.".employee_name");
    $this->db->join($this->table_pengguna  , $this->table_user . '.nik = ' . $this->table_pengguna   . '.nik');
    $this->db->where($this->table_user . '.user_type', 'HUMANCAPITAL');
    $this->db->where($this->table_pengguna . '.section_code', $sectionCode);

    if ($result_type == "row") {
        return $this->db->get($this->table_user)->row();
    } else {
        return $this->db->get($this->table_user)->result();
    }
}

public function findHCbdo($sectionCode,$result_type)
{
    $select = "";
    $arrayName = array('SPV','HUMANCAPITAL');

    $this->db->select($this->table_pengguna.".nik");
    $this->db->select($this->table_pengguna.".employee_name");
    $this->db->join($this->table_pengguna  , $this->table_user . '.nik = ' . $this->table_pengguna   . '.nik');
    $this->db->where_in($this->table_user . '.user_type', $arrayName);
    // $this->db->where($this->table_user . '.user_type', 'SPV');

    if ($result_type == "row") {
        return $this->db->get($this->table_user)->row();
    } else {
        return $this->db->get($this->table_user)->result();
    }
}
    public function findActive($nik, $result_type)
    {
        $select = "";
        $this->db->select($this->table.".loan_id");
        $this->db->select($this->table.".loan_amount");
        $this->db->select($this->table.".loan_tenor");
        $this->db->select($this->table.".loan_purpose");
        $this->db->select($this->table.".loan_description");
        $this->db->select($this->table.".loan_file");
        $this->db->select($this->table.".loan_status");
        $this->db->select($this->table.".loan_amountapprove");
        $this->db->select($this->table.".loan_realization");
        $this->db->select($this->table_pengguna.".subunit_code");
        $this->db->select($this->table_pengguna.".employee_level");
        $this->db->join($this->table_pengguna  , $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->order_by($this->table.".loan_id",'DESC');
        $this->db->group_by($this->table.".loan_id");
        $this->db->limit(3);

        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function findLoanNow($nik, $result_type)
    {
        $select = "";
        $status = array('Paid');

        $this->db->select($this->table.".loan_id");
        $this->db->select($this->table.".loan_amount");
        $this->db->select($this->table.".loan_tenor");
        $this->db->select($this->table.".loan_purpose");
        $this->db->select($this->table.".loan_description");
        $this->db->select($this->table.".loan_file");
        $this->db->select($this->table.".loan_status");
        $this->db->select($this->table.".loan_amountapprove");
        $this->db->select($this->table.".loan_apply");
        $this->db->select($this->table.".approvedate");
        $this->db->select($this->table_pengguna.".subunit_code");
        $this->db->select($this->table_pengguna.".employee_level");
        $this->db->join($this->table_pengguna  , $this->table . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->where($this->table . '.nik', $nik);
        $this->db->where_not_in($this->table . '.loan_status', $status);
        $this->db->order_by($this->table.".loan_id",'DESC');
        $this->db->group_by($this->table.".loan_id");


        if ($result_type == "row") {
            return $this->db->get($this->table)->row();
        } else {
            return $this->db->get($this->table)->result();
        }
    }
    public function create($data)
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    public function createStatus($data)
    {
        if ($this->db->insert($this->table_loan_status, $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($data, $where)
    {
        if ($this->db->update($this->table, $data, $where)) {
            return true;
        } else {
            return false;
        }
    }

    public function findStatus($loan_id,$status,$statusDec)
    {
        $select = "";

        $this->db->select($this->table_loan_status.".loan_status_id");
        $this->db->select($this->table_loan_status.".loan_id");
        $this->db->select($this->table_loan_status.".nik");
        $this->db->select($this->table_loan_status.".date_change");
        $this->db->select($this->table_loan_status.".loan_status_name");
        $this->db->select($this->table_pengguna.".employee_name");
        $this->db->join($this->table_pengguna  , $this->table_loan_status . '.nik = ' . $this->table_pengguna   . '.nik');
        $this->db->where($this->table_loan_status . '.loan_id', $loan_id);
        $this->db->where($this->table_loan_status . '.loan_status_name', $status);
        $this->db->or_where($this->table_loan_status . '.loan_status_name', $statusDec);
        $this->db->order_by($this->table_loan_status.".loan_status_id",'DESC');

        return $this->db->get($this->table_loan_status)->row();
    }

    public function findPembayaran($loan_id)
    {
        $select = "";

        $this->db->where($this->table_loan_detail . '.loan_id', $loan_id);
        $this->db->order_by($this->table_loan_detail.".dtloan_id",'ASC');

        return $this->db->get($this->table_loan_detail)->result();
    }

    public function countPembayaran($loan_id)
    {
        $select = "";

        $this->db->where($this->table_loan_detail . '.loan_id', $loan_id);
        $this->db->where($this->table_loan_detail . '.dtloan_status', 'Paid');
        $this->db->order_by($this->table_loan_detail.".dtloan_id",'ASC');

        return $this->db->get($this->table_loan_detail)->num_rows();
    }

    public function sumPaid($loan_id)
    {
        $select = "";
        $this->db->select_sum($this->table_loan_detail.".dtloan_paypermonth");
        $this->db->where($this->table_loan_detail . '.loan_id', $loan_id);
        $this->db->where($this->table_loan_detail . '.dtloan_status', 'Paid');
        $this->db->order_by($this->table_loan_detail.".dtloan_id",'ASC');

        return $this->db->get($this->table_loan_detail)->row();
    }

}
