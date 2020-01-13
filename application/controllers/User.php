<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function index()
    {
        $data['title'] = 'Profile';
        $data['user'] = $this->db->get_where('user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $this->load->view('templates/_header', $data);
        $this->load->view('templates/_sidebar', $data);
        $this->load->view('templates/_topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/_footer');
    }
}
