<?php

namespace Simi\Simirewardpoints\Block\Adminhtml\Customer\Tab;

use Magento\Customer\Controller\RegistryConstants;

class History extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Simi\Simirewardpoints\Model\Customer
     */
    protected $_rewardCustomer;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Simi\Simirewardpoints\Model\Transaction
     */
    protected $_transaction;

    /**
     * @var \Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction\Actions $actions
     */
    protected $_actions;

    /**
     * @var \Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction\Status $status
     */
    protected $_status;

    /**
     * @var \Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction\StoreView $storeView
     */
    protected $_storeView;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Simi\Simirewardpoints\Model\Customer $rewardCustomer
     * @param \Simi\Simirewardpoints\Model\Transaction $transaction
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Simi\Simirewardpoints\Model\Customer $rewardCustomer,
        \Simi\Simirewardpoints\Model\TransactionFactory $transaction,
        \Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction\Actions $actions,
        \Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction\Status $status,
        \Simi\Simirewardpoints\Ui\Component\Listing\Column\Transaction\StoreView $storeView,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_rewardCustomer = $rewardCustomer;
        $this->_transaction = $transaction;
        $this->_actions = $actions;
        $this->_status = $status;
        $this->_storeView = $storeView;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('transactionHistoryGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        }
        $collection = $this->_transaction->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transaction_id',
            'type' => 'number',
        ]);

        $this->addColumn('title', [
            'header' => __('Title'),
            'align' => 'left',
            'index' => 'title',
        ]);

        $this->addColumn('action', [
            'header' => __('Action'),
            'align' => 'left',
            'index' => 'action',
            'type' => 'options',
            'options' => $this->_actions->toOptionHash(),
        ]);

        $this->addColumn('point_amount', [
            'header' => __('Points'),
            'align' => 'right',
            'index' => 'point_amount',
            'type' => 'number',
        ]);

        $this->addColumn('point_used', [
            'header' => __('Points Used'),
            'align' => 'right',
            'index' => 'point_used',
            'type' => 'number',
        ]);

        $this->addColumn('created_time', [
            'header' => __('Created On'),
            'index' => 'created_time',
            'type' => 'datetime',
        ]);

        $this->addColumn('expiration_date', [
            'header' => __('Expires On'),
            'index' => 'expiration_date',
            'type' => 'datetime',
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => $this->_status->toOptionHash(),
        ]);

        $this->addColumn('store_id', [
            'header' => __('Store View'),
            'align' => 'left',
            'index' => 'store_id',
            'type' => 'options',
            'options' => $this->_storeView->toOptionHash(),
        ]);


        return parent::_prepareColumns();
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('simirewardpoints/transaction/view', ['id' => $row->getId()]);
    }

    /**
     * get grid url (use for ajax load)
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('simirewardpoints/customer/rewardhistorygrid', ['_current' => true]);
    }
}
