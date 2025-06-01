<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        if($this->session->has_userdata('authenticated'))
        {
            if($this->session->userdata('authenticated') == '3')
            {
                // echo "u are admin";
            }
        }
        else
        {
            $this->session->set_flashdata('status', 'Inicia sesión primero');
            redirect(base_url('login'));
        }
    }

    public function check_isAdmin()
    {
        if($this->session->has_userdata('authenticated'))
        {
            if($this->session->userdata('authenticated') !='3')
            {
                $this->session->set_flashdata('status', 'Acceso Denegado');
                redirect(base_url('403'));
            }
        }
        else
        {
            $this->session->set_flashdata('status', 'Inicia sesión primero');
            redirect(base_url('login'));
        }
    }

    public function check_isIngresante()
    {
        if($this->session->has_userdata('authenticated'))
        {
            if($this->session->userdata('authenticated') !='2' && $this->session->userdata('authenticated') !='3')
            {
                $this->session->set_flashdata('status', 'Acceso Denegado');
                redirect(base_url('403'));
            }
        }
        else
        {
            $this->session->set_flashdata('status', 'Inicia sesión primero');
            redirect(base_url('login'));
        }
    }

    public function check_isSolicitante()
    {
        if($this->session->has_userdata('authenticated'))
        {
            if($this->session->userdata('authenticated') !='1' && $this->session->userdata('authenticated') !='3')
            {
                $this->session->set_flashdata('status', 'Acceso Denegado');
                redirect(base_url('403'));
            }
        }
        else
        {
            $this->session->set_flashdata('status', 'Inicia sesión primero');
            redirect(base_url('login'));
        }
    }
}

?>