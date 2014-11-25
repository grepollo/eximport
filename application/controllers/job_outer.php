<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/REST_Controller.php');

class Job_outer extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		//if (!$this->ion_auth->logged_in()) {
		//	redirect('auth/login', 'refresh');
		//}
		$this->load->model('Jobouter_model', 'model');
	}
	
	public function index_get()
	{
        //$this->load->view('default/header');
        //$this->load->view('entry/index');
        //$this->load->view('default/footer');
	}
	
	public function read_get()
	{		
		$id = $this->get('jobouterId');
		$filters = $this->get('filter');	
		//for kendo datasource parameters
		$limit = $this->get('pageSize');
		$offset = $this->get('skip');
		$page = $this->get('page');		
		$sort = $this->get('sort');	
		$total = 0;
		if($limit>0) {
			//get the total data base on filters
			$total = sizeof($this->model->read($id, $filters));
		}			
		$data = array(
			'data' => $this->model->read($id, $filters, $limit, $offset, $sort),
			'total' => $total
		);
		
		if($this->get('format') == 'json') {
			$message = $data;
			$this->response($message, 200);
		} else {
			$message = array('data' => $data);
			$this->response($message, 200);
		}				
	}
	
	public function create_post() 
	{
		$data = $this->post();
		$result = $this->model->create($data);
		if(!isset($result['msg'])) {
			$msg = array('data' => $result);
			$code = 200;
		} else {
			$msg = 'Error: ' . $result['msg'];
			$code = 400;
		}
		$this->response($msg, $code);
	}
	
	public function update_post() 
	{
		$id = $this->post('jobouterId');
		$where = array('jobouterId' => $id);
		$data = $this->post();
		
		$result = $this->model->update($data, $where);
		if(!isset($result['msg'])) {
			$msg = array('data' => $result);
			$code = 200;
		} else {
			$msg = 'Error: ' . $result['msg'];
			$code = 400;
		}
		$this->response($msg, $code);
	}
	
	public function delete_post() 
	{
		$data = $this->post();
		$where = "jobouterId = {$data['jobouterId']}";
		$result = $this->model->delete($where);
		$this->response($result, 200);
	}

    public function unpaid_get()
    {
        $sql = "SELECT jobouter.*, partialQty, distributionId, distributionFinishedQty FROM jobouter
                LEFT JOIN distribution on jobouter.jobouterId = distribution.jobouterId
                WHERE distributionPayment != 'PAID' AND distributionStatus != 'Not Worked'
                GROUP BY jobouter.jobouterId";
        $total = 0;
        //check for partial payment
        $result = $this->model->select($sql);

        foreach($result as $key => $row) {
            $check = $row['distributionFinishedQty'] - $row['partialQty'];
            if($check == 0 ) { //unpaid
                unset($result[$key]);
            }
        }
        $data = array(
            'data' => $result,
            'total' => $total
        );

        if($this->get('format') == 'json') {
            $message = $data;
            $this->response($message, 200);
        } else {
            $message = array('data' => $data);
            $this->response($message, 200);
        }
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */