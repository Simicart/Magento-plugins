<?php
class Simi_Simidailydeal_Block_Adminhtml_Randomdeal_Renderer_Simidailydealavailable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
        $simidailydealId = $row->getProductId();
        $str='';
        $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->load($simidailydealId);
        if (!$simidailydealId){
            $str= $this->__('There is no daily deal available!');
        }  else {
         $str .='<a href="'.$this->getUrl('simidailydealadmin/adminhtml_simidailydeal/edit/', array('id' => $simidailydealId)).'">'.$simidailydeal->getTitle().'</a></br>';
        }

        return $str;
    }
}