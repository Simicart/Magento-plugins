<?php
class Magestore_PaypalBNCode_Model_Config extends Mage_Paypal_Model_Config
{
    /**
     * BN code getter
     * override method
     *
     * @param string $countryCode ISO 3166-1
     */
    public function getBuildNotationCode($countryCode = null)
    {
		if($this->isMageEnterprise()){
			$newBnCode = 'Magestore_SI_MagentoEE';
		} else {
			$newBnCode = 'Magestore_SI_MagentoCE';
		}
		
        $bnCode = parent::getBuildNotationCode($countryCode);
		$newBnCode = str_replace('Varien_Cart',$newBnCode,$bnCode);	
		
		if(class_exists("Magestore_Onestepcheckout_Helper_Data") && Mage::getStoreConfig('onestepcheckout/general/active')){
			return $newBnCode;
		} else {
			return $bnCode;
		}
    }
	
	public function isMageEnterprise() {
		return Mage::getConfig ()->getModuleConfig ( 'Enterprise_Enterprise' ) 
			&& Mage::getConfig ()->getModuleConfig ( 'Enterprise_AdminGws' ) 
			&& Mage::getConfig ()->getModuleConfig ( 'Enterprise_Checkout' ) 
			&& Mage::getConfig ()->getModuleConfig ( 'Enterprise_Customer' );
	}		

}