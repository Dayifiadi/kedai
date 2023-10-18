<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Absensi extends REST_Controller
{

  private $arr_result         = array();

  private $field_list         = array(
    'absensi_karyawan_id', 'nik', 'area_kerja_id',
    'latitude_absensi', 'longitude_absensi', 'jarak_absensi', 'tanggal_absensi', 'day', 'jam_masuk', 'jam_keluar', 'foto_absensi', 'keterangan'
  );
  private $required_field     = array('');
  private $md5_field          = array('');
  private $primary_key        = 'absensi_karyawan_id';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Mabsensi');
    $this->load->model('Mpengguna');

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

  public function sync_post()
  {
    $data_absensi_masuk = $this->Mabsensi->findSYNC($this->input->post('idMasuk'), 'result');
    // $data_absensi_keluar = $this->Mabsensi->findSYNC($this->input->post('idKeluar'), 'row');
    foreach ($data_absensi_masuk as $absensi) {
      echo  $absensi->nik;
      $absensikaryawan_id = $absensi->absensikaryawan_id;
      $where = array(
        'absensikaryawan_id' => $absensikaryawan_id,
        'nik' => $absensi->nik,
      );
      if ($absensi->absensikaryawan_areakerja_keluar == NULL) {
        $data = array(
          'absensikaryawan_time_keluar' => $absensi->absensikaryawan_time_masuk,
          'absensikaryawan_areakerja_keluar' => $absensi->absensikaryawan_areakerja_masuk,
          'absensikaryawan_date_keluar' => $absensi->absensikaryawan_date_masuk,
          'absensikaryawan_ket_keluar' => $absensi->absensikaryawan_ket_masuk,
          'absensikaryawan_photo_keluar' => $absensi->absensikaryawan_photo_masuk,
          'absensikaryawan_jarak_keluar' => $absensi->absensikaryawan_jarak_masuk,
          'absensikaryawan_latitude_keluar' => $absensi->absensikaryawan_latitude_masuk,
          'absensikaryawan_longitude_keluar' => $absensi->absensikaryawan_longitude_masuk,
        );
      } else {
        $data = array(
          'absensikaryawan_time_keluar' => $absensi->absensikaryawan_time_keluar,
          'absensikaryawan_areakerja_keluar' => $absensi->absensikaryawan_areakerja_keluar,
          'absensikaryawan_date_keluar' => $absensi->absensikaryawan_date_keluar,
          'absensikaryawan_ket_keluar' => $absensi->absensikaryawan_ket_keluar,
          'absensikaryawan_photo_keluar' => $absensi->absensikaryawan_photo_keluar,
          'absensikaryawan_jarak_keluar' => $absensi->absensikaryawan_jarak_keluar,
          'absensikaryawan_latitude_keluar' => $absensi->absensikaryawan_latitude_keluar,
          'absensikaryawan_longitude_keluar' => $absensi->absensikaryawan_longitude_keluar,
        );
      }
      $data_absensi = $this->Mabsensi->update($data, $where);
      $this->db->delete('absensi_karyawan', array('absensikaryawan_id' => $absensi->absensikaryawan_id));
    }

    if ($data_absensi) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'success',
          'message' => 'Berhasil disimpan.',
        )
      );
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Gagal disimpan.',
        )
      );
    }
    print json_encode($arr_result);
  }

  public function find_now_post()
  {
    $nik  = $this->input->post('nik');

    $data_absensi = $this->Mabsensi->findNewNewNaDUlu($nik, 'row');
    $dateNow = date('Y-m-d');
    $date2 = date('Y-m-d', strtotime(' -1 day'));
    $query = array(
      'nik' => $nik,
    );
    $data_user = $this->Mpengguna->find($query, 'row');
    $arr_result = array();
    $isError = FALSE;

    if (empty($data_absensi)) {
      $isError = TRUE;
    } else {
      if ($data_absensi->tanggal_absensi == null) {

        if ($data_absensi->tanggal_absensi_keluar == null) {

          if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == null) {
            $isError = FALSE;
          } else {
            if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
              $isError = FALSE;
            } else {
              if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                $isError = TRUE;
              } else {
                if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                  $isError = TRUE;
                } else {
                  if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == null) {
                      $isError = FALSE;
                    } else {
                      if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == null) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                            $isError = FALSE;
                          } else {
                            $isError = TRUE;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        } else {
          if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
            $isError = FALSE;
          } else {
            if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
              $isError = TRUE;
            } else {
              if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                $isError = TRUE;
              } else {
                if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                  $isError = TRUE;
                } else {
                  if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == null) {
                    $isError = FALSE;
                  } else {
                    if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                      $isError = FALSE;
                    } else {
                      if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == null) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                          $isError = FALSE;
                        } else {
                          $isError = TRUE;
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      } else {
        if ($data_absensi->tanggal_absensi_keluar == null) {
          if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == null) {

            $isError = FALSE;
          } else {
            if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
              $isError = FALSE;
            } else {
              if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                $isError = TRUE;
              } else {
                if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                  $isError = TRUE;
                } else {
                  if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == null) {
                      $isError = FALSE;
                    } else {
                      if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == null) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                            $isError = FALSE;
                          } else {
                            $isError = TRUE;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        } else {
          if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
            $isError = FALSE;
          } else {
            if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
              $isError = TRUE;
            } else {
              if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                $isError = TRUE;
              } else {
                if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                  $isError = TRUE;
                } else {
                  if ($data_absensi->tanggal_absensi == date('Y-m-d') && $data_absensi->tanggal_absensi_keluar == null) {
                    $isError = FALSE;
                  } else {
                    if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d')) {
                      $isError = FALSE;
                    } else {
                      if ($data_absensi->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensi->tanggal_absensi_keluar == null) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensi->tanggal_absensi == null && $data_absensi->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                          $isError = FALSE;
                        } else {
                          $isError = TRUE;
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }



    if ($isError == FALSE) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'success',
          'message' => 'Data absensi ditemukan.',
          'data_absensi'     => $data_absensi,
          'tanggal_now'     => $dateNow,
          'tanggal_yes'     => $date2,
        )
      );
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Absensi tidak ditemukan!.',
          'data_user'     => $data_user,
          'tanggal_now'     => $dateNow,
          'tanggal_yes'     => $date2,
        )
      );
    }


    print json_encode($arr_result);
  }



  public function find_shifting_post()
  {
    $nik  = $this->input->post('nik');

    $data_shifting_now                       = $this->Mabsensi->findShiftingMasuk($nik, 'row');
    $data_shifting_yes                       = $this->Mabsensi->findShiftingYes($nik, 'row');
    $dateNow = date('Y-m-d');
    $date2 = date('Y-m-d', strtotime(' -1 day'));
    $date3 = date('Y-m-d', strtotime(' +1 day'));
    $time = date('H:i');
    $query = array(
      'nik' => $nik,
    );
    $data_user = $this->Mpengguna->find($query, 'row');
    $arr_result = array();
    $STATUS = '';
    $isError = FALSE;
    $data_absensisend = '';
    $data_shiftingsend = '';

    if (empty($data_shifting_yes)) {
      if (empty($data_shifting_now)) {
        $isError = TRUE;
      } else {
        // echo "1 " . @$data_shifting_now->absensi_jadwal_id;
        if ($data_shifting_now->absensi_jadwal_id == "") {
          $isError = TRUE;
        } else {
          $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
          $data_shiftingsend                      = $data_shifting_now;
          if (empty($data_absensisend)) {
            $isError = TRUE;
          }
        }
        $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
        $data_shiftingsend                      = $data_shifting_now;
        if (empty($data_absensisend)) {
          $isError = TRUE;
        }
      }
    } else {
      if (!empty($data_shifting_now)) {
        if (@$data_shifting_now->absensi_jadwal_id == "") {
          $isError = TRUE;
        } else {
          // echo "2 " . @$data_shifting_now->absensi_jadwal_id;
          $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
          $data_shiftingsend                      = $data_shifting_now;
          if (empty($data_absensisend)) {
            $isError = TRUE;
          }
          if ($data_shifting_now->absensi_jadwal_status == 'OFF') {
            $timestamp = strtotime($data_shifting_yes->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
            // $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 140 * 60 * 2;
            $timeMasuk = date('H:i', $timestamp);
            if ($time <= $timeMasuk || $data_shifting_now->absensi_jadwal_status == 'OFF') {
              if (@$data_shifting_now->absensi_jadwal_id == "") {
                $isError = TRUE;
              } else {
                // echo "4 " . @$data_shifting_yes->absensi_jadwal_id;
                $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_yes->absensi_jadwal_id);
                $data_shiftingsend                      = $data_shifting_yes;
                if (empty($data_absensisend)) {
                  $isError = TRUE;
                }
              }
            }
          } else {
            $timestamp = strtotime($data_shifting_now->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
            // $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 140 * 60 * 2;
            $timeMasuk = date('H:i', $timestamp);
            if ($time <= $timeMasuk || $data_shifting_now->absensi_jadwal_status == 'OFF') {
              if (@$data_shifting_now->absensi_jadwal_id == "") {
                $isError = TRUE;
              } else {
                // echo "4 " . @$data_shifting_yes->absensi_jadwal_id;
                $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_yes->absensi_jadwal_id);
                $data_shiftingsend                      = $data_shifting_yes;
                if (empty($data_absensisend)) {
                  $isError = TRUE;
                }
              }
            }
          }
        }
      } else {
        if (@$data_shifting_yes->absensi_jadwal_id == "") {
          $isError = TRUE;
        } else {
          // echo "3 " . @$data_shifting_yes->absensi_jadwal_id;
          $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_yes->absensi_jadwal_id);
          $data_shiftingsend                      = $data_shifting_yes;
          if (empty($data_absensisend)) {
            $isError = TRUE;
          }
        }
      }
    }
    if ($isError == FALSE) {

      $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
      // $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 140 * 60 * 2;
      $timeMasuk = date('H:i', $timestamp);
      // if ($data_absensisend->tanggal_absensi == $dateNow && $data_absensisend->tanggal_absensi_keluar == $dateNow) {
      //   echo "masuk1";
      // } elseif ($data_absensisend->tanggal_absensi == $dateNow && $data_absensisend->tanggal_absensi_keluar == $date3) {
      //   echo "masuk2";
      // } elseif ($data_absensisend->tanggal_absensi == $date2 && $data_absensisend->tanggal_absensi_keluar == $dateNow) {
      //   echo "masuk3";
      // } elseif ($data_absensisend->tanggal_absensi == $date2 && $data_absensisend->tanggal_absensi_keluar == $date2) {
      //   echo "masuk4";
      //   if ($time <= $timeMasuk) {
      //     echo "masuk1";
      //     $isError = FALSE;
      //   } else {
      //     $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
      //     $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
      //     $data_shiftingsend                      = $data_shifting_now;
      //     echo "update1";
      //     $isError = FALSE;
      //     if ($data_absensisend->absensikaryawan_areakerja_masuk == '') {
      //       $data['status_absen_masuk']                  = 'Tidak Absen In';
      //     }
      //     $data['status_absen_keluar']                  = 'Tidak Absen Out';
      //     $where['absensi_jadwal_id']                   = @$data_shifting_yes->absensi_jadwal_id;
      //     $this->db->update('absensi_karyawan', $data, $where);
      //   }
      // }

      if ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('00:00') && $data_shiftingsend->absensi_jadwal_jam_keluar <= date('12:00')) {
        if ($time <= $timeMasuk) {
          // echo "masuk1";
          $isError = FALSE;
        } else {
          $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
          if ($data_shifting_yes->absensi_jadwal_status != 'OFF' || $data_shifting_yes->absensi_jadwal_status != 'OFF') {
            //   if ($data_absensisend->status_absen_masuk == NULL && $data_absensisend->absensikaryawan_areakerja_masuk == NULL) {
            //     if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
            //       $data['status_absen_masuk']                  = 'Tidak Absen IN';
            //       $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
            //       $this->db->update('absensi_karyawan', $data, $where);
            //     }
            //   }
            if ($data_absensisend->status_absen_masuk == NULL && $data_absensisend->absensikaryawan_areakerja_masuk == NULL) {
              if ($data_shiftingsend->absensi_jadwal_jam_masuk >= date('H:i') && $data_shiftingsend->absensi_jadwal_tanggal != date('Y-m-d')) {
                if ($data_shiftingsend->absensi_jadwal_jam_masuk == date(date('Y-m-d'), strtotime(' -1 day'))) {
                  if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
                    $data['status_absen_keluar']                  = 'Tidak Absen IN';
                    $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                    $this->db->update('absensi_karyawan', $data, $where);
                  }
                }
              }
            }
            if ($data_absensisend->status_absen_keluar == NULL && $data_absensisend->absensikaryawan_areakerja_keluar == NULL) {
              if ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('H:i') && $data_shiftingsend->absensi_jadwal_tanggal != date('Y-m-d')) {
                if ($data_shiftingsend->absensi_jadwal_jam_keluar == date(date('Y-m-d'), strtotime(' -1 day'))) {
                  if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
                    $data['status_absen_keluar']                  = 'Tidak Absen OUT';
                    $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                    $this->db->update('absensi_karyawan', $data, $where);
                  }
                }
              }
            }

            if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
              if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
                $data['status_absen_keluar']                  = 'Alpa';
                $data['status_absen_masuk']                   = 'Alpa';
                $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                $this->db->update('absensi_karyawan', $data, $where);
              }
            }
          }

          if (!empty($data_shifting_now)) {
            $data_absensisend_now               = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
            $data_shiftingsend                  = $data_shifting_now;
            // echo "update1";

            $data_absensisend                      = $data_absensisend_now;
            $isError = FALSE;
          } else {
            $isError = TRUE;
          }
        }
      } elseif ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('12:00')) {
        if ($time <= $timeMasuk) {
          // echo "masuk2";
          $isError = FALSE;
        } else {
          $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
          if ($data_shifting_yes->absensi_jadwal_status != 'OFF' || $data_shifting_yes->absensi_jadwal_status != 'OFF') {
            // if ($data_absensisend->status_absen_masuk == NULL && $data_absensisend->absensikaryawan_areakerja_masuk == NULL) {
            //   if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
            //     $data['status_absen_masuk']                  = 'Tidak Absen IN';
            //     $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
            //     $this->db->update('absensi_karyawan', $data, $where);
            //   }
            // }
            if ($data_absensisend->status_absen_masuk == NULL && $data_absensisend->absensikaryawan_areakerja_masuk == NULL) {
              if ($data_shiftingsend->absensi_jadwal_jam_masuk >= date('H:i') && $data_shiftingsend->absensi_jadwal_tanggal != date('Y-m-d')) {
                if ($data_shiftingsend->absensi_jadwal_jam_masuk == date(date('Y-m-d'), strtotime(' -1 day'))) {
                  if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
                    $data['status_absen_keluar']                  = 'Tidak Absen IN';
                    $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                    $this->db->update('absensi_karyawan', $data, $where);
                  }
                }
              }
            }
            if ($data_absensisend->status_absen_keluar == NULL && $data_absensisend->absensikaryawan_areakerja_keluar == NULL) {
              if ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('H:i') && $data_shiftingsend->absensi_jadwal_tanggal != date('Y-m-d')) {
                if ($data_shiftingsend->absensi_jadwal_jam_keluar == date(date('Y-m-d'), strtotime(' -1 day'))) {
                  if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
                    $data['status_absen_keluar']                  = 'Tidak Absen OUT';
                    $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                    $this->db->update('absensi_karyawan', $data, $where);
                  }
                }
              }
            }

            if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
              if (@$data_shifting_yes->absensi_jadwal_id != NULL) {
                $data['status_absen_keluar']                  = 'Alpa';
                $data['status_absen_masuk']                   = 'Alpa';
                $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                $this->db->update('absensi_karyawan', $data, $where);
              }
            }
          }

          if (!empty($data_shifting_now)) {
            $data_absensisend_now               = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
            $data_shiftingsend                  = $data_shifting_now;
            // echo "update2";

            $data_absensisend                   = $data_absensisend_now;
            $isError = FALSE;
          } else {
            $isError = TRUE;
          }
        }
      } else {
        if ($data_absensisend->status_absen_masuk == 'OFF' || $data_absensisend->status_absen_keluar == "OFF") {
          $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
          if (!empty($data_shifting_now)) {
            $data_absensisend_now               = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
            $data_shiftingsend                  = $data_shifting_now;
            // echo "update4";

            $data_absensisend                   = $data_absensisend_now;
            $isError = FALSE;
          } else {
            $isError = TRUE;
          }
        } else {
          $isError = TRUE;
        }
      }
    }

    // die();
    $noShift = "No";

    if ($isError == TRUE) {
      // $data_absensi = $this->Mabsensi->findNewNewNa($nik, 'row');
      // if (empty($data_absensi)) {
      //   $data_absensi = $this->Mabsensi->findNewNewNaYesNa($nik, 'row');
      //   if (empty($data_absensi)) {
      //     $data_absensi = $this->Mabsensi->findNewNewNaKeluarYesNa($nik, 'row');
      //     if (empty($data_absensi)) {
      //       $data_absensi = $this->Mabsensi->findNewNewNaKeluarYesNaBaru($nik, 'row');
      //     }
      //   }
      // }
      $data_absensi = $this->Mabsensi->findNewNewNaDUlu($nik, 'row');

      $data_absensisend                       = $data_absensi;
      $noShift = "Yes";

      if (empty($data_absensisend)) {
        $isError = TRUE;
      } else {
        if ($data_absensisend->tanggal_absensi == null) {

          if ($data_absensisend->tanggal_absensi_keluar == null) {

            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == null) {
              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                $isError = FALSE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                      $isError = TRUE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                            $isError = FALSE;
                          } else {
                            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                              $isError = FALSE;
                            } else {
                              $isError = TRUE;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          } else {

            if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                $isError = TRUE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {
                      $isError = FALSE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                            $isError = FALSE;
                          } else {
                            $isError = TRUE;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        } else {

          if ($data_absensisend->tanggal_absensi_keluar == null) {

            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == null) {

              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                $isError = FALSE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                      $isError = TRUE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {

                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                            $isError = FALSE;
                          } else {
                            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                              $isError = FALSE;
                            } else {
                              $isError = TRUE;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          } else {

            if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {

              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                $isError = TRUE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {

                      $isError = FALSE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                            $isError = FALSE;
                          } else {
                            if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' +1 day')) && $data_absensisend->fda_request_id != null) {
                              if ($data_absensisend->fda_request_id != null) {
                                $data_absensi = $this->Mabsensi->findNewNewNaDUluNow($nik, 'row');

                                $data_absensisend                       = $data_absensi;
                                $noShift = "Yes";
                                $isError = FALSE;
                              } else {
                                $isError = FALSE;
                              }
                            } else {
                              $data_absensi = $this->Mabsensi->findNewNewNaDUluNow($nik, 'row');

                              $data_absensisend                       = $data_absensi;
                              $noShift = "Yes";
                              $isError = FALSE;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    // $data_shift_all  = $this->Mabsensi->findShiftingYesAll('result');
    // if (!empty($data_shift_all)) {
    //   foreach ($data_shift_all as $shiftAll) {
    //     $timestamp = strtotime($shiftAll->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
    //     $timeMasuk = date('H:i', $timestamp);
    //     $dataAbsenAll = $this->Mabsensi->findByShifting(@$shiftAll->absensi_jadwal_id);
    //     if (!empty($dataAbsenAll)) {
    //       if ($shiftAll->absensi_jadwal_jam_keluar >= date('00:00') && $shiftAll->absensi_jadwal_jam_keluar <= date('12:00')) {
    //         if ($time <= $timeMasuk) {
    //         } else {
    //           if ($dataAbsenAll->status_absen_masuk == '') {
    //             $data['status_absen_masuk']                  = 'Tidak Absen IN';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //           if ($dataAbsenAll->status_absen_keluar == '') {
    //             $data['status_absen_keluar']                  = 'Tidak Absen OUT';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //           if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
    //             $data['status_absen_keluar']                  = 'Alpa';
    //             $data['status_absen_masuk']                   = 'Alpa';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //         }
    //       } elseif ($shiftAll->absensi_jadwal_jam_keluar >= date('12:00')) {
    //         if ($time <= $timeMasuk) {
    //           // echo "masuk2";
    //         } else {
    //           // echo "update2";
    //           if ($dataAbsenAll->status_absen_masuk == '') {
    //             $data['status_absen_masuk']                  = 'Tidak Absen IN';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //           if ($dataAbsenAll->status_absen_keluar == '') {
    //             $data['status_absen_keluar']                  = 'Tidak Absen OUT';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }

    //           if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
    //             $data['status_absen_keluar']                  = 'Alpa';
    //             $data['status_absen_masuk']                   = 'Alpa';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //         }
    //       } else {
    //         // echo "gagal";
    //       }
    //     }
    //   }
    // }




    if ($isError == FALSE) {
      if (empty($data_shiftingsend)) {
        if (empty($data_absensisend)) {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'error',
              'message' => 'Absensi tidak ditemukan!.',
              'masuk' => $noShift,
              'data_user'     => $data_user,
              'tanggal_now'     => $dateNow,
              'tanggal_yes'     => $date2,
            )
          );
        } else {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'masuk' => $noShift,
              'data_absensi'     => $data_absensisend,
              'tanggal_now'     => $dateNow,
              'tanggal_yes'     => $date2,
            )
          );
        }
      } else {
        if ($noShift == 'Yes') {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'masuk' => $noShift,
              'data_absensi'     => $data_absensisend,
              'tanggal_now'     => $dateNow,
              'tanggal_yes'     => $date2,
            )
          );
        } else {
          if (empty($data_absensisend)) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan!.',
                'masuk' => $noShift,
                'data_user'     => $data_user,
                'data_shifting'     => $data_shiftingsend,
                'tanggal_now'     => $dateNow,
                'tanggal_yes'     => $date2,
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'masuk' => $noShift,
                'data_absensi'     => $data_absensisend,
                'data_shifting'     => $data_shiftingsend,
                'tanggal_now'     => $dateNow,
                'tanggal_yes'     => $date2,
              )
            );
          }
        }
      }
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Absensi tidak ditemukan!.',
          'masuk' => $noShift,
          'data_user'     => $data_user,
          'tanggal_now'     => $dateNow,
          'tanggal_yes'     => $date2,
        )
      );
    }

    print json_encode($arr_result);
  }




  public function find_shifting_test_post()
  {
    $nik  = $this->input->post('nik');

    $data_shifting_now                       = $this->Mabsensi->findShiftingMasuk($nik, 'row');
    $data_shifting_yes                       = $this->Mabsensi->findShiftingYes($nik, 'row');
    $dateNow = date('Y-m-d');
    $date2 = date('Y-m-d', strtotime(' -1 day'));
    $date3 = date('Y-m-d', strtotime(' +1 day'));
    $time = date('H:i');
    $query = array(
      'nik' => $nik,
    );
    $data_user = $this->Mpengguna->find($query, 'row');
    $arr_result = array();
    $STATUS = '';
    $isError = FALSE;
    $data_absensisend = '';
    $data_shiftingsend = '';

    if (empty($data_shifting_yes)) {
      if (empty($data_shifting_now)) {
        $isError = TRUE;
      } else {
        echo "1 " . @$data_shifting_now->absensi_jadwal_id;
        if ($data_shifting_now->absensi_jadwal_id == "") {
          $isError = TRUE;
        } else {
          $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
          $data_shiftingsend                      = $data_shifting_now;
          if (empty($data_absensisend)) {
            $isError = TRUE;
          }
        }
        $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
        $data_shiftingsend                      = $data_shifting_now;
        if (empty($data_absensisend)) {
          $isError = TRUE;
        }
      }
    } else {
      if (!empty($data_shifting_now)) {
        if (@$data_shifting_now->absensi_jadwal_id == "") {
          $isError = TRUE;
        } else {
          echo "2 " . @$data_shifting_now->absensi_jadwal_id;
          $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
          $data_shiftingsend                      = $data_shifting_now;
          if (empty($data_absensisend)) {
            $isError = TRUE;
          }
          $timestamp = strtotime($data_shifting_now->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
          // $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 140 * 60 * 2;
          $timeMasuk = date('H:i', $timestamp);
          if ($time <= $timeMasuk || $data_shifting_now->absensi_jadwal_status == 'OFF') {
            if (@$data_shifting_now->absensi_jadwal_id == "") {
              $isError = TRUE;
            } else {
              echo "4 " . @$data_shifting_yes->absensi_jadwal_id;
              $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_yes->absensi_jadwal_id);
              $data_shiftingsend                      = $data_shifting_yes;
              if (empty($data_absensisend)) {
                $isError = TRUE;
              }
            }
          }
        }
      } else {
        if (@$data_shifting_yes->absensi_jadwal_id == "") {
          $isError = TRUE;
        } else {
          echo "3 " . @$data_shifting_yes->absensi_jadwal_id;
          $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_yes->absensi_jadwal_id);
          $data_shiftingsend                      = $data_shifting_yes;
          if (empty($data_absensisend)) {
            $isError = TRUE;
          }
        }
      }
    }
    if ($isError == FALSE) {

      $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
      // $timestamp = strtotime($data_shiftingsend->absensi_jadwal_jam_masuk) - 140 * 60 * 2;
      $timeMasuk = date('H:i', $timestamp);
      // if ($data_absensisend->tanggal_absensi == $dateNow && $data_absensisend->tanggal_absensi_keluar == $dateNow) {
      //   echo "masuk1";
      // } elseif ($data_absensisend->tanggal_absensi == $dateNow && $data_absensisend->tanggal_absensi_keluar == $date3) {
      //   echo "masuk2";
      // } elseif ($data_absensisend->tanggal_absensi == $date2 && $data_absensisend->tanggal_absensi_keluar == $dateNow) {
      //   echo "masuk3";
      // } elseif ($data_absensisend->tanggal_absensi == $date2 && $data_absensisend->tanggal_absensi_keluar == $date2) {
      //   echo "masuk4";
      //   if ($time <= $timeMasuk) {
      //     echo "masuk1";
      //     $isError = FALSE;
      //   } else {
      //     $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
      //     $data_absensisend                       = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
      //     $data_shiftingsend                      = $data_shifting_now;
      //     echo "update1";
      //     $isError = FALSE;
      //     if ($data_absensisend->absensikaryawan_areakerja_masuk == '') {
      //       $data['status_absen_masuk']                  = 'Tidak Absen In';
      //     }
      //     $data['status_absen_keluar']                  = 'Tidak Absen Out';
      //     $where['absensi_jadwal_id']                   = @$data_shifting_yes->absensi_jadwal_id;
      //     $this->db->update('absensi_karyawan', $data, $where);
      //   }
      // }

      if ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('00:00') && $data_shiftingsend->absensi_jadwal_jam_keluar <= date('12:00')) {
        if ($time <= $timeMasuk) {
          echo "masuk1";
          $isError = FALSE;
        } else {
          $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
          if ($data_absensisend->status_absen_masuk == '' && $data_absensisend->absensikaryawan_areakerja_masuk == '') {
            if (@$data_shifting_yes->absensi_jadwal_id != null) {
              $data['status_absen_masuk']                  = 'Tidak Absen IN';
              $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
              $this->db->update('absensi_karyawan', $data, $where);
            }
          }
          if ($data_absensisend->status_absen_keluar == '' && $data_absensisend->absensikaryawan_areakerja_keluar == '') {
            if ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('H:i') && $data_shiftingsend->absensi_jadwal_tanggal != date('Y-m-d')) {
              if ($data_shiftingsend->absensi_jadwal_jam_keluar == date(date('Y-m-d'), strtotime(' -1 day'))) {
                if (@$data_shifting_yes->absensi_jadwal_id != null) {
                  $data['status_absen_keluar']                  = 'Tidak Absen OUT';
                  $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                  $this->db->update('absensi_karyawan', $data, $where);
                }
              }
            }
          }

          if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
            if (@$data_shifting_yes->absensi_jadwal_id != null) {
              $data['status_absen_keluar']                  = 'Alpa';
              $data['status_absen_masuk']                   = 'Alpa';
              $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
              $this->db->update('absensi_karyawan', $data, $where);
            }
          }
          if (!empty($data_shifting_now)) {
            $data_absensisend_now               = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
            $data_shiftingsend                  = $data_shifting_now;
            echo "update1";

            $data_absensisend                      = $data_absensisend_now;
            $isError = FALSE;
          } else {
            $isError = TRUE;
          }
        }
      } elseif ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('12:00')) {
        if ($time <= $timeMasuk) {
          echo "masuk2";
          $isError = FALSE;
        } else {
          $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
          if ($data_absensisend->status_absen_masuk == '' && $data_absensisend->absensikaryawan_areakerja_masuk == '') {
            if (@$data_shifting_yes->absensi_jadwal_id != null) {
              $data['status_absen_masuk']                  = 'Tidak Absen IN';
              $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
              $this->db->update('absensi_karyawan', $data, $where);
            }
          }
          if ($data_absensisend->status_absen_keluar == '' && $data_absensisend->absensikaryawan_areakerja_keluar == '') {
            if ($data_shiftingsend->absensi_jadwal_jam_keluar >= date('H:i') && $data_shiftingsend->absensi_jadwal_tanggal != date('Y-m-d')) {
              if ($data_shiftingsend->absensi_jadwal_jam_keluar == date(date('Y-m-d'), strtotime(' -1 day'))) {
                if (@$data_shifting_yes->absensi_jadwal_id != null) {
                  $data['status_absen_keluar']                  = 'Tidak Absen OUT';
                  $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
                  $this->db->update('absensi_karyawan', $data, $where);
                }
              }
            }
          }

          if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
            if (@$data_shifting_yes->absensi_jadwal_id != null) {
              $data['status_absen_keluar']                  = 'Alpa';
              $data['status_absen_masuk']                   = 'Alpa';
              $where['absensi_jadwal_id']                   = $data_shifting_yes->absensi_jadwal_id;
              $this->db->update('absensi_karyawan', $data, $where);
            }
          }
          if (!empty($data_shifting_now)) {
            $data_absensisend_now               = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
            $data_shiftingsend                  = $data_shifting_now;
            echo "update2";

            $data_absensisend                   = $data_absensisend_now;
            $isError = FALSE;
          } else {
            $isError = TRUE;
          }
        }
      } else {
        if ($data_absensisend->status_absen_masuk == 'OFF' || $data_absensisend->status_absen_keluar == "OFF") {
          $data_shifting_now                  = $this->Mabsensi->findShiftingMasuk($nik, 'row');
          if (!empty($data_shifting_now)) {
            $data_absensisend_now               = $this->Mabsensi->findByShifting(@$data_shifting_now->absensi_jadwal_id);
            $data_shiftingsend                  = $data_shifting_now;
            echo "update4";

            $data_absensisend                   = $data_absensisend_now;
            $isError = FALSE;
          } else {
            $isError = TRUE;
          }
        } else {
          $isError = TRUE;
        }
        // echo "gagal";
      }
    }

    // die();
    $noShift = "No";

    if ($isError == TRUE) {
      $data_absensi = $this->Mabsensi->findNewNewNa($nik, 'row');
      if (empty($data_absensi)) {
        $data_absensi = $this->Mabsensi->findNewNewNaYesNa($nik, 'row');
        if (empty($data_absensi)) {
          $data_absensi = $this->Mabsensi->findNewNewNaKeluarYesNa($nik, 'row');
          if (empty($data_absensi)) {
            $data_absensi = $this->Mabsensi->findNewNewNaKeluarYesNaBaru($nik, 'row');
          }
        }
      }
      $data_absensisend                       = $data_absensi;
      $noShift = "Yes";

      if (empty($data_absensisend)) {
        $isError = TRUE;
      } else {
        if ($data_absensisend->tanggal_absensi == null) {

          if ($data_absensisend->tanggal_absensi_keluar == null) {

            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == null) {
              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                $isError = FALSE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                      $isError = TRUE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                            $isError = FALSE;
                          } else {
                            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                              $isError = FALSE;
                            } else {
                              $isError = TRUE;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          } else {
            if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                $isError = TRUE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {
                      $isError = FALSE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                            $isError = FALSE;
                          } else {
                            $isError = TRUE;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        } else {

          if ($data_absensisend->tanggal_absensi_keluar == null) {
            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == null) {

              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                $isError = FALSE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                      $isError = TRUE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                            $isError = FALSE;
                          } else {
                            if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                              $isError = FALSE;
                            } else {
                              $isError = TRUE;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          } else {
            if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {

              $isError = FALSE;
            } else {
              if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                $isError = TRUE;
              } else {
                if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                  $isError = TRUE;
                } else {
                  if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                    $isError = TRUE;
                  } else {
                    if ($data_absensisend->tanggal_absensi == date('Y-m-d') && $data_absensisend->tanggal_absensi_keluar == null) {

                      $isError = FALSE;
                    } else {
                      if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d')) {
                        $isError = FALSE;
                      } else {
                        if ($data_absensisend->tanggal_absensi == date('Y-m-d', strtotime(' -1 day')) && $data_absensisend->tanggal_absensi_keluar == null) {
                          $isError = FALSE;
                        } else {
                          if ($data_absensisend->tanggal_absensi == null && $data_absensisend->tanggal_absensi_keluar == date('Y-m-d', strtotime(' -1 day'))) {
                            $isError = FALSE;
                          } else {
                            $isError = TRUE;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    // $data_shift_all  = $this->Mabsensi->findShiftingYesAll('result');
    // if (!empty($data_shift_all)) {
    //   foreach ($data_shift_all as $shiftAll) {
    //     $timestamp = strtotime($shiftAll->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
    //     $timeMasuk = date('H:i', $timestamp);
    //     $dataAbsenAll = $this->Mabsensi->findByShifting(@$shiftAll->absensi_jadwal_id);
    //     if (!empty($dataAbsenAll)) {
    //       if ($shiftAll->absensi_jadwal_jam_keluar >= date('00:00') && $shiftAll->absensi_jadwal_jam_keluar <= date('12:00')) {
    //         if ($time <= $timeMasuk) {
    //         } else {
    //           if ($dataAbsenAll->status_absen_masuk == '') {
    //             $data['status_absen_masuk']                  = 'Tidak Absen IN';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //           if ($dataAbsenAll->status_absen_keluar == '') {
    //             $data['status_absen_keluar']                  = 'Tidak Absen OUT';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //           if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
    //             $data['status_absen_keluar']                  = 'Alpa';
    //             $data['status_absen_masuk']                   = 'Alpa';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //         }
    //       } elseif ($shiftAll->absensi_jadwal_jam_keluar >= date('12:00')) {
    //         if ($time <= $timeMasuk) {
    //           // echo "masuk2";
    //         } else {
    //           // echo "update2";
    //           if ($dataAbsenAll->status_absen_masuk == '') {
    //             $data['status_absen_masuk']                  = 'Tidak Absen IN';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //           if ($dataAbsenAll->status_absen_keluar == '') {
    //             $data['status_absen_keluar']                  = 'Tidak Absen OUT';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }

    //           if ($data_absensisend->status_absen_masuk == 'Tidak Absen IN' && $data_absensisend->status_absen_keluar == 'Tidak Absen OUT') {
    //             $data['status_absen_keluar']                  = 'Alpa';
    //             $data['status_absen_masuk']                   = 'Alpa';
    //             $where['absensi_jadwal_id']                   = @$shiftAll->absensi_jadwal_id;
    //             $this->db->update('absensi_karyawan', $data, $where);
    //           }
    //         }
    //       } else {
    //         // echo "gagal";
    //       }
    //     }
    //   }
    // }




    if ($isError == FALSE) {
      if (empty($data_shiftingsend)) {
        if (empty($data_absensisend)) {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'error',
              'message' => 'Absensi tidak ditemukan!.',
              'data_user'     => $data_user,
              'tanggal_now'     => $dateNow,
              'tanggal_yes'     => $date2,
            )
          );
        } else {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'masuk' => $STATUS,
              'data_absensi'     => $data_absensisend,
              'tanggal_now'     => $dateNow,
              'tanggal_yes'     => $date2,
            )
          );
        }
      } else {
        if ($noShift == 'Yes') {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'masuk' => $STATUS,
              'data_absensi'     => $data_absensisend,
              'tanggal_now'     => $dateNow,
              'tanggal_yes'     => $date2,
            )
          );
        } else {
          if (empty($data_absensisend)) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan!.',
                'data_user'     => $data_user,
                'data_shifting'     => $data_shiftingsend,
                'tanggal_now'     => $dateNow,
                'tanggal_yes'     => $date2,
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'masuk' => $STATUS,
                'data_absensi'     => $data_absensisend,
                'data_shifting'     => $data_shiftingsend,
                'tanggal_now'     => $dateNow,
                'tanggal_yes'     => $date2,
              )
            );
          }
        }
      }
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Absensi tidak ditemukan!.',
          'data_user'     => $data_user,
          'tanggal_now'     => $dateNow,
          'tanggal_yes'     => $date2,
        )
      );
    }

    print json_encode($arr_result);
  }
  public function setUpdate_post()
  {

    // $data_shift_all  = $this->Mabsensi->findShiftingYesAll('result');
    // $time = date('H:i');
    // $dateNA = date('Y-m-d', strtotime("-1 day", strtotime(date("Y-m-d"))));
    // if (!empty($data_shift_all)) {
    //   foreach ($data_shift_all as $shiftAll) {
    //     if ($shiftAll->absensi_jadwal_status != 'OFF') {
    //       $timestamp = strtotime($shiftAll->absensi_jadwal_jam_masuk) - 60 * 60 * 2;
    //       $timeMasuk = date('H:i', $timestamp);
    //       $dataAbsenAll = $this->Mabsensi->findByShifting(@$shiftAll->absensi_jadwal_id);
    //       if (!empty($dataAbsenAll)) {
    //         if ($dataAbsenAll->fda_request_id == NULL || $dataAbsenAll->fda_request_id == "0") {
    //           $dataBAru['status_absen_keluar']                  = NULL;
    //           $dataBAru['status_absen_masuk']                  = NULL;
    //           $where['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //           $this->db->update('absensi_karyawan', $dataBAru, $where);
    //         }
    //         if ($dataAbsenAll->absensikaryawan_time_keluar <= $shiftAll->absensi_jadwal_jam_keluar) {
    //           $status_absen_keluar = 'Pulang Cepat';
    //           $dataBAru['status_absen_keluar']                  = $status_absen_keluar;
    //           $where['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //           $this->db->update('absensi_karyawan', $dataBAru, $where);
    //         }

    //         if ($dataAbsenAll->absensikaryawan_time_masuk >= $shiftAll->absensi_jadwal_jam_masuk) {
    //           $status_absen_masuk = 'Datang Telat';
    //           $dataBAru['status_absen_masuk']                  = $status_absen_masuk;
    //           $where['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //           $this->db->update('absensi_karyawan', $dataBAru, $where);
    //         }

    //         if ($shiftAll->absensi_jadwal_jam_keluar >= date('00:00') && $shiftAll->absensi_jadwal_jam_keluar <= date('12:00')) {
    //           if ($time <= $timeMasuk) {
    //             echo "masuk1";
    //           } else {
    //             echo "update1";
    //             if ($dataAbsenAll->status_absen_masuk == '' && $dataAbsenAll->absensikaryawan_areakerja_masuk == '') {
    //               if (@$shiftAll->absensi_jadwal_id != null) {
    //                 $data['status_absen_masuk']                  = 'Tidak Absen IN';
    //                 $where['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                 $this->db->update('absensi_karyawan', $data, $where);
    //               }
    //             }
    //             if ($dataAbsenAll->status_absen_keluar == '' && $dataAbsenAll->absensikaryawan_areakerja_keluar == '') {
    //               if ($dataAbsenAll->tanggal_absensi_keluar == NULL || $dataAbsenAll->tanggal_absensi_keluar == $dateNA) {
    //                 if (@$shiftAll->absensi_jadwal_id != null) {
    //                   echo "update out ATAS";
    //                   $data1['status_absen_keluar']                  = 'Tidak Absen OUT';
    //                   $where1['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                   $this->db->update('absensi_karyawan', $data1, $where1);
    //                 }
    //               }
    //             }
    //             if ($dataAbsenAll->status_absen_keluar == 'PULANG CEPAT' && $dataAbsenAll->absensikaryawan_areakerja_keluar == '') {
    //               if ($dataAbsenAll->tanggal_absensi_keluar == NULL || $dataAbsenAll->tanggal_absensi_keluar == $dateNA) {
    //                 if (@$shiftAll->absensi_jadwal_id != null) {
    //                   echo "update out ATAS";
    //                   $data1['status_absen_keluar']                  = 'Tidak Absen OUT';
    //                   $where1['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                   $this->db->update('absensi_karyawan', $data1, $where1);
    //                 }
    //               }
    //             }
    //             if ($dataAbsenAll->status_absen_masuk == 'Tidak Absen IN' && $dataAbsenAll->status_absen_keluar == 'Tidak Absen OUT') {
    //               if (@$shiftAll->absensi_jadwal_id != null) {
    //                 $data2['status_absen_keluar']                  = 'Alpa';
    //                 $data2['status_absen_masuk']                   = 'Alpa';
    //                 $where2['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                 $this->db->update('absensi_karyawan', $data2, $where2);
    //               }
    //             }
    //           }
    //         } elseif ($shiftAll->absensi_jadwal_jam_keluar >= date('12:00')) {
    //           if ($time <= $timeMasuk) {
    //             echo "masuk2";
    //           } else {
    //             echo "update2";
    //             if ($dataAbsenAll->status_absen_masuk == '' && $dataAbsenAll->absensikaryawan_areakerja_masuk == '') {
    //               if (@$shiftAll->absensi_jadwal_id != null) {
    //                 $data3['status_absen_masuk']                  = 'Tidak Absen IN';
    //                 $where3['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                 $this->db->update('absensi_karyawan', $data3, $where3);
    //               }
    //             }
    //             if ($dataAbsenAll->status_absen_keluar == '' && $dataAbsenAll->absensikaryawan_areakerja_keluar == '') {
    //               if ($dataAbsenAll->tanggal_absensi_keluar == NULL || $dataAbsenAll->tanggal_absensi_keluar == $dateNA) {
    //                 if (@$shiftAll->absensi_jadwal_id != NULL) {
    //                   echo "update out";
    //                   $data4['status_absen_keluar']                  = 'Tidak Absen OUT';
    //                   $where4['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                   $this->db->update('absensi_karyawan', $data4, $where4);
    //                 }
    //               }
    //             }
    //             if ($dataAbsenAll->status_absen_keluar == 'PULANG CEPAT' && $dataAbsenAll->absensikaryawan_areakerja_keluar == '') {
    //               if ($dataAbsenAll->tanggal_absensi_keluar == NULL || $dataAbsenAll->tanggal_absensi_keluar == $dateNA) {
    //                 if (@$shiftAll->absensi_jadwal_id != NULL) {
    //                   echo "update out";
    //                   $data4['status_absen_keluar']                  = 'Tidak Absen OUT';
    //                   $where4['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                   $this->db->update('absensi_karyawan', $data4, $where4);
    //                 }
    //               }
    //             }
    //             if ($dataAbsenAll->status_absen_masuk == 'Tidak Absen IN' && $dataAbsenAll->status_absen_keluar == 'Tidak Absen OUT') {
    //               if (@$shiftAll->absensi_jadwal_id != null) {
    //                 $data5['status_absen_keluar']                  = 'Alpa';
    //                 $data5['status_absen_masuk']                   = 'Alpa';
    //                 $where5['absensi_jadwal_id']                   = $shiftAll->absensi_jadwal_id;
    //                 $this->db->update('absensi_karyawan', $data5, $where5);
    //               }
    //             }
    //           }
    //         } else {
    //           // echo "gagal";
    //         }
    //       }
    //     }
    //   }
    // }
    // $arr_result = array(
    //   'jne_ittsm' => array(
    //     'status' => 'error',
    //     'message' => 'Absensi tidak ditemukan!.',
    //     'data_shift_all'     => $data_shift_all,
    //   )
    // );
    // print json_encode($arr_result);
  }

  public function find_yes_post()
  {
    $nik  = $this->input->post('nik');
    $absensi_karyawan_id = $this->input->post('absensi_karyawan_id');

    $data_absensi = $this->Mabsensi->findNow($nik, 'row');

    $arr_result = array();
    if (empty($data_absensi)) {
      $data_absensi = $this->Mabsensi->findYes($nik, 'row');
      if (empty($data_absensi)) {
        $data_absensi = $this->Mabsensi->findYesKel($nik, 'row');
        if (empty($data_absensi)) {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'error',
              'message' => 'Absensi tidak ditemukan!.',
            )
          );
        } else {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'data_absensi'     => $data_absensi,
            )
          );
        }
      } else {
        if ($absensi_karyawan_id == $data_absensi->absensikaryawan_id) {
          $data_absensi_kel = $this->Mabsensi->findYesKel($nik, 'row');
          if (empty($data_absensi_kel)) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan!.',
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'data_absensi'     => $data_absensi_kel,
              )
            );
          }
        } else {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'data_absensi'     => $data_absensi,
            )
          );
        }
      }
    } else {
      if ($absensi_karyawan_id == $data_absensi->absensikaryawan_id) {
        $data_absensi = $this->Mabsensi->findYes($nik, 'row');
        if (empty($data_absensi)) {
          $data_absensi = $this->Mabsensi->findYesKel($nik, 'row');
          if (empty($data_absensi)) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan!.',
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'data_absensi'     => $data_absensi,
              )
            );
          }
        } else {
          if ($absensi_karyawan_id == $data_absensi->absensikaryawan_id) {
            $data_absensi_kel = $this->Mabsensi->findYesKel($nik, 'row');
            if (empty($data_absensi_kel)) {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'error',
                  'message' => 'Absensi tidak ditemukan!.',
                )
              );
            } else {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'success',
                  'message' => 'Data absensi ditemukan.',
                  'data_absensi'     => $data_absensi_kel,
                )
              );
            }
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'data_absensi'     => $data_absensi,
              )
            );
          }
        }
      } else {
        $data_absensi_kel = $this->Mabsensi->findNowKelaur($nik, 'row');
        if (empty($data_absensi_kel)) {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Data absensi ditemukan.',
              'data_absensi'     => $data_absensi,
            )
          );
        } else {
          if ($data_absensi_kel->tanggal_absensi == null) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'data_absensi'     => $data_absensi,
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Data absensi ditemukan.',
                'data_absensi'     => $data_absensi_kel,
              )
            );
          }
        }
      }
    }

    print json_encode($arr_result);
  }

  public function find_history_post()
  {
    $nik  = $this->input->post('nik');
    $order_by            = 'absensikaryawan_id';
    $ordering            = "desc";
    $limit               = 10;
    $page                = 0;

    if (isset($_POST['order_by'])) {
      $order_by = $this->input->post('order_by');
    }

    if (isset($_POST['ordering'])) {
      $ordering = $this->input->post('ordering');
    }

    if (isset($_POST['limit'])) {
      $limit = $this->input->post('limit');
    }

    if (isset($_POST['page'])) {
      $page = $this->input->post('page');
    }


    $option = array(
      'limit' => $limit,
      'page'  => $page,
      'order' => array(
        'order_by' => $order_by,
        'ordering' => $ordering,
      ),
    );
    $data_absensi = $this->Mabsensi->findHistory($nik, 'result', $option);

    $arr_result = array();
    if (empty($data_absensi)) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Absensi tidak ditemukan!.',
        )
      );
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'success',
          'message' => 'Data absensi ditemukan.',
          'data_absensi'     => $data_absensi,
        )
      );
    }

    print json_encode($arr_result);
  }

  public function jadwalALL_post()
  {
    $nik  = $this->input->post('nik');
    $order_by            = 'absensi_jadwal_tanggal';
    $ordering            = "ASC";
    $limit               = 10;
    $page                = 0;

    if (isset($_POST['order_by'])) {
      $order_by = $this->input->post('order_by');
    }

    if (isset($_POST['ordering'])) {
      $ordering = $this->input->post('ordering');
    }

    if (isset($_POST['limit'])) {
      $limit = $this->input->post('limit');
    }

    if (isset($_POST['page'])) {
      $page = $this->input->post('page');
    }


    $option = array(
      'limit' => $limit,
      'page'  => $page,
      'order' => array(
        'order_by' => $order_by,
        'ordering' => $ordering,
      ),
    );
    $data_absensi = $this->Mabsensi->find_all_jadwal($nik, 'result', $option);

    $arr_result = array();
    if (empty($data_absensi)) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Absensi tidak ditemukan!.',
        )
      );
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'success',
          'message' => 'Data absensi ditemukan.',
          'data_absensi'     => $data_absensi,
        )
      );
    }

    print json_encode($arr_result);
  }
  public function getTime_post()
  {
    $nik    = $this->input->post('nik');
    $branch = $this->input->post('branch');
    // $date2=date('H:i', strtotime(' -15 minutes'));
    // if ($branch = 'TSM000') {
    //   $date2 = date('H:i', strtotime(' -7 minutes'));
    // } else {
    $date2 = date('H:i');
    // }

    $arr_result = array(
      'jne_ittsm' => array(
        'status' => 'success',
        'message' => 'Data absensi ditemukan.',
        'timeNa'     => $date2,
      )
    );

    print json_encode($arr_result);
  }

  public function getDate_post()
  {
    $dateNow = date('Y-m-d');

    $arr_result = array(
      'jne_ittsm' => array(
        'status' => 'success',
        'message' => 'Data absensi ditemukan.',
        'timeNa'     => $dateNow,
      )
    );

    print json_encode($arr_result);
  }

  public function create_absensi_post()
  {
    $area_kerja_id  = $this->input->post('area_kerja_id');
    $nik  = $this->input->post('nik');
    $keterangan  = $this->input->post('keterangan');
    $longitude_user  = $this->input->post('longitude_user');
    $latitude_user  = $this->input->post('latitude_user');
    $modul_id  = $this->input->post('modul_id');
    $isMasuk  = $this->input->post('isMasuk');
    $isKeluar  = $this->input->post('isKeluar');
    $checkGlobal = $this->Mabsensi->checkGlobal($nik);
    if (empty($checkGlobal)) {
      $chekjarak = $this->Mabsensi->checkJarak($latitude_user, $longitude_user, $nik);
    } else {
      if ($checkGlobal->is_global == 1) {
        $chekjarak = $this->Mabsensi->checkJarakGlobal($latitude_user, $longitude_user);
      } else {
        $chekjarak = $this->Mabsensi->checkJarak($latitude_user, $longitude_user, $nik);
      }
    }

    $timeNow = date('H:i');
    // if ($modul_id == 2) {
    //   $chekjarak=$this->Mabsensi->checkJarakPic($latitude_user,$longitude_user);
    // }else {
    //   $chekjarak=$this->Mabsensi->checkJarak($latitude_user,$longitude_user,$nik);
    // }
    if (empty($chekjarak)) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Lokasi anda terlalu jauh dari titik absen, silahkan mendekati area kerja anda!.',
          'chekjarak' => $chekjarak,
        )
      );
    } else {

      $data_absensi = $this->Mabsensi->findNow($nik, 'row');

      $arr_result = array();
      if (empty($data_absensi)) {
        $data_absensi = $this->Mabsensi->findYesKel($nik, 'row');
        if (empty($data_absensi)) {
          $fileName = $nik . date('Ymd') . $this->input->post('fileName');
          $fileImage = $this->input->post('fileImage');
          $binaryImage = base64_decode($fileImage);
          header('Content-Type: bitmap; charset=utf-8');
          //gambar akan disimpan pada folder image
          $fileGAMBAR = fopen('../uploads/image_absensi/' . $fileName, 'wb');
          // Create File
          fwrite($fileGAMBAR, $binaryImage);
          fclose($fileGAMBAR);
          $binryImage = base64_decode($fileImage);
          $data = array(
            'nik' => $nik,
            'absensikaryawan_areakerja_masuk' => $chekjarak->areakerja_id,
            'absensikaryawan_time_masuk' => $timeNow,
            'absensikaryawan_date_masuk' => date('Y-m-d H:i:s'),
            'absensikaryawan_ket_masuk' => $keterangan,
            'absensikaryawan_photo_masuk' => $fileName,
            'absensikaryawan_jarak_masuk' => $chekjarak->distance,
            'absensikaryawan_latitude_masuk' => $latitude_user,
            'absensikaryawan_longitude_masuk' => $longitude_user,
          );
          $data_absensi = $this->Mabsensi->create($data);
          if ($data_absensi) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Berhasil disimpan.',
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Gagal disimpan.',
              )
            );
          }
        } else {
          $fileName = $nik . date('Ymd') . $this->input->post('fileName');
          $fileImage = $this->input->post('fileImage');
          $binaryImage = base64_decode($fileImage);
          header('Content-Type: bitmap; charset=utf-8');
          //gambar akan disimpan pada folder image
          $fileGAMBAR = fopen('../uploads/image_absensi/' . $fileName, 'wb');
          // Create File
          fwrite($fileGAMBAR, $binaryImage);
          fclose($fileGAMBAR);
          $binryImage = base64_decode($fileImage);
          $absensikaryawan_id = $data_absensi->absensikaryawan_id;
          $where = array('absensikaryawan_id' => $absensikaryawan_id,);
          $data = array(
            'absensikaryawan_time_keluar' => $timeNow,
            'absensikaryawan_areakerja_keluar' => $chekjarak->areakerja_id,
            'absensikaryawan_date_keluar' => date('Y-m-d H:i:s'),
            'absensikaryawan_ket_keluar' => $keterangan,
            'absensikaryawan_photo_keluar' => $fileName,
            'absensikaryawan_jarak_keluar' => $chekjarak->distance,
            'absensikaryawan_latitude_keluar' => $latitude_user,
            'absensikaryawan_longitude_keluar' => $longitude_user,
          );
          $data_absensi = $this->Mabsensi->update($data, $where);
          if ($data_absensi) {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'success',
                'message' => 'Berhasil disimpan.',
              )
            );
          } else {
            $arr_result = array(
              'jne_ittsm' => array(
                'status' => 'error',
                'message' => 'Gagal disimpan.',
              )
            );
          }
        }
      } else {
        $fileName = $nik . date('Ymd') . $this->input->post('fileName');
        $fileImage = $this->input->post('fileImage');
        $binaryImage = base64_decode($fileImage);
        header('Content-Type: bitmap; charset=utf-8');
        //gambar akan disimpan pada folder image
        $fileGAMBAR = fopen('../uploads/image_absensi/' . $fileName, 'wb');
        // Create File
        fwrite($fileGAMBAR, $binaryImage);
        fclose($fileGAMBAR);
        $binryImage = base64_decode($fileImage);
        $absensikaryawan_id = $data_absensi->absensikaryawan_id;
        $where = array('absensikaryawan_id' => $absensikaryawan_id,);
        $data = array(
          'absensikaryawan_time_keluar' => $timeNow,
          'absensikaryawan_areakerja_keluar' => $chekjarak->areakerja_id,
          'absensikaryawan_date_keluar' => date('Y-m-d H:i:s'),
          'absensikaryawan_ket_keluar' => $keterangan,
          'absensikaryawan_photo_keluar' => $fileName,
          'absensikaryawan_jarak_keluar' => $chekjarak->distance,
          'absensikaryawan_latitude_keluar' => $latitude_user,
          'absensikaryawan_longitude_keluar' => $longitude_user,
        );
        $data_absensi = $this->Mabsensi->update($data, $where);
        if ($data_absensi) {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'success',
              'message' => 'Berhasil disimpan.',
            )
          );
        } else {
          $arr_result = array(
            'jne_ittsm' => array(
              'status' => 'error',
              'message' => 'Gagal disimpan.',
            )
          );
        }
      }
    }


    print json_encode($arr_result);
  }


  public function create_absensi_new_post()
  {
    $area_kerja_id  = $this->input->post('area_kerja_id');
    $nik  = $this->input->post('nik');
    $keterangan  = $this->input->post('keterangan');
    $longitude_user  = $this->input->post('longitude_user');
    $latitude_user  = $this->input->post('latitude_user');
    $modul_id  = $this->input->post('modul_id');
    $isMasuk  = $this->input->post('isMasuk');
    $isKeluar  = $this->input->post('isKeluar');
    $isSudahNa  = $this->input->post('isSudahNa');
    $isMasukSudah  = $this->input->post('isMasukSudah');
    $isKeluarSudah  = $this->input->post('isKeluarSudah');
    $absensikaryawan_id  = $this->input->post('absensiKaryawanId');

    $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();
    $chekjarak = $this->Mabsensi->checkJarak($latitude_user, $longitude_user, $nik);
    if (empty($chekjarak)) {
      $checkGlobal = $this->Mabsensi->checkGlobal($nik, $employee->branch_code, 1);
      if (empty($checkGlobal)) {
        $checkGlobal = $this->Mabsensi->checkGlobal($nik, $employee->branch_code, 2);
        if (empty($checkGlobal)) {
          $chekjarak = $this->Mabsensi->checkJarak($latitude_user, $longitude_user, $nik);
        } else {
          $chekjarak = $this->Mabsensi->checkJarakGlobalArea($employee->branch_code, $latitude_user, $longitude_user);
        }
      } else {
        $chekjarak = $this->Mabsensi->checkJarakGlobal($employee->branch_code, $latitude_user, $longitude_user);
      }
    }

    $timeNow = date('H:i');
    $dateNowNa = date('Y-m-d H:i:s');
    if (empty($chekjarak)) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Lokasi anda terlalu jauh dari titik absen, silahkan mendekati area kerja anda!.',
          'chekjarak' => $chekjarak,
        )
      );
    } else {
      $areakerja_id = $chekjarak->areakerja_id;
      $distance = $chekjarak->distance;
      $isError = FALSE;
      if ($isSudahNa == 1) {
        $fileName = $nik . date('Ymd') . $this->input->post('fileName');
        $fileImage = $this->input->post('fileImage');
        $binaryImage = base64_decode($fileImage);
        header('Content-Type: bitmap; charset=utf-8');
        //gambar akan disimpan pada folder image
        $fileGAMBAR = fopen('../uploads/image_absensi/' . $fileName, 'wb');
        // Create File
        fwrite($fileGAMBAR, $binaryImage);
        fclose($fileGAMBAR);
        $binryImage = base64_decode($fileImage);
        $isError = FALSE;
      } else {
        if ($employee->branch_code == "TSM000") {
          $isError = TRUE;
        } else {
          $isError = FALSE;
        }
        $fileName = "";
      }

      $arr_result = array();
      if ($isError == FALSE) {
        if ($isMasuk == 1) {
          if ($isKeluarSudah == 1) {
            $where = array('absensikaryawan_id' => $absensikaryawan_id,);
            $data = array(
              'absensikaryawan_areakerja_masuk' => $areakerja_id,
              'absensikaryawan_time_masuk' => $timeNow,
              'absensikaryawan_date_masuk' => $dateNowNa,
              'absensikaryawan_ket_masuk' => $keterangan,
              'absensikaryawan_photo_masuk' => $fileName,
              'absensikaryawan_jarak_masuk' => $distance,
              'absensikaryawan_latitude_masuk' => $latitude_user,
              'absensikaryawan_longitude_masuk' => $longitude_user,
            );
            $data_absensi = $this->Mabsensi->update($data, $where);
            if ($data_absensi) {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'success',
                  'message' => 'Berhasil diupdate.',
                )
              );
            } else {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'error',
                  'message' => 'Gagal disimpan.',
                )
              );
            }
          } else {
            $data = array(
              'nik' => $nik,
              'absensikaryawan_areakerja_masuk' => $areakerja_id,
              'absensikaryawan_time_masuk' => $timeNow,
              'absensikaryawan_date_masuk' => $dateNowNa,
              'absensikaryawan_ket_masuk' => $keterangan,
              'absensikaryawan_photo_masuk' => $fileName,
              'absensikaryawan_jarak_masuk' => $distance,
              'absensikaryawan_latitude_masuk' => $latitude_user,
              'absensikaryawan_longitude_masuk' => $longitude_user,
            );
            $data_absensi = $this->Mabsensi->create($data);
            if ($data_absensi) {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'success',
                  'message' => 'Berhasil disimpan.',
                )
              );
            } else {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'error',
                  'message' => 'Gagal disimpan.',
                )
              );
            }
          }
        } elseif ($isKeluar == 1) {
          if ($isMasukSudah == 1) {
            $where = array('absensikaryawan_id' => $absensikaryawan_id,);
            $data = array(
              'absensikaryawan_areakerja_keluar' => $areakerja_id,
              'absensikaryawan_time_keluar' => $timeNow,
              'absensikaryawan_date_keluar' => $dateNowNa,
              'absensikaryawan_ket_keluar' => $keterangan,
              'absensikaryawan_photo_keluar' => $fileName,
              'absensikaryawan_jarak_keluar' => $distance,
              'absensikaryawan_latitude_keluar' => $latitude_user,
              'absensikaryawan_longitude_keluar' => $longitude_user,
            );
            $data_absensi = $this->Mabsensi->update($data, $where);
            if ($data_absensi) {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'success',
                  'message' => 'Berhasil diupdate.',
                )
              );
            } else {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'error',
                  'message' => 'Gagal disimpan.',
                )
              );
            }
          } else {
            $data = array(
              'nik' => $nik,
              'absensikaryawan_areakerja_keluar' => $areakerja_id,
              'absensikaryawan_time_keluar' => $timeNow,
              'absensikaryawan_date_keluar' => $dateNowNa,
              'absensikaryawan_ket_keluar' => $keterangan,
              'absensikaryawan_photo_keluar' => $fileName,
              'absensikaryawan_jarak_keluar' => $distance,
              'absensikaryawan_latitude_keluar' => $latitude_user,
              'absensikaryawan_longitude_keluar' => $longitude_user,
            );
            $data_absensi = $this->Mabsensi->create($data);
            if ($data_absensi) {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'success',
                  'message' => 'Berhasil disimpan.',
                )
              );
            } else {
              $arr_result = array(
                'jne_ittsm' => array(
                  'status' => 'error',
                  'message' => 'Gagal disimpan.',
                )
              );
            }
          }
        }
      } else {
        $arr_result = array(
          'jne_ittsm' => array(
            'status' => 'error',
            'message' => 'Anda harus melakukan selfie terlebih dahulu.',
          )
        );
      }
    }


    print json_encode($arr_result);
  }




  public function crt_shift_post()
  {
    $nik  = $this->input->post('nik');
    $keterangan  = $this->input->post('keterangan');
    $longitude_user  = $this->input->post('longitude_user');
    $latitude_user  = $this->input->post('latitude_user');
    $isMasuk  = $this->input->post('isMasuk');
    $isKeluar  = $this->input->post('isKeluar');
    $isSudahNa  = $this->input->post('isSudahNa');
    $isMasukSudah  = $this->input->post('isMasukSudah');
    $isKeluarSudah  = $this->input->post('isKeluarSudah');
    $absensikaryawan_id  = $this->input->post('absensiKaryawanId');
    $absensiJadwalId  = $this->input->post('absensiJadwalId');

    $employee        = $this->db->get_where('employee', ['nik' => $nik])->row();
    $chekjarak = $this->Mabsensi->checkJarak($latitude_user, $longitude_user, $nik);
    if (empty($chekjarak)) {
      $checkGlobal = $this->Mabsensi->checkGlobal($nik, $employee->branch_code, 1);
      if (empty($checkGlobal)) {
        $checkGlobal = $this->Mabsensi->checkGlobal($nik, $employee->branch_code, 2);
        if (empty($checkGlobal)) {
          $chekjarak = $this->Mabsensi->checkJarak($latitude_user, $longitude_user, $nik);
        } else {
          $chekjarak = $this->Mabsensi->checkJarakGlobalArea($employee->branch_code, $latitude_user, $longitude_user);
        }
      } else {
        $chekjarak = $this->Mabsensi->checkJarakGlobal($employee->branch_code, $latitude_user, $longitude_user);
      }
    }

    $timeNow = date('H:i');
    $dateNowNa = date('Y-m-d H:i:s');
    if (empty($chekjarak)) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Lokasi anda terlalu jauh dari titik absen, silahkan mendekati area kerja anda!.',
          'chekjarak' => $chekjarak,
        )
      );
    } else {
      $areakerja_id = $chekjarak->areakerja_id;
      $distance = $chekjarak->distance;
      $isError = FALSE;
      if ($isSudahNa == 1) {
        $fileName = $nik . date('Ymd') . $this->input->post('fileName');
        $fileImage = $this->input->post('fileImage');
        $binaryImage = base64_decode($fileImage);
        header('Content-Type: bitmap; charset=utf-8');
        //gambar akan disimpan pada folder image
        $fileGAMBAR = fopen('../uploads/image_absensi/' . $fileName, 'wb');
        // Create File
        fwrite($fileGAMBAR, $binaryImage);
        fclose($fileGAMBAR);
        $binryImage = base64_decode($fileImage);
        $isError = FALSE;
      } else {
        if ($employee->branch_code == "TSM000") {
          $isError = TRUE;
        } else {
          $isError = FALSE;
        }
        $fileName = "";
      }

      $arr_result = array();
      if ($isError == FALSE) {
        if ($absensiJadwalId == '' || $absensiJadwalId == 'null' || $absensiJadwalId == NULL) {
          if ($isMasuk == 1) {
            if ($isKeluarSudah == 1) {
              $where = array('absensikaryawan_id' => $absensikaryawan_id,);
              $data = array(
                'absensikaryawan_areakerja_masuk' => $areakerja_id,
                'absensikaryawan_time_masuk' => $timeNow,
                'absensikaryawan_date_masuk' => $dateNowNa,
                'absensikaryawan_ket_masuk' => $keterangan,
                'absensikaryawan_photo_masuk' => $fileName,
                'absensikaryawan_jarak_masuk' => $distance,
                'absensikaryawan_latitude_masuk' => $latitude_user,
                'absensikaryawan_longitude_masuk' => $longitude_user,
              );
              $data_absensi = $this->Mabsensi->update($data, $where);
              if ($data_absensi) {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Berhasil diupdate.',
                  )
                );
              } else {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan.',
                  )
                );
              }
            } else {
              $data = array(
                'nik' => $nik,
                'absensikaryawan_areakerja_masuk' => $areakerja_id,
                'absensikaryawan_time_masuk' => $timeNow,
                'absensikaryawan_date_masuk' => $dateNowNa,
                'absensikaryawan_ket_masuk' => $keterangan,
                'absensikaryawan_photo_masuk' => $fileName,
                'absensikaryawan_jarak_masuk' => $distance,
                'absensikaryawan_latitude_masuk' => $latitude_user,
                'absensikaryawan_longitude_masuk' => $longitude_user,
              );
              $data_absensi = $this->Mabsensi->create($data);
              if ($data_absensi) {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Berhasil disimpan.',
                  )
                );
              } else {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan.',
                  )
                );
              }
            }
          } elseif ($isKeluar == 1) {
            if ($isMasukSudah == 1) {
              $where = array('absensikaryawan_id' => $absensikaryawan_id,);
              $data = array(
                'absensikaryawan_areakerja_keluar' => $areakerja_id,
                'absensikaryawan_time_keluar' => $timeNow,
                'absensikaryawan_date_keluar' => $dateNowNa,
                'absensikaryawan_ket_keluar' => $keterangan,
                'absensikaryawan_photo_keluar' => $fileName,
                'absensikaryawan_jarak_keluar' => $distance,
                'absensikaryawan_latitude_keluar' => $latitude_user,
                'absensikaryawan_longitude_keluar' => $longitude_user,
              );
              $data_absensi = $this->Mabsensi->update($data, $where);
              if ($data_absensi) {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Berhasil diupdate.',
                  )
                );
              } else {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan.',
                  )
                );
              }
            } else {
              $data = array(
                'nik' => $nik,
                'absensikaryawan_areakerja_keluar' => $areakerja_id,
                'absensikaryawan_time_keluar' => $timeNow,
                'absensikaryawan_date_keluar' => $dateNowNa,
                'absensikaryawan_ket_keluar' => $keterangan,
                'absensikaryawan_photo_keluar' => $fileName,
                'absensikaryawan_jarak_keluar' => $distance,
                'absensikaryawan_latitude_keluar' => $latitude_user,
                'absensikaryawan_longitude_keluar' => $longitude_user,
              );
              $data_absensi = $this->Mabsensi->create($data);
              if ($data_absensi) {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Berhasil disimpan.',
                  )
                );
              } else {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan.',
                  )
                );
              }
            }
          }
        } else {
          $data_shifting = $this->Mabsensi->findShiftingById($absensiJadwalId);
          if (!empty($data_shifting)) {
            if ($isKeluar == 1) {
              $status_absen_keluar = '';
              // if ($data_shifting->absensi_jadwal_jam_keluar <= $timeNow . ":00") {
              //   $status_absen_keluar = 'Pulang Cepat';
              // }
              $where = array('absensikaryawan_id' => $absensikaryawan_id,);
              $data = array(
                'absensikaryawan_areakerja_keluar' => $areakerja_id,
                'absensikaryawan_time_keluar' => $timeNow,
                'absensikaryawan_date_keluar' => $dateNowNa,
                'absensikaryawan_ket_keluar' => $keterangan,
                'absensikaryawan_photo_keluar' => $fileName,
                'absensikaryawan_jarak_keluar' => $distance,
                'absensikaryawan_latitude_keluar' => $latitude_user,
                'absensikaryawan_longitude_keluar' => $longitude_user,
                'status_absen_keluar' => $status_absen_keluar,
              );
              $data_absensi = $this->Mabsensi->update($data, $where);
              if ($data_absensi) {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Berhasil disimpan.',
                  )
                );
              } else {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan.',
                  )
                );
              }
            } elseif ($isMasuk == 1) {
              $status_absen_masuk = '';

              if ($timeNow >= $data_shifting->absensi_jadwal_jam_masuk . ":00") {
                $status_absen_masuk = 'Datang Telat';
              }
              $where = array('absensikaryawan_id' => $absensikaryawan_id,);
              $data = array(
                'absensikaryawan_areakerja_masuk' => $areakerja_id,
                'absensikaryawan_time_masuk' => $timeNow,
                'absensikaryawan_date_masuk' => $dateNowNa,
                'absensikaryawan_ket_masuk' => $keterangan,
                'absensikaryawan_photo_masuk' => $fileName,
                'absensikaryawan_jarak_masuk' => $distance,
                'absensikaryawan_latitude_masuk' => $latitude_user,
                'absensikaryawan_longitude_masuk' => $longitude_user,
                'status_absen_masuk' => $status_absen_masuk,
              );
              $data_absensi = $this->Mabsensi->update($data, $where);
              if ($data_absensi) {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'success',
                    'message' => 'Berhasil disimpan.',
                  )
                );
              } else {
                $arr_result = array(
                  'jne_ittsm' => array(
                    'status' => 'error',
                    'message' => 'Gagal disimpan.',
                  )
                );
              }
            }
          } else {
            // $arr_result = array(
            //   'jne_ittsm' => array(
            //     'status' => 'error',
            //     'message' => 'Anda Tidak Memiliki Data Shifting!.',
            //   )
            // );
            if ($isMasuk == 1) {
              if ($isKeluarSudah == 1) {
                $where = array('absensikaryawan_id' => $absensikaryawan_id,);
                $data = array(
                  'absensikaryawan_areakerja_masuk' => $areakerja_id,
                  'absensikaryawan_time_masuk' => $timeNow,
                  'absensikaryawan_date_masuk' => $dateNowNa,
                  'absensikaryawan_ket_masuk' => $keterangan,
                  'absensikaryawan_photo_masuk' => $fileName,
                  'absensikaryawan_jarak_masuk' => $distance,
                  'absensikaryawan_latitude_masuk' => $latitude_user,
                  'absensikaryawan_longitude_masuk' => $longitude_user,
                );
                $data_absensi = $this->Mabsensi->update($data, $where);
                if ($data_absensi) {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'success',
                      'message' => 'Berhasil diupdate.',
                    )
                  );
                } else {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'error',
                      'message' => 'Gagal disimpan.',
                    )
                  );
                }
              } else {
                $data = array(
                  'nik' => $nik,
                  'absensikaryawan_areakerja_masuk' => $areakerja_id,
                  'absensikaryawan_time_masuk' => $timeNow,
                  'absensikaryawan_date_masuk' => $dateNowNa,
                  'absensikaryawan_ket_masuk' => $keterangan,
                  'absensikaryawan_photo_masuk' => $fileName,
                  'absensikaryawan_jarak_masuk' => $distance,
                  'absensikaryawan_latitude_masuk' => $latitude_user,
                  'absensikaryawan_longitude_masuk' => $longitude_user,
                );
                $data_absensi = $this->Mabsensi->create($data);
                if ($data_absensi) {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'success',
                      'message' => 'Berhasil disimpan.',
                    )
                  );
                } else {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'error',
                      'message' => 'Gagal disimpan.',
                    )
                  );
                }
              }
            } elseif ($isKeluar == 1) {
              if ($isMasukSudah == 1) {
                $where = array('absensikaryawan_id' => $absensikaryawan_id,);
                $data = array(
                  'absensikaryawan_areakerja_keluar' => $areakerja_id,
                  'absensikaryawan_time_keluar' => $timeNow,
                  'absensikaryawan_date_keluar' => $dateNowNa,
                  'absensikaryawan_ket_keluar' => $keterangan,
                  'absensikaryawan_photo_keluar' => $fileName,
                  'absensikaryawan_jarak_keluar' => $distance,
                  'absensikaryawan_latitude_keluar' => $latitude_user,
                  'absensikaryawan_longitude_keluar' => $longitude_user,
                );
                $data_absensi = $this->Mabsensi->update($data, $where);
                if ($data_absensi) {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'success',
                      'message' => 'Berhasil diupdate.',
                    )
                  );
                } else {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'error',
                      'message' => 'Gagal disimpan.',
                    )
                  );
                }
              } else {
                $data = array(
                  'nik' => $nik,
                  'absensikaryawan_areakerja_keluar' => $areakerja_id,
                  'absensikaryawan_time_keluar' => $timeNow,
                  'absensikaryawan_date_keluar' => $dateNowNa,
                  'absensikaryawan_ket_keluar' => $keterangan,
                  'absensikaryawan_photo_keluar' => $fileName,
                  'absensikaryawan_jarak_keluar' => $distance,
                  'absensikaryawan_latitude_keluar' => $latitude_user,
                  'absensikaryawan_longitude_keluar' => $longitude_user,
                );
                $data_absensi = $this->Mabsensi->create($data);
                if ($data_absensi) {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'success',
                      'message' => 'Berhasil disimpan.',
                    )
                  );
                } else {
                  $arr_result = array(
                    'jne_ittsm' => array(
                      'status' => 'error',
                      'message' => 'Gagal disimpan.',
                    )
                  );
                }
              }
            }
          }
        }
      } else {
        $arr_result = array(
          'jne_ittsm' => array(
            'status' => 'error',
            'message' => 'Anda harus melakukan selfie terlebih dahulu.',
          )
        );
      }
    }


    print json_encode($arr_result);
  }


  public function export_post()
  {
    $nik  = $this->input->post('nik');
    $order_by            = 'absensikaryawan_id';
    $ordering            = "desc";
    $limit               = 10000;
    $page                = 0;

    if (isset($_POST['order_by'])) {
      $order_by = $this->input->post('order_by');
    }

    if (isset($_POST['ordering'])) {
      $ordering = $this->input->post('ordering');
    }

    if (isset($_POST['limit'])) {
      $limit = $this->input->post('limit');
    }

    if (isset($_POST['page'])) {
      $page = $this->input->post('page');
    }


    $option = array(
      'limit' => $limit,
      'page'  => $page,
      'order' => array(
        'order_by' => $order_by,
        'ordering' => $ordering,
      ),
    );
    $data_absensi = $this->Mabsensi->findHistory($nik, 'result', $option);

    $arr_result = array();
    if (empty($data_absensi)) {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'error',
          'message' => 'Absensi tidak ditemukan!.',
        )
      );
    } else {
      $arr_result = array(
        'jne_ittsm' => array(
          'status' => 'success',
          'message' => 'Data absensi ditemukan.',
          'data_absensi'     => $data_absensi,
        )
      );
    }

    print json_encode($arr_result);
  }
}