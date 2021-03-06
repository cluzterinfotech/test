<?php 
namespace Payment\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class DeductionOverpaymentFormValidator implements InputFilterAwareInterface 
{
	protected $inputFilter;
    
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
    
	public function getInputFilter()
	{
		if (!$this->inputFilter)
		{
			$inputFilter = new InputFilter(); 
			$factory = new InputFactory(); 
            
			$inputFilter->add($factory->createInput([
				'name' => 'employeeNumber', 
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					/*array(
			    		'name' => 'StringLength',
						'options' => array(
					    	//'encoding' => 'UTF-8',
							//'min' => '3',
							//'max' => '25',
						),
					),*/
				),
			]));
            
			$inputFilter->add($factory->createInput([
					'name' => 'amount',
					'required' => true,
					//'filters' => array(
							//array('name' => 'Digits'),
					//),
					'validators' => array(
						/*array (
							'name' => 'StringLength',
							'options' => array(
								//'encoding' => 'UTF-8',
								//'min' => '2',
								//'max' => '3',
							),
						),*/
					),
			]));
			
			$inputFilter->add($factory->createInput([
			    'name' => 'numberOfMonthsDed',
			    'required' => true,
			    'filters' => array(
			        array('name' => 'Digits'),
			    ),
			    'validators' => array(
			        /*array (
			         'name' => 'StringLength',
			         'options' => array(
			         //'encoding' => 'UTF-8',
			         //'min' => '2',
			         //'max' => '3',
			         ),
			         ),*/
			    ),
			]));
            
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}