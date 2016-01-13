<?php

class Simi_Simipayu_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	public function checkInstallAction(){
		echo "1";
		exit();
	}

	public function successAction(){
		echo "success";
		exit();
	}

	public function failureAction(){
		echo "failure";
		exit();
	}

	public function testAction(){
		Zend_debug::dump(Mage::helper('simipayu')->getFormFields("100002129"));die();
	}
}