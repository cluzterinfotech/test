<?php 

namespace Payment\Controller;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Payment\Model\Company;
use Payment\Model\DateRange;
use Payment\Model\Paysheet;
use Payment\Model\Person; 
use Application\Form\SubmitButonForm; 
use Application\Form\MonthYear; 
use Payment\Form\SelectLeaveAllowanceEmployeeForm;  
use Application\Model\SelectLeaveAllowanceEmployeeGrid;

class LeaveallowanceController extends AbstractActionController
{
	public function indexAction()
	{   
		return new ViewModel(); 
	} 
	
	public function listAction() { }
	
	public function ajaxlistAction() {
		$grid = $this->getGrid();
		$grid->setAdapter($this->getDbAdapter())
			 ->setSource($this->getLeaveAllowanceService()->selectEmployeeLa())
			 ->setParamAdapter($this->getRequest()->getPost());
		return $this->htmlResponse($grid->render()); 
	}
	
	public function htmlResponse($html) {
		$response = $this->getResponse();
		$response->setStatusCode(200);
		$response->setContent($html);
		return $response;  
	}
	 
	public function selectAction()
	{ 
		$form = new SelectLeaveAllowanceEmployeeForm(); 
		$company = $this->getServiceLocator()->get('company');
		$form->get('employeeNumberLeaveAllowance')
		     ->setOptions(array('value_options' => 
				$this->notTakenLAEmployeeList($company)))
		//->setAttribute('readOnly', true)
		; 
		return array('form' => $form); 
		//exit; 
	} 
	
	public function calculateAction()
	{   
		$form = $this->getForm(); 
		$prg = $this->prg('/leaveallowance/calculate',true); 
		if ($prg instanceof Response ) { 
			return $prg; 
		} elseif ($prg === false) { 
			return array ('form' => $form); 
		} 
		$company = $this->getCompanyService(); 
		$form->setData($prg); 
		$dateRange = $this->getDateService();
		$leaveAllowance = $this->getLeaveAllowanceService();     
		$routeInfo = $this->getRouteInfo();   
			
		$leaveAllowance->calculate($company,$dateRange,$routeInfo);   
		$this->flashMessenger()->setNamespace('success') 
             ->addMessage('Leave Allowance Calculated Successfully'); 
		$this->redirect()->toRoute('leaveallowance',array( 
				'action' => 'calculate' 
		));  
		return array(  
			'form' => $form, 
			$prg 
		);  
	}  
	
	private function getForm() {
        $form = new SubmitButonForm();
        $form->get('submit')->setValue('Calculate Leave Allowance');
		return $form;
	}
	
	public function closeAction() {
				
		$form = $this->getForm();
		$form->get('submit')->setValue('Close Leave Allowance');
		$prg = $this->prg('/leaveallowance/close', true);
		if ($prg instanceof Response ) {
			return $prg; 
		} elseif ($prg === false) {
			return array ('form' => $form); 
		}   
		$company = $this->getCompanyService(); 
		$form->setData($prg);  
		$dateRange = $this->getDateService(); 
		$laService = $this->getLeaveAllowanceService();  
		    
		$isAlreadyClosed = 0;// $laService->isLaClosed($company,$dateRange);
		if($isAlreadyClosed) {
			$this->flashMessenger()->setNamespace('info')
			     ->addMessage('Leave Allowance already closed for all batches'); 
		} else {  
			// @todo 
			$routeInfo = $this->getRouteInfo(); 
			// $laService = $this->getLeaveAllowanceService(); 
			$laService->close($company,$dateRange,$routeInfo); 
			$this->flashMessenger()->setNamespace('success')
		         ->addMessage('Leave Allowance Closed Successfully'); 
		}  
		$this->redirect()->toRoute('leaveallowance',array( 
				'action' => 'close'
		)); 
		return array( 
				'form' => $form, 
				$prg 
		); 
	} 
	
	public function saveemployeetolistAction() {
		$formValues = $this->params()->fromPost('formVal');
		
		$laService = $this->getLeaveAllowanceService();
		$company = $this->getServiceLocator()->get('company');
		$formValues['companyId'] = $company->getId();
		//\Zend\Debug\Debug::dump($formValues);
		//exit;
		$laService->saveEmployeeLeaveAllowance($formValues);
		//var_dump($formValues);
		exit;
	}
	
	public function removeAction()
	{
		// @todo
		$id = (int) $this->params()->fromRoute('id',0);
		$laService = $this->getLeaveAllowanceService(); 
		$laService->removeEmployeeLeaveAllowance($id);
		// echo json_encode($id); 
		exit;
	}
	
	public function reportAction() {
		
		$form = $this->getReportForm(); 
		$request = $this->getRequest();
		if ($request->isPost()) { 
			$form->setData($request->getPost());
			if ($form->isValid()) {
				return $this->redirect()->toRoute('leaveallowance');
			}
		}
		return array(
				'form' => $form,
		);
	}
	
	/*private function getLeaveAllowanceService() {
		return $this->getServiceLocator()->get('Paysheet'); 
	}*/
	
	private function getCompanyService() {
	   return $this->getServiceLocator()->get('company'); 
    }
    
    private function getDateService() {
        return $this->getServiceLocator()->get('dateRange');
    } 
	
	public function viewreportAction() { 
                                                                                
        $viewmodel = new ViewModel();
        $viewmodel->setTerminal(1);       
        $request = $this->getRequest();
        $output = " "; 
        
        if($request->isPost()) {
            
            $values = $request->getPost(); 
            $month = $values['month']; 
            $year = $values['year']; 
            $type = 1;
            
            $param = array('month' => $month,'year' => $year);
            
            //$results = $this->getLeaveAllowanceService()->getPaysheetReport($param); 
            $output = $this->getLeaveAllowanceService()->getLeaveAllowanceReport($param); 
           // \Zend\Debug\Debug::dump($output);
            /* if($results) {
            	foreach($results as $result) {
            		$output .= $result['employeeNumber']."<br/>";
            	}
            } else {
            	$output = "Sorry! no results found";
            }  */
            
            /* if ($type == 1) {
                $output = $this->getLeaveAllowanceService()->getPaysheetReport($param); 
            } else if ($type == 2) {
                $output = $this->getReportTable()->summary($values);
            } else if ($type == 3) {
                $output = $this->getReportTable()->byorder($values);
            } else if ($type == 4) {
                $output = $this->getReportTable()->bystatus($values);
            } else if ($type == 5) {
                $output = $this->getReportTable()->report($values,'2'); 
            } else if ($type == 6) {
                $output = $this->getReportTable()->duplicateobr($values); 
            } */
            
        }
                   
        $viewmodel->setVariables(array(
            'report'            => $output,
        	//'name'              => array('Employee Name' => 'employeeNamex'),
         	//'allowance'         => $this->getPaysheetAllowanceArray(),
        	//'deduction'         => $this->getPaysheetDeductionArray(),
        	//'companyDeduction'  => $this->companyDeductionArray() 
        ));
        return $viewmodel; 
	} 
	
	public function successAction() {
		return new ViewModel();
	}
	
	public function policyAction() {
		
	}
	
	public function manualAction() {
	
	}
	
	private function getEmployeeService() {
		return $this->getServiceLocator()->get('employeeService');
	}
	
	private function getLeaveAllowanceService() {
		return $this->getServiceLocator()->get('leaveAllowanceService');
	}
	
	private function notTakenLAEmployeeList(Company $company) {
		return $this->getEmployeeService()->notTakenLAEmployeeList($company);
	}
	
	public function getReportForm() {
		$form = new MonthYear(); 
		$form->get('submit')->setValue('View Paysheet Report');
		return $form;
	}
    
	/*private function getLeaveAllowanceService() {
		return $this->getServiceLocator()->get('paysheet');
	}*/  
	
	private function getPaysheetAllowanceArray() {
		return array(
				
				// 'Employee Number'  => 'employeeNumber', 
				'Initial Salary'   => 'Initial',
				'COLA'             => 'COLA',
				'Cashier'          => 'Cashier',
				'Airport'          => 'Airport',
				//'Breakfast'        => 'Breakfast',
				'Fitter'           => 'Fitter',
				'Hardship'         => 'Hardship',
				'Housing'          => 'Housing',
				'Overtime'         => 'Overtime',
				'Meal'             => 'Meal',
				'NatureofWork'     => 'NatureofWork', 
				'Representative'   => 'Representative',
				'Shift'            => 'Shift', 
				'Transportation'   => 'Transportation',
				'OtherAllowance'   => 'OtherAllowance',
				//'OtMeal'           => 'OtMeal',
				'SpecialAllowance' => 'SpecialAllowance',
				'President'        => 'President', 
				/*
				'Social Insurance' => 'SocialInsurance',
				'IncomeTax'        => 'IncomeTax', 
				'Zakat'            => 'Zakat', 
				'Provident Fund'   => 'ProvidentFund',
				'Zamala'                => 'Zamala',
				'Union Share'           => 'UnionShare',
				'Telephone'           => 'Telephone',
				//'Punishment'          => 'Punishment',
				'OtherDeduction'      => 'OtherDeduction',
				'KhartoumUnion'         => 'KhartoumUnion',
				'Cooperation'           => 'Cooperation',
				'AdvanceSalary'  => 'AdvanceSalary',
				//'Absenteeism'         => 'Absenteeism',
				'PersonalLoan'          => 'PersonalLoan', 
				'Company SI'            => 'SocialInsuranceCompany',
				'Company PF'            => 'ProvidentFundCompany',
				*/
		);
	}
	
	public function getPaysheetDeductionArray() {
		return array( 
				'Social Insurance' => 'SocialInsurance',
				'IncomeTax'        => 'IncomeTax',
				'Zakat'            => 'Zakat',
				'Provident Fund'   => 'ProvidentFund', 
				'Zamala'           => 'Zamala',
				'Union Share'      => 'UnionShare',
				'Telephone'        => 'Telephone',
				//'Punishment'     => 'Punishment',
				'OtherDeduction'   => 'OtherDeduction',
				'KhartoumUnion'    => 'KhartoumUnion',
				'Cooperation'      => 'Cooperation',
				'AdvanceSalary'    => 'AdvanceSalary',
				'Advance Housing'  => 'AdvanceHousing',
				//'Absenteeism'    => 'Absenteeism',
				'PersonalLoan'     => 'PersonalLoan',
		); 
	}
	
	public function companyDeductionArray() {
		return array( 
			'Company SI'  => 'SocialInsuranceCompany',
			'Company PF'  => 'ProvidentFundCompany',
		); 
	}
	
	private function getRouteInfo() {
		return array(
			'controller' => $this->getEvent()
			                     ->getRouteMatch()
			                     ->getParam('controller','index'),
			'action'     => $this->getEvent()
			                     ->getRouteMatch()
			                     ->getParam('action','index'),
		);
	}
	
	private function getGrid() {
		return new SelectLeaveAllowanceEmployeeGrid(); 
	} 
	
	private function getDbAdapter() {
		return $this->getServiceLocator()->get('sqlServerAdapter');
	}
	
}