<?php

namespace Simi\Instantsearch\Model\ResourceModel;

class Searchterm extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var type 
     */
    protected $storeManagerInterface;

    public $simiObjectManager;

    public $productStatus;
    /**
     * 
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param type $connectionName
     */

    protected $helperSearch;
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Simi\Instantsearch\Helper\Search $helperSearch,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManagerInterface = $storeManagerInterface;
        $this->helperSearch = $helperSearch;
        $this->simiObjectManager = $simiObjectManager;
        $this->productStatus = $productStatus;
        $this->storeManager = $this->simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
    }

    protected function _construct() {
        
    }

    public function getProductCollection($params, $limit = 10000)
    {
        $this->category = $this->simiObjectManager->create('\Magento\Catalog\Model\Category')->load($this->storeManager->getStore()->getRootCategoryId());
        $productcollection = $this->simiObjectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addAttributeToSelect('*') 
            ->setPageSize($limit)
            ->addCategoryFilter($this->category)            
            ->addAttributeToFilter(
                array(
                    array('attribute'=>'name','like'=>'%'.$params.'%'),
                    array('attribute'=>'description','like'=>'%'.$params.'%'),
                    array('attribute'=>'sku','like'=>'%'.$params.'%')
                )
            )
            ->addAttributeToSort('entity_id','asc');
            
            return $productcollection;
    }

    // when Instantsearch using magento search logic to response product
    public function getDefaultResultProduct($params, $limit = 100)
    {
        $searchCollection = $this->simiObjectManager
            ->create('Magento\CatalogSearch\Model\ResourceModel\Fulltext\SearchCollection');
        $searchCollection->addSearchFilter($params);

        $collection = $searchCollection;
        
        // \Zend_Debug::dump($collection->getData());die();
        
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('status',1)
            // ->addAttributeToSort('entity_id', 'asc')
            ->addFinalPrice()
            ->setPageSize($limit);
            

        return $collection;
    }
}
