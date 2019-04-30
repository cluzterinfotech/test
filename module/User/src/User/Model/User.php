<?php
// module/Album/src/Album/Model/Album.php:
namespace User\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface {
	// public $id;
	public $username;
	public $password;
	protected $inputFilter;
	public function exchangeArray($data) {
		// $this->id = (isset($data['id'])) ? $data['id'] : null;
		$this->username = (isset ( $data ['username'] )) ? $data ['username'] : null;
		$this->password = (isset ( $data ['password'] )) ? $data ['password'] : null;
	}
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {
			$inputFilter = new InputFilter ();
			$factory = new InputFactory ();
			/*
			 * $inputFilter->add($factory->createInput(array(
			 * 'name' => 'id',
			 * 'required' => true,
			 * 'filters' => array(
			 * array('name' => 'Int'),
			 * ),
			 * )));
			 */
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'username',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StripTags' 
							),
							array (
									'name' => 'StringTrim' 
							) 
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100 
									) 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'password',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StripTags' 
							),
							array (
									'name' => 'StringTrim' 
							) 
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 6,
											'max' => 20 
									) 
							) 
					) 
			) ) );
			
			/* $inputFilter->add ( $factory->createInput ( array (
					'name' => 'company',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 6,
											'max' => 20
									)
							)
					)
			) ) ); */
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}
