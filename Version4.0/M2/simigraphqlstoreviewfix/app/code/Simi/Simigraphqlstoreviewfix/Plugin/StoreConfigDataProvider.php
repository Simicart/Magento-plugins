<?php

namespace Simi\Simigraphqlstoreviewfix\Plugin;

class StoreConfigDataProvider
{

    private $simiObjectManager;
    private $storeManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
   		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
        $this->storeManager = $storeManager;
    }

	public function aroundGetStoreConfigData(\Magento\StoreGraphQl\Model\Resolver\Store\StoreConfigDataProvider $subject, callable $proceed, $store)
	{
		$currentStore = $this->storeManager->getStore();
		$result = $proceed($currentStore);
		return $result;
	}

}