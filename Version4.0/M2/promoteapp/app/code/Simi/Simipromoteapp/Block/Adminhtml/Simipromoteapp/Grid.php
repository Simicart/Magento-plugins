<?php

namespace Simi\Simipromoteapp\Block\Adminhtml\Simipromoteapp;

/**
 * Adminhtml Connector grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    public $collectionFactory;
    public $moduleManager;

    /**
     * @var order model
     */
    public $resource;
    public $simiObjectManager;

    /**
     * @var order status model
     */
    public $orderStatus;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simipromoteapp\Model\ResourceModel\Simipromoteapp\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollection,
        array $data = []
    ) {
        $this->simiObjectManager    = $simiObjectManager;
        $this->collectionFactory = $collectionFactory;
        $this->moduleManager      = $moduleManager;
        $this->resource          = $resourceConnection;
        $this->orderStatus       = $orderStatusCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('simipromoteappGrid');
        $this->setDefaultSort('simipromoteapp_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    public function _prepareColumns()
    {
        $this->addColumn('simipromoteapp_id', [
            'header' => __('ID'),
            'index'  => 'simipromoteapp_id',
        ]);

        $this->addColumn('customer_name', [
            'header' => __('Cusstomer Name'),
            'index'  => 'customer_name',
        ]);

        $this->addColumn('customer_email', [
            'header' => __('Customer Email'),
            'index'  => 'customer_email',
        ]);
        
        $this->addColumn('is_open', array(
            'header' => __('Open Email?'),
            'align'	 => 'left',
            'width'	 => '80px',
            'index'	 => 'is_open',
            'type'		=> 'options',
            'options'	 => array(
                    1 => 'Yes',
                    0 => 'No',
            ),
        ));
        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/simipromoteapp/grid', ['_current' => true]);
    }
}
