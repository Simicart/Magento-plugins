<?php

class Simi_Simipayu_Model_Source_TransactionMode
{
	 public function toOptionArray()
    {
        $options =  array();       ;
        $options[] = array(
            	   'value' => 'test',
            	   'label' => 'Test'
         );
		 $options[] = array(
            	   'value' => 'live',
            	   'label' => 'Live'
         );

        return $options;
    }
}