<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/REST_Controller.php');

class Invoice extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$this->load->model('Invoice_model', 'model');
	}
	
	public function index_get()
	{
        $this->load->view('default/header');
        $this->load->view('invoice/index');
        $this->load->view('default/footer');
	}
	
	public function read_get()
	{		
		$id = $this->get('invoiceId');
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
		$id = $this->post('invoiceId');
		$where = array('invoiceId' => $id);
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
		$where = "invoiceId = {$data['invoiceId']}";
		$result = $this->model->delete($where);
		$this->response($result, 200);
	}

    public function excel_post()
    {
        $this->load->library('excel');
        $loc = FCPATH . 'assets/file/';

        $filters = $this->post('filter');
        //for kendo datasource parameters
        $result = $this->model->read(null,$filters);

        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Invoices');
        $this->excel->getActiveSheet()->setCellValue('A1', 'INVOICES');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->mergeCells('A1:H1');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //set cell A1 content with some text
        if(!empty($result)) {
            $i = 4;
            $this->excel->getActiveSheet()->setCellValue('A3', 'Date');
            $this->excel->getActiveSheet()->setCellValue('B3', 'Invoice No.');
            $this->excel->getActiveSheet()->setCellValue('C3', 'Type');
            $this->excel->getActiveSheet()->setCellValue('D3', 'Category');
            $this->excel->getActiveSheet()->setCellValue('E3', 'Paid To Name');
            $this->excel->getActiveSheet()->setCellValue('F3', 'Description');
            $this->excel->getActiveSheet()->setCellValue('G3', 'Check No.');
            $this->excel->getActiveSheet()->setCellValue('H3', 'Amount');

            foreach($result as $row) {
                $this->excel->getActiveSheet()->setCellValue('A'.$i, $row['date']);
                $this->excel->getActiveSheet()->setCellValue('B'.$i, $row['invoiceId']);
                $this->excel->getActiveSheet()->setCellValue('C'.$i, $row['type']);
                $this->excel->getActiveSheet()->setCellValue('D'.$i, $row['category']);
                $this->excel->getActiveSheet()->setCellValue('E'.$i, $row['paidToName']);
                $this->excel->getActiveSheet()->setCellValue('F'.$i, $row['description']);
                $this->excel->getActiveSheet()->setCellValue('G'.$i, $row['checkNumber']);
                $this->excel->getActiveSheet()->setCellValue('H'.$i, $row['amount']);
                $i++;
            }
            $this->excel->getActiveSheet()->setCellValue('F'.$i, "Total");
            $this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
            $this->excel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H4:H". ($i-1) . ")");
            //adjust column width to auto
            for($col = 'A'; $col !== 'G'; $col++) {
                $this->excel->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save($loc . 'invoice_'.date('Y-m-d').'.xls');

        $this->response(array('msg'=>'success'), 200);
    }

    public function download_get()
    {
        $this->load->helper('download');
        $loc = FCPATH . 'assets/file/';
        $filename = 'invoice_'.date('Y-m-d').'.xls';
        $fullPath = file_get_contents($loc . $filename);
        force_download($filename, $fullPath);

        exit;
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */