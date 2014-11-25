<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/REST_Controller.php');

class Distribution extends REST_Controller {


	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
        $this->load->model('Distribution_model', 'model');
        $this->load->model('Job_entry_detail_model', 'entryDetail');
        $this->load->model('Job_entry_model', 'jobentry');
	}
	
	public function index_get()
	{
        $this->load->view('default/header');
        $this->load->view('distribution/index');
        $this->load->view('default/footer');
	}

    public function read_get()
    {
        $id = $this->get('distributionId');
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
        $result = $this->model->read($id, $filters, $limit, $offset, $sort);
        if(!empty($result)) {
            for($i = 0; $i<count($result); $i++) {
                $diff =  $result[$i]['distributionFinishedQty'] - $result[$i]['partialQty'];
                if($diff > 0) {
                    $result[$i]['amount'] = $result[$i]['itemsJobouterPrice'] * $diff;
                } else {
                    $result[$i]['amount'] = $result[$i]['itemsJobouterPrice'] * $result[$i]['distributionFinishedQty'];
                }
                $result[$i]['taxAmount'] = $result[$i]['amount'] * .02;
                $result[$i]['netAmount'] = $result[$i]['amount'] - $result[$i]['taxAmount'];
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

    public function create_post()
    {
        $data = $this->post();
        $data['distributionDate'] = date('Y-m-d');
        $data['distributionDateUpdated'] = date('Y-m-d');
        $data['distributionBalancedQty'] = $data['distributionQty'] - $data['distributionFinishedQty'];
		$data['distributionStatus'] = 'Not Worked';
        //get the items order quantity
        $result = $this->entryDetail->read($data['jedId'],null);
        $orderedQty = $result[0]['quantity'];
        //get the total quanity of existing jobouter
        $filter = array(
            'filters' => array(
                array(
                    'operator' 	=> 'eq',
                    'field'		=> 'jedId',
                    'value'		=> $data['jedId']
                )
            )
        );
        $result = $this->model->read(null,$filter);
        $totalOrderedQty = 0;
        if(!empty($result)) {
            foreach($result as $row) {
                $totalOrderedQty += $row['distributionQty'];
            }
        }
        
        $remainingQty = $orderedQty - $totalOrderedQty;
        
        $success = 1;
        if($data['distributionQty'] <= $remainingQty) {
        	$result = $this->model->create($data);
        	$msg = 'Jobouter details successfully added.';
        	$msg = array('data' => $result, 'msg' => $msg, 'success' => $success);
        	$code = 200;
        } else {
        	$msg = 'Distribution quantity is exceed from the remaining job order quantity';
        	$success = 0;
        	$msg = array('data' => array($data), 'msg' => $msg, 'success' => $success);
        	$code = 400;
        }
        
        $this->response($msg, $code);
    }

    public function update_post()
    {
        $id = $this->post('distributionId');
        $data = $this->post();
        $data['distributionDateUpdated'] = date('Y-m-d');
        $data['distributionBalancedQty'] = $data['distributionQty'] - $data['distributionFinishedQty'];

        //get the items order quantity
        $result = $this->entryDetail->read($data['jedId'],null);
        $orderedQty = $result[0]['quantity'];
        //get the total quanity of existing jobouter
        $filter = array(
            'filters' => array(
                array(
                    'operator' 	=> 'eq',
                    'field'		=> 'jedId',
                    'value'		=> $data['jedId']
                ),
                array(
                    'operator' 	=> 'neq',
                    'field'		=> 'distributionId',
                    'value'		=> $data['distributionId']
                )
            )
        );
        $result = $this->model->read(null,$filter);
        $totalOrderedQty = 0;

        if(!empty($result)) {
            foreach($result as $row) {
                $totalOrderedQty += $row['distributionQty'];
            }
        }

        $remainingQty = $orderedQty - $totalOrderedQty;

        $success = 1;
        $where = array('distributionId' => $id);
        $status = 'Not Worked';
        if($data['distributionFinishedQty'] > $data['distributionQty']) {
            $msg = "Finished quantity cannot be exceed from job quantity.";
            $success = 0;
        }
        if($data['distributionQty'] > $remainingQty ) {
            $msg = 'Distribution quantity is exceed from the remaining job order quantity';
            $success = 0;
        }

        if($success) {
            if($data['distributionFinishedQty'] > 0 && $data['distributionFinishedQty'] < $data['distributionQty']) {
                $status = 'Started';
                $where2= array('jedId' => $data['jedId']);
                $arrData = array('status' => $status,'jedId' => $data['jedId']);
                $this->entryDetail->update($arrData, $where2);
            } elseif($data['distributionQty'] == $data['distributionFinishedQty']) {
                $status = 'Finished';
                //update the status of job entry details items
                if($orderedQty == ($totalOrderedQty + $data['distributionQty'])) {
                    $where2= array('jedId' => $data['jedId']);
                    $arrData = array('status' => $status, 'jedId' => $data['jedId']);
                    $this->entryDetail->update($arrData, $where2);
                }
                //set status to close if all items is finished
                $filter = array(
                    'filters' => array(
                        array(
                            'operator' 	=> 'eq',
                            'field'		=> 'jobEntryId',
                            'value'		=> $data['jobEntryId']
                        )
                    )
                );
                $result = $this->entryDetail->read(null,$filter);
                $itemCount = count($result);
                $checkCount = 0;
                foreach($result as $row) {
                    if($row['status'] == 'Finished') {
                        $checkCount++;
                    }
                }

                if($itemCount == $checkCount) {
                    //update status to close
                    $where3 = array('jobEntryId' => $data['jobEntryId']);
                    $arrData = array('jobEntryStatus' => 'CLOSED');
                    $this->jobentry->update($arrData, $where3);
                }

            } else {

            }
            $data['distributionStatus'] = $status;
            $result = $this->model->update($data, $where);

            $msg = 'Jobouter details successfully updated.';

            $msg = array('data' => $result, 'msg' => $msg, 'success' => $success);
            $code = 200;
        } else {
            $msg = array('data'=> array($data),'msg' => $msg, 'success' => $success);
            $code = 400;
        }
        $this->response($msg, $code);
    }

    public function delete_post()
    {
        $data = $this->post();
        $where = "distributionId = {$data['distributionId']}";
        $result = $this->model->delete($where);
        $this->response($result, 200);
    }

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */