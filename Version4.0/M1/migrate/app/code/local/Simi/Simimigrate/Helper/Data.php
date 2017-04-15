<?php
class Simi_Simimigrate_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function addSimicartAppConfigId($collection) {
        $appTable = Mage::getSingleton('core/resource')->getTableName('simimigrate/app');
        $collection->getSelect()
                ->join(array('apptable' => $appTable),
                        'apptable.app_id = main_table.app_id', array('apptable.simicart_app_config_id'));
        return $collection;
    }
    
    public function joinAppConfigTable($collection) {
        $appTable = Mage::getSingleton('core/resource')->getTableName('simimigrate/app');
        $collection->getSelect()
                ->join(array('apptable' => $appTable),
                        'apptable.app_id = main_table.app_id');
        return $collection;
    }
}
	 