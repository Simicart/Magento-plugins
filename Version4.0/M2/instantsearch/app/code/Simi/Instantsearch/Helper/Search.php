<?php

/**
 * Connector data helper
 */

namespace Simi\Instantsearch\Helper;

use Magento\Store\Model\ScopeInterface;

class Search extends \Magento\Framework\App\Helper\AbstractHelper
{

    public $simiObjectManager;
    public $storeManager;
    public $builderQuery;
    public $data        = [];
    public $sortOrders = [];
    public $category;
    public $productStatus;
    public $productVisibility;
    public $filteredAttributes = [];
    public $is_search = 0;

    const XML_PATH_RANGE_STEP = 'catalog/layered_navigation/price_range_step';
    const MIN_RANGE_POWER     = 10;



    // const XML_PATH_SEARCH_DELAY = 'simi_instantsearch/simi_instsantsearch_main/search_delay';

    /**
     * XML config path autocomplete fields
     */
    const XML_PATH_SEARCH_POPUP = 'simi_instantsearch/simi_instsantsearch_main/search_popup';

    /**
     * XML config path suggest results number
     */
    const XML_PATH_SEARCH_SUGGEST = 'simi_instantsearch/simi_instsantsearch_main/search_suggest';

    /**
     * XML config path product results number
     */
    const XML_PATH_SEARCH_RESULT = 'simi_instantsearch/simi_instsantsearch_main/search_result';

    /**
     * XML config path product result fields
     */
    const XML_PATH_SEARCH_FIELD = 'simi_instantsearch/simi_instsantsearch_main/search_field';

    const XML_PATH_ACTIVE = 'simi_instantsearch/simi_instsantsearch_main/active';

    const XML_PATH_SEARCHING_TYPE = 'simi_instantsearch/simi_instsantsearch_main/searching_type';

    const XML_PATH_MINIMUM_SEARCH = 'simi_instantsearch/simi_instsantsearch_main/minimum_search';

    const XML_PATH_TITLE_BACKGROUND_COLOR = 'simi_instantsearch/simi_instsantsearch_main/title_background_color';
    
    const XML_PATH_BACKGROUND_COLOR = 'simi_instantsearch/simi_instsantsearch_main/background_color';
    
    const XML_PATH_FONT_COLOR = 'simi_instantsearch/simi_instsantsearch_main/font_color';

    const XML_PATH_BORDER_COLOR = 'simi_instantsearch/simi_instsantsearch_main/border_color';

    const XML_PATH_SUGGEST_FIELD_TITLE = 'simi_instantsearch/simi_instsantsearch_main/suggest_field_title';

    const XML_PATH_RESULT_FIELD_TITLE = 'simi_instantsearch/simi_instsantsearch_main/result_field_title';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {

        $this->simiObjectManager = $simiObjectManager;
        $this->scopeConfig      = $this->simiObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->storeManager     = $this->simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->productStatus     = $productStatus;
        $this->productVisibility = $productVisibility;
        parent::__construct($context);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return product collection.
     *
     */
    public function getBuilderQuery()
    {
        return $this->builderQuery;
    }

    public function getProduct($product_id)
    {
        $this->builderQuery = $this->simiObjectManager->create('Magento\Catalog\Model\Product')->load($product_id);
        if (!$this->builderQuery->getId()) {
            throw new \Simi\Instantsearch\Helper\SimiException(__('Resource cannot callable.'), 6);
        }
        return $this->builderQuery;
    }

    /**
     *
     */
    public function setCategoryProducts($category)
    {
        $this->category = $this->simiObjectManager->create('\Magento\Catalog\Model\Category')->load($category);
        $this->setLayers(0);
        return $this;
    }

    public function loadCategoryWithId($id)
    {
        $categoryModel    = $this->simiObjectManager
            ->create('\Magento\Catalog\Model\Category')->load($id);
        return $categoryModel;
    }

    public function loadAttributeByKey($key)
    {
        return $this->simiObjectManager
            ->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection')
            ->getItemByColumnValue('attribute_code', $key);
    }

    /**
     * @param int $is_search
     * @param int $category
     * set Layer and collection on Products
     */
    public function setLayers($is_search = 0)
    {
        $this->is_search = $is_search;
        $data       = $this->getData();
        $controller = $data['controller'];
        $parameters = $data['params'];
        $params = [];
        $params['filter']['q'] = $parameters['q'];


        $collection         = $this->simiObjectManager
            ->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('entity_id', 1)
            ->addFinalPrice();
          
        $collection         = $this->_filter($collection, $params);

        if (!$this->scopeConfig->getValue('cataloginventory/options/show_out_of_stock')) {
            $this->simiObjectManager->get('Magento\CatalogInventory\Helper\Stock')
                ->addInStockFilterToCollection($collection);
        }
        $this->builderQuery = $collection;
        
    }

    public function _filter($collection, $params)
    {
        $cat_filtered = false;
        //search
        if (isset($params['filter']['q'])) {
            $this->getSearchProducts($collection, $params);
        } else {
            $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
            $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        }

        $data       = $this->getData();
        $controller = $data['controller'];

        return $collection;
    }

    public function getSearchProducts(&$collection, $params)
    {
        $data       = $this->getData();
        $parameters = $data['params'];
        

        $searchCollection = $this->simiObjectManager
            ->create('Magento\CatalogSearch\Model\ResourceModel\Fulltext\SearchCollection');
        $searchCollection->addSearchFilter($params['filter']['q']);

        $collection = $searchCollection;
        
        
        
        // $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        if(isset($parameters['limit'])) { 
            $collection->addAttributeToSelect('*')
            ->setPageSize($parameters['limit'])
            ->addStoreFilter()
            ->addAttributeToFilter('status',1)
            ->addFinalPrice();
        } else {
            $collection->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('status',1)
            ->addFinalPrice();
        }
        // \Zend_Debug::dump($collection->getData());die();
         
    }

    public function getLayerNavigator($collection = null)
    {
        if (!$collection) {
            $collection = $this->builderQuery;
        }
        $data       = $this->getData();
        $params = $data['params'];

        $attributeCollection = $this->simiObjectManager
            ->create('Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection');
        $attributeCollection->addIsFilterableFilter()
            ->addVisibleFilter()
            ->addFieldToFilter('is_visible_on_front', 1);

        $allProductIds = $collection->getAllIds();
        $arrayIDs      = [];
        foreach ($allProductIds as $allProductId) {
            $arrayIDs[$allProductId] = '1';
        }
        $layerFilters = [];

        $titleFilters = [];
        $this->_filterByAtribute($collection, $attributeCollection, $titleFilters, $layerFilters, $arrayIDs);

        if ($this->simiObjectManager
            ->get('Magento\Framework\App\ProductMetadataInterface')
            ->getEdition() != 'Enterprise')
            $this->_filterByPriceRange($layerFilters, $collection, $params);

        // category
        if ($this->category) {
            $childrenCategories = $this->category->getChildrenCategories();
            $collection->addCountToCategories($childrenCategories);
            $filters            = [];
            foreach ($childrenCategories as $childCategory) {
                if ($childCategory->getProductCount()) {
                    $filters[] = [
                        'label' => $childCategory->getName(),
                        'value' => $childCategory->getId(),
                        'count' => $childCategory->getProductCount()
                    ];
                }
            }

            $layerFilters[] = [
                'attribute' => 'category_id',
                'title'     => __('Categories'),
                'filter'    => ($filters),
            ];
        }

        $paramArray = (array)$params;
        $selectedFilters = $this->_getSelectedFilters();
        $selectableFilters = $this->_getSelectableFilters($collection, $paramArray, $selectedFilters, $layerFilters);

        $layerArray = ['layer_filter' => $selectableFilters];
        if ($this->simiObjectManager->get('Simi\Instantsearch\Helper\Data')->countArray($selectedFilters) > 0) {
            $layerArray['layer_state'] = $selectedFilters;
        }
        return $layerArray;
    }

    public function _getSelectedFilters()
    {
        $selectedFilters   = [];
        foreach ($this->filteredAttributes as $key => $value) {
            if (($key == 'category_id') && is_array($value) &&
                ($this->simiObjectManager->get('Simi\Instantsearch\Helper\Data')->countArray($value)>=2)) {
                $value = $value[1];
                $category = $this->loadCategoryWithId($value);
                $selectedFilters[] = [
                    'value'=>$value,
                    'label'=>$category->getName(),
                    'attribute' => 'category_id',
                    'title'     => __('Categories'),
                ];
                continue;
            }
            if (($key == 'price') && is_array($value) &&
                ($this->simiObjectManager->get('Simi\Instantsearch\Helper\Data')->countArray($value)>=2)) {
                $selectedFilters[] = [
                    'value'=> implode('-', $value),
                    'label'=> $this->_renderRangeLabel($value[0], $value[1]),
                    'attribute' => 'price',
                    'title'     => __('Price')
                ];
                continue;
            }

            $attribute = $this->loadAttributeByKey($key);
            if (is_array($value)) {
                $value = $value[0];
            }
            foreach ($attribute->getSource()->getAllOptions() as $layerFilter) {
                if ($layerFilter['value'] == $value) {
                    $layerFilter['attribute'] = $key;
                    $layerFilter['title'] = $attribute->getDefaultFrontendLabel();
                    $selectedFilters[]    = $layerFilter;
                }
            }
        }
        return $selectedFilters;
    }

    public function _getSelectableFilters($collection, $paramArray, $selectedFilters, $layerFilters)
    {
        $selectableFilters = [];
        if (is_array($paramArray) && isset($paramArray['filter']) && ($this->simiObjectManager
                    ->get('Simi\Instantsearch\Helper\Data')
                    ->countCollection($collection) >= 1)) {
            foreach ($layerFilters as $layerFilter) {
                $filterable = true;
                foreach ($selectedFilters as $key => $value) {
                    if ($layerFilter['attribute'] == $value['attribute']) {
                        $filterable = false;
                        break;
                    }
                }
                if ($filterable) {
                    $selectableFilters[] = $layerFilter;
                }
            }
        }
        return $selectableFilters;
    }

    public function _filterByAtribute($collection, $attributeCollection, &$titleFilters, &$layerFilters, $arrayIDs)
    {
        foreach ($attributeCollection as $attribute) {
            $attributeOptions = [];
            $attributeValues  = $collection->getAllAttributeValues($attribute->getAttributeCode());
            if (($attribute->getData('is_visible') != '1') || ($attribute->getData('is_filterable') != '1')
                || ($attribute->getData('is_visible_on_front') != '1')
                || (in_array($attribute->getDefaultFrontendLabel(), $titleFilters))) {
                continue;
            }
            foreach ($attributeValues as $productId => $optionIds) {
                if (isset($optionIds[0]) && isset($arrayIDs[$productId]) && ($arrayIDs[$productId] != null)) {
                    $optionIds = explode(',', $optionIds[0]);
                    foreach ($optionIds as $optionId) {
                        if (isset($attributeOptions[$optionId])) {
                            $attributeOptions[$optionId] ++;
                        } else {
                            $attributeOptions[$optionId] = 1;
                        }
                    }
                }
            }

            $options = $attribute->getSource()->getAllOptions();
            $filters = [];
            foreach ($options as $option) {
                if ($option['value'] && isset($attributeOptions[$option['value']])
                    && $attributeOptions[$option['value']]) {
                    $option['count'] = $attributeOptions[$option['value']];
                    $filters[]       = $option;
                }
            }

            if ($this->simiObjectManager->get('Simi\Instantsearch\Helper\Data')->countArray($filters) >= 1) {
                $titleFilters[] = $attribute->getDefaultFrontendLabel();
                $layerFilters[] = [
                    'attribute' => $attribute->getAttributeCode(),
                    'title'     => $attribute->getDefaultFrontendLabel(),
                    'filter'    => $filters,
                ];
            }
        }
    }

    public function _filterByPriceRange(&$layerFilters, $collection, $params)
    {
        $priceRanges = $this->_getPriceRanges($collection);
        $filters     = [];
        $totalCount  = 0;
        $maxIndex    = 0;
        if ($this->simiObjectManager->get('Simi\Instantsearch\Helper\Data')->countArray($priceRanges['counts']) > 0) {
            $maxIndex = max(array_keys($priceRanges['counts']));
        }
        foreach ($priceRanges['counts'] as $index => $count) {
            if ($index === '' || $index == 1) {
                $index = 1;
                $totalCount += $count;
            } else {
                $totalCount = $count;
            }
            if (isset($params['layer']['price'])) {
                $prices    = explode('-', $params['layer']['price']);
                $fromPrice = $prices[0];
                $toPrice   = $prices[1];
            } else {
                $fromPrice = $priceRanges['range'] * ($index - 1);
                $toPrice   = $index == $maxIndex ? '' : $priceRanges['range'] * ($index);
            }

            if ($index >= 1) {
                $filters[$index] = [
                    'value' => $fromPrice . '-' . $toPrice,
                    'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                    'count' => (int) ($totalCount)
                ];
            }
        }
        if ($this->simiObjectManager
                ->get('Simi\Instantsearch\Helper\Data')
                ->countArray($filters) >= 1) {
            $layerFilters[] = [
                'attribute' => 'price',
                'title'     => __('Price'),
                'filter'    => array_values($filters),
            ];
        }
    }
    /*
     * Get price range filter
     *
     * @param @collection \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @return array
     */

    public function _getPriceRanges($collection)
    {
        $maxPrice = $collection->getMaxPrice();
        $index    = 1;
        do {
            $range  = pow(10, strlen(floor($maxPrice)) - $index);
            $counts = $collection->getAttributeValueCountByRange('price', $range);
            $index++;
        } while ($range > self::MIN_RANGE_POWER && $this->simiObjectManager
            ->get('Simi\Instantsearch\Helper\Data')->countArray($counts) < 2);

        return ['range' => $range, 'counts' => $counts];
    }

    /*
     * Show price filter label
     *
     * @param $fromPrice int
     * @param $toPrice int
     * @return string
     */

    public function _renderRangeLabel($fromPrice, $toPrice)
    {
        $helper             = $this->simiObjectManager->create('Magento\Framework\Pricing\Helper\Data');
        $formattedFromPrice = $helper->currency($fromPrice, true, false);
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }

            return __('%1 - %2', $formattedFromPrice, $helper->currency($toPrice, true, false));
        }
    }

    public function getImageProduct($product, $file = null, $width = null, $height = null)
    {
        if (!($width === null) && !($height === null)) {
            if ($file) {
                return $this->simiObjectManager->get('Magento\Catalog\Helper\Image')
                    ->init($product, 'product_page_image_medium')
                    ->setImageFile($file)
                    ->resize($width, $height)
                    ->getUrl();
            }
            return $this->simiObjectManager->get('Magento\Catalog\Helper\Image')
                ->init($product, 'product_page_image_medium')
                ->setImageFile($product->getFile())
                ->resize($width, $height)
                ->getUrl();
        }
        if ($file) {
            return $this->simiObjectManager->get('Magento\Catalog\Helper\Image')
                ->init($product, 'product_page_image_medium')
                ->setImageFile($file)
                ->resize(600, 600)
                ->getUrl();
        }
        return $this->simiObjectManager->get('Magento\Catalog\Helper\Image')
            ->init($product, 'product_page_image_medium')
            ->setImageFile($product->getFile())
            ->resize(600, 600)
            ->getUrl();
    }

    public function setStoreOrders($block_list, $block_toolbar, $is_search = 0)
    {
        if (!$block_toolbar->isExpanded()) {
            return;
        }
        $sort_orders = [];

        if ($sort = $block_list->getSortBy()) {
            $block_toolbar->setDefaultOrder($sort);
        }
        if ($dir = $block_list->getDefaultDirection()) {
            $block_toolbar->setDefaultDirection($dir);
        }

        $availableOrders = $block_toolbar->getAvailableOrders();

        if ($is_search == 1) {
            unset($availableOrders['position']);
            $availableOrders = array_merge([
                'relevance' => __('Relevance')
            ], $availableOrders);

            $block_toolbar->setAvailableOrders($availableOrders)
                ->setDefaultDirection('asc')
                ->setSortBy('relevance');
        }

        foreach ($availableOrders as $_key => $_order) {
            if ($block_toolbar->isOrderCurrent($_key)) {
                if ($block_toolbar->getCurrentDirection() == 'desc') {
                    $sort_orders[] = [
                        'key'       => $_key,
                        'value'     => $_order,
                        'direction' => 'asc',
                        'default'   => '0'
                    ];

                    $sort_orders[] = [
                        'key'       => $_key,
                        'value'     => $_order,
                        'direction' => 'desc',
                        'default'   => '1'
                    ];
                } else {
                    $sort_orders[] = [
                        'key'       => $_key,
                        'value'     => $_order,
                        'direction' => 'asc',
                        'default'   => '1'
                    ];
                    $sort_orders[] = [
                        'key'       => $_key,
                        'value'     => $_order,
                        'direction' => 'desc',
                        'default'   => '0'
                    ];
                }
            } else {
                $sort_orders[] = [
                    'key'       => $_key,
                    'value'     => $_order,
                    'direction' => 'asc',
                    'default'   => '0'
                ];

                $sort_orders[] = [
                    'key'       => $_key,
                    'value'     => $_order,
                    'direction' => 'desc',
                    'default'   => '0'
                ];
            }
        }
        $this->sortOrders = $sort_orders;
    }

    public function getStoreQrders()
    {
        if (!$this->sortOrders) {
            $block_toolbar = $this->simiObjectManager->get('Magento\Catalog\Block\Product\ProductList\Toolbar');
            $block_list    = $this->simiObjectManager->get('Magento\Catalog\Block\Product\ListProduct');
            $data = $this->getData();
            if (isset($data['params']['order']) && isset($data['params']['dir'])) {
                $block_list->setSortBy($data['params']['order']);
                $block_list->setDefaultDirection($data['params']['dir']);
            }
            $this->setStoreOrders($block_list, $block_toolbar, $this->is_search);
        }
        return $this->sortOrders;
    }

/////
    // public function getSearchDelay($storeId = null)
    // {
    //     return (int)$this->scopeConfig->getValue(
    //         self::XML_PATH_SEARCH_DELAY,
    //         ScopeInterface::SCOPE_STORE,
    //         $storeId
    //     );
    // }

    /**
     * Retrieve comma-separated search popup
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSearchPopup($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_POPUP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve list of search popup
     *
     * @param int|null $storeId
     * @return array
     */
    public function getSearchPopupAsArray($storeId = null)
    {
        return explode(',', $this->getSearchPopup($storeId));
    }

    /**
     * Retrieve number of search suggest
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSearchSuggest($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_SUGGEST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve number of search result 
     *
     * @param int|null $storeId
     * @return int
     */
    public function getSearchResult($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_RESULT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve result fields
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSearchField($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_FIELD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve list of search fields
     *
     * @param int|null $storeId
     * @return array
     */
    public function getSearchFieldAsArray($storeId = null)
    {
        return explode(',', $this->getSearchField($storeId));
    }

    public function isActive($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getSearchingType($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEARCHING_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getMinimumCharacterSearch($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINIMUM_SEARCH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTitleBackgroundColor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE_BACKGROUND_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getBackgroundColor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BACKGROUND_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getFontColor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FONT_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getBorderColor($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BORDER_COLOR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getSuggestTitle($storeId = null) {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RESULT_FIELD_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getResultTitle($storeId = null) {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SUGGEST_FIELD_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
/////

}
