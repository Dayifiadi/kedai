<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
| https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
| $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
| $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
| $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|   my-controller/my-method -> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;


//get FOR DEV
$route['ambil_header'] = "api/Authorization/getParamsHeader";
$route['send_all'] = "api/Notification/send_notification";
$route['not_topic'] = "api/Notification/send_notification_topic";

//checkVersi
$route['appvea/check?(:any)'] = "api/VersionAndroid/check";

//post user
$route['apppeng/log?(:any)'] = "api/PenggunaAwal/login";
$route['apppeng/findLog?(:any)'] = "api/Pengguna/findLogin";
$route['apppeng/findNew?(:any)'] = "api/Pengguna/findNew";
$route['apppeng/findApplication?(:any)'] = "api/Pengguna/findApplication";
$route['apppeng/findUserTypeAll?(:any)'] = "api/Pengguna/findUserTypeAll";
$route['apppeng/updatePwd?(:any)'] = "api/Pengguna/update_password";
$route['apppeng/setToken?(:any)'] = "api/Pengguna/setToken";
$route['apppeng/notLogin?(:any)'] = "api/Pengguna/reset_pengajuan";
$route['apppeng/resetAll?(:any)'] = "api/Pengguna/findAllReset";
$route['apppeng/approveReset?(:any)'] = "api/Pengguna/approveReset";
$route['apppeng/findAllDakar?(:any)'] = "api/Pengguna/findAllDakar";


//post absensi
$route['appabs/findNow?(:any)'] = "api/Absensi/find_now";
$route['appabs/findYes?(:any)'] = "api/Absensi/find_yes";
$route['appabs/getTime?(:any)'] = "api/Absensi/getTime";
$route['appabs/getDate?(:any)'] = "api/Absensi/getDate";
$route['appabs/shifting?(:any)'] = "api/Absensi/find_shifting";
$route['appabs/create_absensi?(:any)'] = "api/Absensi/create_absensi";
$route['appabs/crt_abs_new?(:any)'] = "api/Absensi/create_absensi_new";
$route['appabs/crt_shift?(:any)'] = "api/Absensi/crt_shift";
$route['appabs/history?(:any)'] = "api/Absensi/find_history";
$route['appabs/jadwalALL?(:any)'] = "api/Absensi/jadwalALL";
$route['appabs/export?(:any)'] = "api/Absensi/Export";
$route['appabs/setUpdate?(:any)'] = "api/Absensi/setUpdate";

//post Aktivitas
$route['appak/crt_ak?(:any)'] = "api/Aktivitas/create";
$route['appak/history?(:any)'] = "api/Aktivitas/find_history";
$route['appak/hs?(:any)'] = "api/Aktivitas/find_history_new";
$route['appak/aktivitas_kat?(:any)'] = "api/Aktivitas/find_kat";
$route['appak/id_aktivitas?(:any)'] = "api/Aktivitas/id_aktivitas";
$route['appak/repair_kat?(:any)'] = "api/Aktivitas/find_kat_repair";
$route['appak/repair_yes?(:any)'] = "api/Aktivitas/find_kat_repair_yes";
$route['appak/by_type?(:any)'] = "api/Aktivitas/find_kat_repair_type";
$route['appak/fuelIdType?(:any)'] = "api/Aktivitas/find_kat_repair_type_by_id";
$route['appak/daily?(:any)'] = "api/Aktivitas/find_daily";
$route['appak/bbm_armada?(:any)'] = "api/Aktivitas/find_bbm";

$route['appak/repair_cat?(:any)'] = "api/Aktivitas/find_kat_repair_yes_detail";
$route['appak/foto?(:any)'] = "api/Aktivitas/simpan_foto";
$route['appak/delete?(:any)'] = "api/Aktivitas/delete_foto";


//post loan
$route['applo/findlo?(:any)'] = "api/Loan/findlo";
$route['applo/addloan?(:any)'] = "api/Loan/addloan";
$route['applo/finddetail?(:any)'] = "api/Loan/finddetail";
$route['applo/updateloan?(:any)'] = "api/Loan/updateloan";
$route['applo/findhistory?(:any)'] = "api/Loan/findhistory";
$route['applo/findAllLoan?(:any)'] = "api/Loan/findAllLoan";

//post egd
$route['appegd/find_all?(:any)'] = "api/EGD/find_history";
$route['appegd/create?(:any)'] = "api/EGD/create";

//post bbm
$route['appBbm/findNop?(:any)'] = "api/BBM/find_nopol";
$route['appBbm/newFindNop?(:any)'] = "api/BBM/find_nopol_new";
$route['appBbm/fA?(:any)'] = "api/BBM/find_history_new";
$route['appBbm/crt?(:any)'] = "api/BBM/create";
$route['appBbm/str?(:any)'] = "api/BBM/start";
$route['appBbm/detail?(:any)'] = "api/BBM/find_detail";
$route['appBbm/cek_stp?(:any)'] = "api/BBM/find_stop";
$route['appBbm/new_stop?(:any)'] = "api/BBM/stp_new";
$route['appBbm/get_stop?(:any)'] = "api/BBM/stop";
$route['appBbm/update?(:any)'] = "api/BBM/update";
$route['appBbm/upd_new?(:any)'] = "api/BBM/update_new";
$route['appBbm/km_uptd?(:any)'] = "api/BBM/km_uptd";
$route['StrukBBM/struk?(:any)'] = "api/BBM/strk";
$route['appBbm/delete?(:any)'] = "api/BBM/delete";
$route['appBbm/newnop_det?(:any)'] = "api/BBM/findNewNopDet";
$route['appBbm/nop_det?(:any)'] = "api/BBM/findNopDet";
$route['appBbm/newDetail?(:any)'] = "api/BBM/detail_new";
$route['appBbm/bbmNA?(:any)'] = "api/BBM/get_bbm";


//post osp
$route['osp/findOsp?(:any)'] = "api/OSP/find_osp";
$route['osp/create?(:any)'] = "api/OSP/create";
$route['osp/buat?(:any)'] = "api/OSP/buat";
$route['osp/history?(:any)'] = "api/OSP/find_history_new";
$route['osp/dataDetail?(:any)'] = "api/OSP/dataDetail";
$route['osp/update?(:any)'] = "api/OSP/update";
$route['osp/edit?(:any)'] = "api/OSP/edit";
$route['osp/export?(:any)'] = "api/OSP/export";

//post fda
$route['appfda/category?(:any)'] = "api/FDA/find_category";
$route['appfda/det_cat?(:any)'] = "api/FDA/find_category_detail";
$route['appfda/req_fda?(:any)'] = "api/FDA/req_fda";
$route['appfda/history?(:any)'] = "api/FDA/find_history_new";
$route['appfda/export?(:any)'] = "api/FDA/export";
$route['appfda/batalkan?(:any)'] = "api/FDA/batalkan";
$route['appfda/check_fda?(:any)'] = "api/FDA/check_fda";

//post log
$route['applog/create?(:any)'] = "api/Log/create";
