<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Security extends CI_Controller {

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
        $this->load->view('security/index');
        $this->load->view('default/footer');
	}

    public function users()
    {
        $this->load->view('security/users');
    }

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */