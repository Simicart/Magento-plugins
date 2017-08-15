<?php

class Simi_Simidailydeal_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _initAction()
	{
		$this->loadLayout();
		$this->renderLayout();

		return $this;
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

    public function installDBAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup('core_setup');
        $installer = $setup;
        $installer->startSetup();

        $installer->run("
            
        ");
        $installer->endSetup();
        echo 'success';
    }
}