<?php

namespace Employee\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Employee\Model\BankInfo;

/**
 * Description of BankInfoFieldset
 *
 * @author Wol
 */
class BankInfoFieldset extends Fieldset implements InputFilterProviderInterface {
	public function __construct() {
		parent::__construct ( 'bankInfo' );
		
		$this->setHydrator ( new ClassMethods () )->setObject ( new BankInfo () );
		
		$this->add ( array (
				'name' => 'id',
				'type' => 'hidden' 
		) );
		
		$this->add ( array (
				'name' => 'emp_personal_info_id',
				'type' => 'text',
				'options' => array (
						'label' => 'Personal Info Id: ' 
				),
				'attributes' => array (
						'disabled' => 'disabled',
						'class' => 'emp_personal_info_id' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'lkp_bank_id',
				'type' => '\Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Bank: ',
						'value_options' => array (),
						'empty_option' => 'Select a bank',
						'disable_inarray_validator' => true 
				),
				'attributes' => array (
						'required' => 'required',
						'class' => 'lkp_bank_id' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'account_number',
				'type' => 'text',
				'options' => array (
						'label' => 'Account Number: ' 
				),
				'attributes' => array (
						'required' => 'required' 
				) 
		) );
	}
	public function getInputFilterSpecification() {
		return array (
				'emp_personal_info_id' => array (
						'required' => false 
				),
				'lkp_bank_id' => array (
						'required' => false 
				),
				'account_number' => array (
						'required' => true 
				) 
		);
	}
}
