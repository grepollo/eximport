<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/REST_Controller.php');

class Payroll extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$this->load->model('Payroll_model', 'model');
        $this->load->modeL('Jobouter_model', 'jobouter');
        $this->load->modeL('Distribution_model', 'distribution');
	}
	
	public function index_get()
	{
        //$this->load->view('default/header');
        //$this->load->view('entry/index');
        //$this->load->view('default/footer');
	}
	
	public function read_get()
	{		
		$id = $this->get('payrollId');
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
		$id = $this->post('payrollId');
		$where = array('payrollId' => $id);
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
		$where = "payrollId = {$data['payrollId']}";
		$result = $this->model->delete($where);
		$this->response($result, 200);
	}
    public function list_get()
    {
        $sql = "SELECT DISTINCT payrollDate FROM payroll
                ORDER BY payrollDate DESC";
        $total = 0;
        $data = array(
            'data' => $this->model->select($sql),
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

    public function generate_post()
    {
        $params = $this->post();

        //check if date is not yet generated
        $filter = array('filters' => array(
           array(
               'operator'   => 'eq',
               'field'      => 'payrollDate',
               'value'      => $params['payrollDate']
           )
        ));

        $result = $this->model->read(null,$filter);
        //echo "<pre>";
        $success = 0;
        if(empty($result)) {
            //set default tax if not set
            if(!isset($params['payrollTax'])) {
                $params['payrollTax'] = 2;
            }
            //get all unpaid finished jobouter
            $sql = "SELECT jobouter.jobouterId, jobouterCode, distributionId, distributionQty,
                distributionFinishedQty, distributionStatus, partialQty, itemsJobouterPrice
                FROM jobouter
                LEFT JOIN distribution on jobouter.jobouterId = distribution.jobouterId
                LEFT JOIN jobentrydetail on distribution.jedId = jobentrydetail.jedId
                LEFT JOIN items on jobentrydetail.itemsId = items.itemsId
                WHERE distributionPayment != 'PAID' AND distributionStatus != 'Not Worked'";

            $result = $this->jobouter->select($sql);

            $tmp = array();

            if(!empty($result)) {
                foreach($result as $row) {
                    //check if the partialQty is not 0
                    if($row['partialQty'] > 0 ) {
                        $amount = $row['itemsJobouterPrice'] * ($row['distributionFinishedQty'] - $row['partialQty']);

                    } else {
                        $amount = $row['itemsJobouterPrice'] * $row['distributionFinishedQty'];
                    }

                    if(isset($tmp[$row['jobouterCode']])) {
                        $tmp[$row['jobouterCode']]['payrollAmount'] += $amount;
                    } else {
                        $tmp[$row['jobouterCode']] = $row;
                        $tmp[$row['jobouterCode']]['payrollAmount'] = $amount;
                    }
                    //update status to paid for this distribution id
                    if($row['distributionStatus'] == 'Finished') {
                        $arrData = array(
                            'distributionPayment' => 'PAID',
                            'distributionId'    => $row['distributionId'],
                            'partialQty'        => 0
                        );
                    } else {
                        $arrData = array(
                            'distributionPayment'   => 'PARTIAL',
                            'distributionId'        => $row['distributionId'],
                            'partialQty'            => $row['distributionFinishedQty']
                        );
                    }
                 
                    $where = array('distributionId' => $row['distributionId']);
                    $this->distribution->update($arrData, $where);
                }

                foreach($tmp as $t) {
                    $data = array(
                        'payrollJobouterId'	=> $t['jobouterId'],
                        'distributionId'    => $t['distributionId'],
                        'payrollDate'		=> $params['payrollDate'],
                        'payrollCashAdvance'=> 0,
                        'payrollAmount'		=> $tmp[$t['jobouterCode']]['payrollAmount'],
                        'payrollTax'		=> $params['payrollTax'],
                        'payrollNet'		=> $tmp[$t['jobouterCode']]['payrollAmount'] - ($tmp[$t['jobouterCode']]['payrollAmount'] * ($params['payrollTax'] / 100))
                    );
                    $this->model->create($data);
                }
                $success = 1;
                $msg = 'Payroll successfully generated.';
                //print_r($data); exit;
            } else {
                $msg = 'No more unpaid jobouter';
                //error no more unpaid jobouter
            }
        } else { // error
            $msg = "The date specified is already generated.";
        }
        $msg = array('msg' => $msg);

        if($success == 1) {
            $code = 200;
        } else {
            $code = 400;
        }
        $this->response($msg, $code);

    }

    public function excel_post()
    {
        $this->load->library('excel');
        $loc = FCPATH . 'assets/file/';

        $filters = $this->post('filter');
        //for kendo datasource parameters
        $result = $this->model->read(null,$filters, NULL, NULL, array(array('field' => 'jobouterCode', 'dir' => 'asc')));

        $this->excel->setActiveSheetIndex(0);

        if(!empty($result)) {
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Payroll');
            $this->excel->getActiveSheet()->setCellValue('A1', 'Payroll - ' . $result[0]['payrollDate']);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->mergeCells('A1:D1');
            $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //set cell A1 content with some text
            $i = 4;
            $this->excel->getActiveSheet()->setCellValue('A3', 'Jobouter');
            $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('B3', ' Amount ');
            $this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('C3', ' Tax (2%) ');
            $this->excel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('D3', 'Net Income');
            $this->excel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('E3', 'Signature');
            $this->excel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            foreach($result as $row) {
                $this->excel->getActiveSheet()->setCellValue('A'.$i, $row['jobouterFirstname'] . ' ' . $row['jobouterLastname']);

                $this->excel->getActiveSheet()
                    ->setCellValue('B'.$i, $row['payrollAmount'])
                    ->getStyle('B'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $this->excel->getActiveSheet()
                    ->setCellValue('C'.$i, $row['taxAmount'])
                    ->getStyle('C'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $this->excel->getActiveSheet()
                    ->setCellValue('D'.$i, $row['payrollNet'])
                    ->getStyle('D'.$i)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $i++;
            }
            $this->excel->getActiveSheet()->setCellValue('A'.$i, "Total");
            $this->excel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
            $this->excel->getActiveSheet()->setCellValue('B'.$i, "=SUM(B4:B". ($i-1) . ")");
            $this->excel->getActiveSheet()->getStyle('B'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
            $this->excel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C4:C". ($i-1) . ")");
            $this->excel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
            $this->excel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D4:D". ($i-1) . ")");
            $this->excel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()->setFormatCode('#,##0.00');

            //adjust column width to auto
            for($col = 'A'; $col !== 'G'; $col++) {
                $this->excel->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save($loc . 'payroll_'.date('Y-m-d').'.xls');

        $this->response(array('msg'=>'success'), 200);
    }

    public function download_get()
    {
        $this->load->helper('download');
        $loc = FCPATH . 'assets/file/';
        $filename = 'payroll_'.date('Y-m-d').'.xls';
        $fullPath = file_get_contents($loc . $filename);
        force_download($filename, $fullPath);
        exit;
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */