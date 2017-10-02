<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simipayfort
 * @copyright   Copyright (c) 2017 
 * @license     
 */

/**
 * Simipayfort Adminhtml Controller
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        Zend_Debug::dump(Mage::getStoreConfig("payment/simipayfort/url_back"));
    }

    /**
     * index action
     */
    public function checkInstallAction() {
        echo "1";
        exit();
    }

    public function installDbAction() {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('simipayfort')};

			CREATE TABLE {$setup->getTable('simipayfort')} (
			  `simipayfort_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NOT NULL default '',
			  `transaction_name` varchar(255) NOT NULL default '',
			  `transaction_email` text NOT NULL default '',
			  `status` smallint(6) NOT NULL default '0',  
			  `currency_code` datetime NULL,
			  `order_id` int(11) NULL,  
			  PRIMARY KEY (`simipayfort_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
		");
        $installer->endSetup();
        echo "success";
    }

    public function startPayfortAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        echo $this->redirectToForm($orderId);
    }

    public function redirectToForm($orderId) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        Mage::getStoreConfig("payment/simipayfort/is_sandbox");
        echo '
        <form name="myForm" id="myForm" action="'.Mage::getUrl('simipayfort/Start.php', array('_secure'=>true)).'" method="POST">
            <input type="hidden" name="secret_key" value="'.Mage::getStoreConfig("payment/simipayfort/private_key").'">
            <input type="hidden" name="open_key" value="'.Mage::getStoreConfig("payment/simipayfort/public_key").'">
            <input type="hidden" name="successurl" value="'.Mage::getStoreConfig("payment/simipayfort/success_url").'">
            <input type="hidden" name="currency" value="'.$order->getData('order_currency_code').'">
            <input type="hidden" name="user_email" value="'.$order->getData('customer_email').'">
            <input type="hidden" name="value" value="'.$order->getData('grand_total').'">
            <input type="hidden" name="command" value="'.Mage::helper('simipayfort')->__('Payment for Order').' '.$orderId.'">
            <input type="submit" value="Submit" />
        </form>

        <script type="text/javascript">
            document.getElementById("myForm").submit();
        </script>
        ';
    }


}
