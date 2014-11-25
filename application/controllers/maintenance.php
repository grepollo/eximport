<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
	}
	
	public function index()
	{
        $this->load->view('default/header');
        $this->load->view('maintenance/index');
        $this->load->view('default/footer');
	}

    public function buyer()
    {
        $this->load->view('maintenance/buyer');
    }

    public function item()
    {
        $this->load->view('maintenance/item');
    }

    public function job_outer()
    {
        $this->load->view('maintenance/job_outer');
    }

    public function unit()
    {
        $this->load->view('maintenance/unit');
    }

    public function inv_category()
    {
        $this->load->view('maintenance/inv_category');
    }

    public function inv_type()
    {
        $this->load->view('maintenance/inv_type');
    }

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */