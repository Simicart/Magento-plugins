<?php

class Simi_Simipayuindia_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_order; 
	
	public function getUrlCheckout($invoice_number){
		return Mage::getUrl('simipayuindia/api/checkout', array('invoice_number' => $invoice_number));
	}

	public function cleanString($string) {
        
        $string_step1 = strip_tags($string);
        $string_step2 = nl2br($string_step1);
        $string_step3 = str_replace("<br />","<br>",$string_step2);
        $cleaned_string = str_replace("\""," inch",$string_step3);        
        return $cleaned_string;
    }

    public function setOrderHistory($order_id){
		$order = Mage::getModel('sales/order');
        $order->loadByIncrementId($order_id);       
        $order->addStatusToHistory($order->getStatus(), Mage::helper('simipayuindia')->__('Customer was redirected to payu.'));
        $order->save();	
        $this->_order = $order;
    //     die('xxxx');
	}

	public function getForm($order_id){
		$this->setOrderHistory($order_id);
		$coFields = $this->getFormFields($this->_order);
		// $params = "?";
		//  foreach ($coFields as $field=>$value) {
  //           $params.=$field.'='.$value.'&';
  //       }
        return $coFields;
	}


	public function getFormFields($order)
    {
	
	    $billing = $order->getBillingAddress();
        $coFields = array();
        $items = $order->getAllItems();
		
		if ($items) {
            $i = 1;
            foreach($items as $item){
                if ($item->getParentItem()) {
                   continue;
                }        
                $coFields['c_prod_'.$i]            = $this->cleanString($item->getSku());
                $coFields['c_name_'.$i]            = $this->cleanString($item->getName());
                $coFields['c_description_'.$i]     = $this->cleanString($item->getDescription());
                $coFields['c_price_'.$i]           = number_format($item->getPrice(), 2, '.', '');
            $i++;
            }
        }
        
        $request = '';
        foreach ($coFields as $k=>$v) {
            $request .= '<' . $k . '>' . $v . '</' . $k . '>';
        }
		
		
		$key=Mage::getStoreConfig('payment/simipayuindia/key');
		$salt=Mage::getStoreConfig('payment/simipayuindia/salt');
		$debug_mode=Mage::getStoreConfig('payment/simipayuindia/debug_mode');
	
	    $orderId = $order->getRealOrderId(); 
	    $mode=Mage::getStoreConfig('payment/simipayuindia/demo_mode');
	    if($mode!='')
		{
		  $txnid = $orderId."_".rand(); 
		}
		else 
	    $txnid = $orderId; 
		
		$coFields['key']          = $key;
		$coFields['txnid']        =  $txnid;
		
		$coFields['amount']       =  number_format($order->getBaseGrandTotal(),0,'','');  
		$coFields['productinfo']  = 'Prpduct Information';  
		$coFields['firstname']    = $billing->getFirstname();
		$coFields['Lastname']     = $billing->getLastname();
		$coFields['City']         = $billing->getCity();
        $coFields['State']        = $billing->getRegion();
		$coFields['Country']      = $billing->getCountry();
        $coFields['Zipcode']      = $billing->getPostcode();
		$coFields['email']        = $order->getCustomerEmail();
        $coFields['phone']        = $billing->getTelephone();
		 
		$coFields['surl']         =  Mage::getBaseUrl().'simipayuindia/api/success/';  
		$coFields['furl']         =  Mage::getBaseUrl().'simipayuindia/api/failure/';
		//$coFields['curl']         =  Mage::getBaseUrl().'payucheckout/shared/canceled/id/'.$this->getOrder()->getRealOrderId();
		
		

		
		$coFields['Pg']           =  'CC';
		$debugId='';
		
        if ($debug_mode==1) {
			$requestInfo= $key.'|'.$coFields['txnid'].'|'.$coFields['amount'].'|'.
			$coFields['productinfo'].'|'.$coFields['firstname'].'|'.$coFields['email'].'|'.$debugId.'||||||||||'.$salt;
			            $debug = Mage::getModel('simipayuindia/simipayuindia')
			                ->setRequestBody($requestInfo)
			                ->save();
								
						$debugId = $debug->getId();	
						
						$coFields['udf1']=$debugId;
						$coFields['Hash']    =   hash('sha512', $key.'|'.$coFields['txnid'].'|'.$coFields['amount'].'|'.
			$coFields['productinfo'].'|'.$coFields['firstname'].'|'.$coFields['email'].'|'.$debugId.'||||||||||'.$salt);
		}
		else
		{
		 $coFields['Hash']         =   strtolower(hash('sha512', $key.'|'.$coFields['txnid'].'|'.$coFields['amount'].'|'.
		 $coFields['productinfo'].'|'.$coFields['firstname'].'|'.$coFields['email'].'|||||||||||'.$salt));
		}
		//Zend_debug::dump($coFields);die();
        return $coFields;
    }

    /**
     * Get url of Payu payment
     *
     * @return string
     */
    public function getPayuCheckoutSharedUrl()
    {
        $mode=Mage::getStoreConfig('payment/simipayuindia/demo_mode');
		
		$url='https://test.payu.in/_payment.php';
		
		if($mode=='')
		{
		  $url='https://secure.payu.in/_payment.php';
		}
		 
         return $url;
    }

    public function getResponseOperation($response)
	{
	   
	   $order = Mage::getModel('sales/order');	
	   $debug_mode=Mage::getStoreConfig('payment/simipayuindia/debug_mode');
	   $key=Mage::getStoreConfig('payment/simipayuindia/key');
	   $salt=Mage::getStoreConfig('payment/simipayuindia/salt');
	    if(isset($response['status']))
		{
		   $txnid=$response['txnid'];
		   $mode=Mage::getStoreConfig('payment/simipayuindia/demo_mode');
	       if($mode!='')
		   {
		     $txnid_split = explode("_", $txnid);
			 $orderid = $txnid_split[0];
		   }
		   else 
		   $orderid=$txnid;
		   
		   if($response['status']=='success')
			{
				$status=$response['status'];
				$order->loadByIncrementId($orderid);
				$billing = $order->getBillingAddress();
				$amount      = $response['amount'];
				$productinfo = $response['productinfo'];  
				$firstname   = $response['firstname'];
				$email       = $response['email'];
				$keyString='';
				$Udf1 = $response['udf1'];
		 		$Udf2 = $response['udf2'];
		 		$Udf3 = $response['udf3'];
		 		$Udf4 = $response['udf4'];
		 		$Udf5 = $response['udf5'];
		 		$Udf6 = $response['udf6'];
		 		$Udf7 = $response['udf7'];
		 		$Udf8 = $response['udf8'];
		 		$Udf9 = $response['udf9'];
		 		$Udf10 = $response['udf10'];
				if($debug_mode==1)
				{
				 $keyString =  $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|'.$Udf1.'|'.$Udf2.'|'.$Udf3.'|'.$Udf4.'|'.$Udf5.'|'.$Udf6.'|'.$Udf7.'|'.$Udf8.'|'.$Udf9.'|'.$Udf10;
				}
				else
				{
			      $keyString =  $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|'.$Udf1.'|'.$Udf2.'|'.$Udf3.'|'.$Udf4.'|'.$Udf5.'|'.$Udf6.'|'.$Udf7.'|'.$Udf8.'|'.$Udf9.'|'.$Udf10;	
				}
				
				$keyArray = explode("|",$keyString);
				$reverseKeyArray = array_reverse($keyArray);
				$reverseKeyString=implode("|",$reverseKeyArray);
				$saltString     = $salt.'|'.$status.'|'.$reverseKeyString;
				$sentHashString = strtolower(hash('sha512', $saltString));
				 $responseHashString=$_REQUEST['hash'];
				if($sentHashString==$responseHashString)
				{
						$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
						$order->save();
						$order->sendNewOrderEmail();
				
				}
				else
				{
					$order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
					$order->cancel()->save();
					}
				
				if ($debug_mode==1) {
			    	$debugId=$response['udf1'];  
					$data = array('response_body'=>implode(",",$response));
					$model = Mage::getModel('simipayuindia/simipayuindia')->load($debugId)->addData($data);
					$model->setId($id)->save();
				  }
			   }
		   
		   if($response['status']=='failure')
		   {
		       $order->loadByIncrementId($orderid);
		       $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
		       // Inventory updated 
			   $this->updateInventory($orderid);
			   
			   $order->cancel()->save();
			   
			   if ($debug_mode==1) {
				$debugId=$response['udf1'];
						$data = array('response_body'=>implode(",",$response));
					$model = Mage::getModel('simipayuindia/simipayuindia')->load($debugId)->addData($data);
					$model->setId($id)->save();
				  }
		   
		   }
		   else  if($response['status']=='pending')
		   {
		       $order->loadByIncrementId($orderid);
		       $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
		       // Inventory updated  
		       $this->updateInventory($orderid);
			   $order->cancel()->save();
			 		   
			   if ($debug_mode==1) {
				$debugId=$response['udf1'];
						$data = array('response_body'=>implode(",",$response));
					$model = Mage::getModel('simipayuindia/simipayuindia')->load($debugId)->addData($data);
					$model->setId($id)->save();
				  }
		   
		   }
		   
		}
        else
		{
		  		   
		   $order->loadByIncrementId($response['id']);
		   $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
		  // Inventory updated 
		   $order_id=$response['id'];
		   $this->updateInventory($order_id);
		   
		   $order->cancel()->save();
		   
		 
		}
	}
	
    public function updateInventory($order_id)
    {
  
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = $order->getAllItems();
		foreach ($items as $itemId => $item)
		{
		   $ordered_quantity = $item->getQtyToInvoice();
		   $sku=$item->getSku();
		   $product = Mage::getModel('catalog/product')->load($item->getProductId());
		   $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId())->getQty();
		  
		   $updated_inventory=$qtyStock + $ordered_quantity;
					
		   $stockData = $product->getStockItem();
		   $stockData->setData('qty',$updated_inventory);
		   $stockData->save(); 
			
	   } 
    }
}