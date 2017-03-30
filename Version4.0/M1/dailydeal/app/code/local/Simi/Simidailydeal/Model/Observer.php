<?php

class Simi_Simidailydeal_Model_Observer {
    public function collectionloadafter($observer) {
		if(!Mage::registry('is_random_simidailydeal'))
		Mage::helper('simidailydeal')->updateSimidailydealStatus();
        $productCollection = $observer['collection'];
        foreach ($productCollection as $product){
            $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($product->getEntityId());
            if($simidailydeal->getId()){
            $temp=$simidailydeal->getQuantity()-$simidailydeal->getSold();
            $product->setData('final_price',$product->getPrice()-$simidailydeal->getSave()*$product->getPrice()/100);
			}
        }
            
    }
     public function getfinalprice($observer) {
        if(!Mage::registry('is_random_simidailydeal'))
		Mage::helper('simidailydeal')->updateSimidailydealStatus();
        $product = $observer['product'];
                $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($product->getEntityId());
                if($simidailydeal->getId()){
                $temp=$simidailydeal->getQuantity()-$simidailydeal->getSold();
                $product->setData('final_price',$product->getPrice()-$simidailydeal->getSave()*$product->getPrice()/100);
                }

    }
    public function saveorder($observer) {
        $order =$observer['order'];
        $items=$order->getAllItems();
        $deals=array();
        foreach($items as $item) {
            $productId=$item->getProductId();
            $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($productId);
			if($simidailydeal->getId()){
            $temp=$simidailydeal->getQuantity()-$simidailydeal->getSold();
            $sold=$simidailydeal->getSold();
            if ($temp>0){
                $deals[]=$simidailydeal->getId();
                $simidailydeal->setSold($sold+$item->getQtyOrdered())
                    ->save();
            }
			}
        }
        $order->setData('simidailydeals',implode(",",$deals));
    }
    public function update_items($observer){
        $cart =$observer['cart'];
        $items = $cart->getQuote()->getAllItems();
        $temp=Mage::getStoreConfig('simidailydeal/general/limit');
        $i=0;
        foreach ($items as $item) {
            $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($item->getProductId());
			if($simidailydeal->getId()){
            $limit=$simidailydeal->getQuantity()-$simidailydeal->getSold();
            if($limit>0){
                if (($limit > $temp)&&($temp>0)) $limit=$temp;
                if ($item->getQty() > $limit) {
                    $item->setQty($limit)->save();
                    $i=1;
                }
            }
			}
        }
        if ($i==1)
            Mage::getSingleton('checkout/session')->addError(Mage::helper('simidailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
    }
    public function addproduct(){
        $cart = $this->_getCart();
        $items = $cart->getQuote()->getAllItems();
        $productId = (int)Mage::app()->getRequest()->getParam('product');
        
                $simidailydeal=Mage::getModel('simidailydeal/simidailydeal')->getSimidailydealByProduct($productId);
				if($simidailydeal->getId()){
                $limit=$simidailydeal->getQuantity()-$simidailydeal->getSold();
                if($limit>0){
                    $temp=Mage::getStoreConfig('simidailydeal/general/limit');
                    if (($limit > $temp)&&($temp>0)) $limit=$temp;
                    $qty=1;
                    $is_order=false;
                    if (Mage::app()->getRequest()->getParam('qty')) $qty=Mage::app()->getRequest()->getParam('qty');
                    
                    foreach ($items as $item){
                        if ($item->getProductId() == $productId) {
                            $is_order=true;
                            if (($item->getQty()+$qty) > $limit) {
                                Mage::app()->getRequest()->setPost('qty',0);
                                $item->setQty($limit-1)->save();
                                Mage::getSingleton('checkout/session')->addError(Mage::helper('simidailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
                            }
                        }                         
                    }
                    if ((!$is_order)&&($qty > $limit )){
                                Mage::app()->getRequest()->setPost('qty',$limit);
                                Mage::getSingleton('checkout/session')->addError(Mage::helper('simidailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
                    }
                }
				}
    }
    protected function _getCart(){
        return Mage::getSingleton('checkout/cart');
    }
    public function qtyItem($items,$product_id,$check){
        $qty=0;
        foreach ($items as $item) {
            if($product_id==$item->getProductId()){
                if($check==1){
               $qty=$item->getQtyCanceled();
                }  else {
                $qty=$item->getQty();    
                }
               return $qty;
            }
        }
        return $qty;
    }

    public function refundCreditmemo($observer){
        $creditmemo=$observer['creditmemo'];
        $order_id=$creditmemo->getOrderId();
        $order=Mage::getModel('sales/order')->load($order_id);
        $simidailydeals=$order->getSimidailydeals();
		$simidailydeals_arr = explode(',', $simidailydeals);
        $items=$creditmemo->getAllItems();
		foreach($simidailydeals_arr as $value){
			$simidailydeal = Mage::getModel('simidailydeal/simidailydeal')->load($value);
			$product_id = $simidailydeal->getProductId();
			$qty = $this->qtyItem($items, $product_id, 0);
			$sold = $simidailydeal->getSold() - $qty;
			
			if($sold >= 0){
				$simidailydeal->setSold($sold)->save();
			}
		}

    }
    public function orderCancelAfter($observer){
       $order=$observer['order'];
       $simidailydeals=$order->getSimidailydeals();
        $items=$order->getAllItems();
		$simidailydeals_arr = explode(',', $simidailydeals);
		foreach($simidailydeals_arr as $value){
			$simidailydeal = Mage::getModel('simidailydeal/simidailydeal')->load($value);
			$product_id = $simidailydeal->getProductId();
			$qty = $this->qtyItem($items, $product_id,1);
			$sold = $simidailydeal->getSold() - $qty;
			if($sold >= 0){
				$simidailydeal->setSold($sold)->save();
				
			}
			
		}
    }
}
