<?php
/**
 * Created by PhpStorm.
 * User: macos
 * Date: 11/16/18
 * Time: 1:56 PM
 */

namespace Simi\Simipushnotif\Block\Adminhtml\Notification;
use \Magento\Backend\Block\Widget\Grid\Extended;


class Grid extends Extended
{
    /**
     * @var \Simi\Simipushnotif\Model\Notification
     */
    public $notificationFactory;

    /**
     * @var \Simi\Simipushnotif\Model\ResourceModel\Notification\CollectionFactory
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
     * @param \Simi\Simipushnotif\Model\DeviceFactory $notificationFactory
     * @param \Simi\Simipushnotif\Model\ResourceModel\Notification\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simipushnotif\Model\NotificationFactory $notificationFactory,
        \Simi\Simipushnotif\Model\ResourceModel\Notification\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {


        $this->collectionFactory = $collectionFactory;
        $this->moduleManager = $moduleManager;
        $this->resource = $resourceConnection;
        $this->notificationFactory = $notificationFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('messageGrid');
        $this->setDefaultSort('message_id');
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
        $this->addColumn('message_id', [
            'header' => __('ID'),
            'index' => 'message_id',
        ]);

        $this->addColumn('notice_title', [
            'header' => __('Message Title'),
            'index' => 'notice_title',
        ]);

        $this->addColumn('notice_content', [
            'header' => __('Message Content'),
            'index' => 'notice_content',
        ]);

        $this->addColumn('created_time', [
            'type' => 'datetime',
            'header' => __('Created Date'),
            'index' => 'created_time',
        ]);

        $this->addColumn('device_id', [
            'header' => __('Device Ids'),
            'index' => 'device_id',
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enable',
                2 => 'Disable'
            )
        ]);

        $this->addColumn(
            'action',
            [
                'header' => __('View'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'message_id'
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
            'message_id' => $row->getId()
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
        $this->setMassactionIdField('message_id');
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