<?php


function is_logged_in()
{
    $ci = get_instance();
    if (!$ci->session->userdata('nik')) {

        $this->session->set_flashdata('session', 'Sessi anda sudah berakhir, silahkan masuk kembali');
        redirect('login');
    }
}
