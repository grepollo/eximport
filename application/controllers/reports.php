<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {

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
        $this->load->view('reports/index');
        $this->load->view('default/footer');
	}

    public function unpaid()
    {
        $this->load->view('reports/unpaid');
    }

    public function payroll()
    {
        $this->load->view('reports/payroll');
    }
    
    public function deliveries()
    {
    	$this->load->view('reports/deliveries');
    }
    
    public function unpaid_excel()
    {
    	ini_set('memory_limit', '-1');
        $this->load->model('Distribution_model', 'model');
        $this->load->library('excel');
        $loc = FCPATH . 'assets/file/';

        $sql = "SELECT jobouter.*, partialQty, distributionId, distributionFinishedQty FROM jobouter
                LEFT JOIN distribution on jobouter.jobouterId = distribution.jobouterId
                WHERE distributionPayment != 'PAID' AND distributionStatus != 'Not Worked'
                GROUP BY jobouter.jobouterId";
        $total = 0;
        //check for partial payment
        $result = $this->model->select($sql);
        //echo count($result); exit;
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        foreach($result as $i => $row) {			
            if($i != 0) {
                $this->excel->createSheet(NULL, $i);
            }
            $this->excel->setActiveSheetIndex($i);
            $this->excel->getActiveSheet()->setTitle($row['jobouterCode']);
            
            $this->excel->getActiveSheet()->setCellValue('A1', $row['jobouterFirstname']);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->setCellValue('B1', $row['jobouterLastname']);
            $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->setCellValue('E1', 'Date Generated: ' . date('Y-m-d'));
            $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->mergeCells('E1:G1');
            
            $this->excel->getActiveSheet()->setCellValue('A2', 'Job Order No.');
            $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
           
            $this->excel->getActiveSheet()->setCellValue('B2', ' PO Handler ');
            $this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('C2', ' Item ');
            $this->excel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('D2', ' Unit ');
            $this->excel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $this->excel->getActiveSheet()->setCellValue('E2', 'Finished Qty');
            $this->excel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
           
            $this->excel->getActiveSheet()->setCellValue('F2', 'Labor Cost');
            $this->excel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('G2', 'Amount');
            $this->excel->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->setCellValue('H2', 'Net Income (- 2% Tax)');
            $this->excel->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //adjust column width to auto

            //get details
            $filter = array(
                'filters' => array(
                    array(
                        'operator'  => 'eq',
                        'field'     => 'jobouterId',
                        'value'     => $row['jobouterId']
                    ),
                    array(
                        'operator'  => 'neq',
                        'field'     => 'distributionPayment',
                        'value'     => 'PAID'
                    ),
                    array(
                        'operator'  => 'neq',
                        'field'     => 'distributionStatus',
                        'value'     => 'Not Worked'
                    )
                )
            );
            $j = 3;
            $details = $this->model->read(NULL, $filter);
            foreach($details as $d) {
                $diff =  $d['distributionFinishedQty'] - $d['partialQty'];
                if($diff > 0) {
                    $amount = $d['itemsJobouterPrice'] * $diff;
                } else {
                    $amount = $d['itemsJobouterPrice'] * $d['distributionFinishedQty'];
                }

                $taxAmount = $amount * .02;
                $netAmount = $amount - $taxAmount;

                $this->excel->getActiveSheet()->setCellValue('A'.$j, $d['jobEntryId']);               
                $this->excel->getActiveSheet()->setCellValue('B'.$j, $d['handler']);
                $this->excel->getActiveSheet()->setCellValue('C'.$j, $d['itemsCode']);
                $this->excel->getActiveSheet()->setCellValue('D'.$j, $d['unitCode']);                
                $this->excel->getActiveSheet()->setCellValue('E'.$j, $d['distributionFinishedQty']);                
                $this->excel->getActiveSheet()->setCellValue('F'.$j, $d['itemsJobouterPrice'])
                    ->getStyle('F'.$j)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $this->excel->getActiveSheet()->setCellValue('G'.$j, $amount)
                    ->getStyle('G'.$j)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
                $this->excel->getActiveSheet()->setCellValue('H'.$j, $netAmount)
                    ->getStyle('H'.$j)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $j++;
            }

            for($col = 'A'; $col !== 'I'; $col++) {
                $this->excel->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }

            $this->excel->getActiveSheet()->setCellValue('F'.$j, "Total");
            $this->excel->getActiveSheet()->getStyle('F'.$j)->getFont()->setBold(true);
            $this->excel->getActiveSheet()->setCellValue('G'.$j, "=SUM(G2:G". ($j-1) . ")");
            $this->excel->getActiveSheet()->getStyle('G'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
            $this->excel->getActiveSheet()->setCellValue('H'.$j, "=SUM(H2:H". ($j-1) . ")");
            $this->excel->getActiveSheet()->getStyle('H'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
           

        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save($loc . 'unpaid_'.date('Y-m-d').'.xls');
        echo json_encode(array('msg'=>'success')); exit;
        
    }
    
    public function download()
    {
    	$this->load->helper('download');
    	$loc = FCPATH . 'assets/file/';    	
    	$filename = 'unpaid_'.date('Y-m-d').'.xls';
    	$fullPath = file_get_contents($loc . $filename);
    	force_download($filename, $fullPath);
    	exit;
    }

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */