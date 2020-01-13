<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sekolah extends CI_Controller
{

    public function __construct()

    {
        parent::__construct(); //untuk memanggil method construct di CI
        $this->load->library('form_validation'); // untuk validasi
    }

    public function index()
    {
        //untuk validasi
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email', [
            'required' => 'Masukan Email!',
            'valid_email' => 'Email Tidak Valid!'
        ]);

        $this->form_validation->set_rules('password', 'Password', 'required|trim', [
            'required' => 'Masukan Password!'
        ]);

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login'; //untuk judul
            $this->load->view('templates/header', $data); // template header
            $this->load->view('sekolah/login');
            $this->load->view('templates/footer'); // template footer

        } else {
            // validasi sukses kita lanjutkan ke login dengan membuat file baru biar tidak panjang dan dibuat private supaya tidak di ases di url
            $this->login();
        }
    }


    private function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // jika user ada
        if ($user) {
            // jika user aktif
            if ($user['is_active'] == 1) {
                // cek paswwordnya
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    if ($user['role_id'] ==  1) {
                        redirect('admin');
                    } else {
                        redirect('user');
                    }
                    //jika password salah
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Password salah! </div>');
                    redirect('sekolah');
                }
                // user belum aktif
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Email belum aktif! </div>');
                redirect('sekolah');
            }
            // user tidak ada
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Email belum terdaftar! </div>');
            redirect('sekolah');
        }
    }

    public function regis()
    {
        //validasi daftar akun bila salah
        //memberikan rules
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim', [
            'required' => 'Masukan nama!'
        ]); // palidasi nama required sebagai pemberi tanda error

        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'Email sudah digunakan!',
            'required' => 'Masukan email!'
        ]); // palidasi email

        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'required' => 'Masukan password!',
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password terlalu pendek!'
        ]); //validasi password

        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Registrasi';
            $this->load->view('templates/header', $data);
            $this->load->view('sekolah/regis');
            $this->load->view('templates/footer');
            // bila di atas benar maka akan di teruskan seperti di bawah ini
        } else {
            $data = [ // sesuaikan data yang ada di database
                'nama' => htmlspecialchars($this->input->post('nama', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 1,
                'date_created' => time()
            ];

            $this->db->insert('user', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Selamat! akun anda sudah siap. Silahkan login </div>');
            redirect('sekolah'); // meneruskan ke halaman login untuk login
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Anda berhasil keluar</div>');
        redirect('sekolah');
    }
}
