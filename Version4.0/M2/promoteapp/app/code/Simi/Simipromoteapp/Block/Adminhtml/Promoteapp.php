<?php

/**
 * Adminhtml connector list block
 *
 */

namespace Simi\Simipromoteapp\Block\Adminhtml;

class Promoteapp extends \Magento\Backend\Block\Widget\Grid\Extended
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
    
    public function isChartEnabled()
    {
        return $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Chart')->isEnable();
    }
    

    public function getFirstDateOfMonth()
    {
        return $this->getHelperDateTime()->getFirstDateOfCurrentMonth();
    }

    public function getLastDateOfMonth()
    {
        return $this->getHelperDateTime()->getLastDateOfCurrentMonth();
    }

    public function getHelperDateTime()
    {
        return $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Datetime');
    }

    public function getHelperChart()
    {
        return $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Chart');
    }

    public function getTextByApp()
    {
        return __($this->getHelperChart()->getTextByApp());
    }

    public function getTextByWebsite()
    {
        return __($this->getHelperChart()->getTextByWebsite());
    }

    public function getChartTitle()
    {
        return __($this->getHelperChart()->getChartTitle());
    }

    public function getPercent()
    {
        return __($this->getHelperChart()->getPercent());
    }
    
    public function getReportHelper()
    {
        return $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Report');
    }

}
