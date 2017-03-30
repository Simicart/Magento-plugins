<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Simi_Simistorelocator_Model_Exporter extends Varien_Object{
    
    protected $_fieldstr = 'simistorelocator_id,name,status,address,city,state,country,zipcode,latitude,longtitude,fax,phone,email,link,zoom_level,image_name,tag_store';
    public function exportStoreLocator(){
        
        $stores = Mage::getModel('simistorelocator/simistorelocator')->getCollection();
        
        if(!count($stores))
        {
            return false;
        }
        
        foreach ($stores as $store){
            $data[] = $this->getStoreData($store);
        }
        $csv = '';
        $csv .= $this->_fieldstr. "\n";
        foreach ($data as $item){
            $rowstr = implode('","', $item);
            $rowstr = '"'.$rowstr.'"';
            $csv .= $rowstr."\n";
        }
        return $csv;
    }
    public function getXmlStoreLocator(){
        
        $stores = MAge::getModel('simistorelocator/simistorelocator')->getCollection();
        $storeCollections = array();
        
        if(!count($stores)){
            return false;
        }
        
        foreach ($stores as $item){
            $data = $this->getStoreData($item);
            $item->setData($data);
            $storeCollections[] = $item;
        }
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($storeCollections as $item) {
            $xml.= $item->toXml();
        }
        $xml.= '</items>';	
		
        return $xml;
    }
    
    public function getStoreData($store)
	{
		$data = $store->getData();
		//prepare location
                $data['tag_store'] = Mage::helper('simistorelocator')->getTags($store->getId());
                $data['image_name'] = Mage::helper('simistorelocator')->getImageNameByStore($store->getId());
                if($data['status'] == 1){
                    $data['status'] = 'Enabled';
                }else{
                    $data['status'] = 'Disabled';
                }
		$fields = $this->_getFields();
		
		$export_data = array();
		foreach($fields as $field)
		{
			$value = isset($data[$field]) ? $data[$field] : '';
			$export_data[$field] = $value;
		}
		
		return $export_data;
	}
	
	protected function _getFields()
	{
		if(! $this->getData('fields'))
		{
			$fields = explode(',',$this->_fieldstr);
			$this->setData('fields',$fields);
		}
		
		return $this->getData('fields');
	}
}
?>
