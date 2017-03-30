<?php

/**
 * RewardPoints Action Library Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper;

class Action extends Config
{

    const XML_CONFIG_ACTIONS = 'global/simirewardpoints/actions';

    /**
     * reward points actions config
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Actions Array (code => label)
     *
     * @var array
     */
    protected $_actions = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $actionConfig = [
            // Admin - Change by Custom
            "admin" => "\\Simi\Simirewardpoints\\Model\\Action\\Admin",
            // Sales - Earning Actions
            "earning_invoice" => "\\Simi\Simirewardpoints\\Model\\Action\\Earning\\Invoice",
            "earning_creditmemo" => "\\Simi\Simirewardpoints\\Model\\Action\\Earning\\Creditmemo",
            "earning_cancel" => "\\Simi\Simirewardpoints\\Model\\Action\\Earning\\Cancel",
            // Sales - Spending Actions
            "spending_order" => "\\Simi\Simirewardpoints\\Model\\Action\\Spending\\Order",
            "spending_creditmemo" => "\\Simi\Simirewardpoints\\Model\\Action\\Spending\\Creditmemo",
            "spending_cancel" => "\\Simi\Simirewardpoints\\Model\\Action\\Spending\\Cancel",
        ];
        $this->_eventManager->dispatch(
            'action_config_simirewardpoints',
            ['object' => $this]
        );
        foreach ($actionConfig as $code => $model) {
            $this->_config[$code] = (string) $model;
        }
        $this->messageManager = $messageManager;
    }

    /**
     * add action config
     *
     * @param array $config;
     * @return $this
     */
    public function setActionConfig($configs = [])
    {
        foreach ($configs as $code => $model) {
            $this->_config[$code] = (string) $model;
        }
        return $this;
    }

    /**
     * Add transaction that change customer reward point balance
     *
     * @param string $actionCode
     * @param Mage_Customer_Model_Customer $customer
     * @param type $object
     * @param array $extraContent
     * @return \Simi\Simirewardpoints\Model\Transaction
     */
    public function addTransaction($actionCode, $customer, $object = null, $extraContent = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
//        \Varien_Profiler::start('REWARDPOINTS_HELPER_ACTION::addTransaction');
        if (!$customer->getId()) {
            $this->messageManager->addError(
                __('Customer must be existed.')
            );
        }
        $actionModel = $this->getActionModel($actionCode);
        $actionModel->setData([
            'customer' => $customer,
            'action_object' => $object,
            'extra_content' => $extraContent
        ])->prepareTransaction();

        $transaction = $objectManager->create('Simi\Simirewardpoints\Model\Transaction');
        if (is_array($actionModel->getData('transaction_data'))) {
            $transaction->setData($actionModel->getData('transaction_data'));
        }
        $transaction->setData('point_amount', (int) $actionModel->getPointAmount());

        if (!$transaction->hasData('store_id')) {
            $transaction->setData('store_id', $storeManager->getStore()->getId());
        }

        $transaction->createTransaction([
            'customer_id' => $customer->getId(),
            'customer' => $customer,
            'customer_email' => $customer->getEmail(),
            'title' => $actionModel->getTitle(),
            'action' => $actionCode,
            'action_type' => $actionModel->getActionType(),
            'created_time' => date('Y-m-d H:i:s'),
            'updated_time' => date('Y-m-d H:i:s'),
        ]);

//        Varien_Profiler::stop('REWARDPOINTS_HELPER_ACTION::addTransaction');
        return $transaction;
    }

    /**
     * get Class Model for Action by code
     *
     * @param string $code
     * @return string
     * @throws Exception
     */
    public function getActionModelClass($code)
    {
        if (isset($this->_config[$code]) && $this->_config[$code]) {
            return $this->_config[$code];
        }
        $this->messageManager->addError(
            __('Action code %1 not found on config.', $code)
        );
    }

    /**
     * get action Model by Code
     *
     * @param string $code
     * @return \Simi\Simirewardpoints\Model\Action\Interface
     * @throws Exception
     */
    public function getActionModel($code)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $modelClass = $this->getActionModelClass($code);
        $model = $objectManager->create($modelClass);
        if (!$model->getCode()) {
            $model->setCode($code);
        }
        return $model;
    }

    /**
     * get actions hash options
     *
     * @return array
     */
    public function getActionsHash()
    {
        if (is_null($this->_actions)) {
            $this->_actions = [];
            foreach ($this->_config as $code => $class) {
                try {
                    $model = $this->getActionModel($code);
                    $this->_actions[$code] = $model->getActionLabel();
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        return $this->_actions;
    }

    /**
     * get actions array options
     *
     * @return array
     */
    public function getActionsArray()
    {
        $actions = [];
        foreach ($this->getActionsHash() as $value => $label) {
            $actions[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $actions;
    }
}
