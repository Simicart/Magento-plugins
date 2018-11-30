<?php

namespace Simi\Simipushnotif\Block\Adminhtml\Device;

use \Magento\Backend\Block\Widget\Grid\Extended;

class Grid extends Extended
{

    /**
     * @var \Simi\Simipushnotif\Model\Device
     */
    public $deviceFactory;

    /**
     * @var \Simi\Simipushnotif\Model\ResourceModel\Device\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    public $moduleManager;

    /**
     * @var order model
     */
    public $resource;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Simi\Simipushnotif\Model\DeviceFactory $deviceFactory
     * @param \Simi\Simipushnotif\Model\ResourceModel\Device\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simipushnotif\Model\DeviceFactory $deviceFactory,
        \Simi\Simipushnotif\Model\ResourceModel\Device\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
    

        $this->collectionFactory = $collectionFactory;
        $this->moduleManager = $moduleManager;
        $this->resource = $resourceConnection;
        $this->deviceFactory = $deviceFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('deviceGrid');
        $this->setDefaultSort('agent_id');
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
     * @return $this
     * @throws \Exception
     */
    public function _prepareColumns()
    {
        $this->addColumn('device_id', [
            'header' => __('ID'),
            'index' => 'device_id',
        ]);

        $this->addColumn('city', [
            'header' => __('City'),
            'index' => 'city',
        ]);

        $this->addColumn('country', [
            'type' => 'options',
            'header' => __('Country'),
            'index' => 'country',
            'options' => $this->deviceFactory->create()->toOptionCountryHash(),
        ]);

        $this->addColumn('created_at', [
            'type' => 'datetime',
            'header' => __('Created Date'),
            'index' => 'created_at',
        ]);

        $this->addColumn(
            'action',
            [
                'header' => __('View'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View Device'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'device_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', [
            'device_id' => $row->getId()
        ]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function _prepareMassaction()
    {
        $this->setMassactionIdField('device_id');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
        return $this;
    }
}
