<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simibarclays\Controller\Index;

class Placement extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $simiObjectManager = $this->_objectManager;
        $orderId = $this->getRequest()->getParam('OrderId');
        $orderModel = $simiObjectManager->get('\Magento\Sales\Model\Order')->getCollection()
            ->addAttributeToFilter('increment_id', $orderId)
            ->getFirstItem();
        if(!$orderModel->getId()) {
            echo 'No Order Found!';
            die;
        }

        $now = strtotime('now');
        $orderModelData = $orderModel->getData();

        $transaction = $simiObjectManager->create('Simi\Simibarclays\Model\Simibarclays')->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();
        if (!$transaction->getId()) {
            $transaction->setData('order_id', $orderId);
            $transaction->setData('token', md5($now.rand(1000000,9999999)));
            $transaction->setData('amount', floatval($orderModelData['grand_total']));
            $transaction->setData('currency_code', $orderModelData['order_currency_code']);
            $transaction->setData('status', 'pending');
            $transaction->save();
        }
        $successUrl = $simiObjectManager->get('Magento\Framework\UrlInterface')
                        ->getUrl('simibarclays/index/success',
                            array(
                                '_secure' => true,
                                'orderId' => $orderId,
                                'token' => $transaction->getData('token'),
                            )
                        );
        $failureUrl = $simiObjectManager->get('Magento\Framework\UrlInterface')
            ->getUrl('simibarclays/index/failure',
                array(
                    '_secure' => true,
                    'orderId' => $orderId
                )
            );

        $customer = array(
            'name' => $orderModelData['customer_firstname']. ' '. $orderModelData['customer_lastname'],
            'email' => $orderModelData['customer_email']
        );

        $order = array(
            'amount' => floatval($orderModelData['grand_total']),
            'orderid' => $orderId,
            'order_currency_code' => $orderModelData['order_currency_code'],
        );

        $formParams = array(
            'ORDERID' => $order['orderid'],
            'AMOUNT' => round($order['amount'] * 100),
            'CURRENCY' => $order['order_currency_code'],
            'ACCEPTURL'=>$successUrl,
            'DECLINEURL'=>$failureUrl,
            'EXCEPTIONURL'=>$failureUrl,
            'CANCELURL'=>$failureUrl,
            'BACKURL'=>$failureUrl,

            'CN' => $customer['name'],
            'EMAIL' => $customer['email'],

            'TITLE' => 'Notorious EPDQ Form',

            'LOGO' => 'https://www.harlowbros.co.uk/assets/images/all-pages/logo.jpg',
            'BUTTONBGCOLOR' => '802626',
            'BUTTONTXTCOLOR' => 'FFFFFF',

            /* You can customise more if you want (but you pay for completely custom templates)
            'BGCOLOR' => 'FFFFFF',
            'TXTCOLOR' => '000000',
            'TBLBGCOLOR' => 'FFFFFF',
            'TBLTXTCOLOR' => '000000',
            'FONTTYPE' => ''
            */

        );

        $helper = $simiObjectManager->get('Simi\Simibarclays\Helper\Data');

        $barclaycardEpdq = new \Simi\Simibarclays\Block\BarclaycardEpdq();
        $barclaycardEpdq->mPSPID = $helper->getStoreConfig('payment/simibarclays/pspid');
        $barclaycardEpdq->mPassword = $helper->getStoreConfig('payment/simibarclays/shain_pass');
        $barclaycardEpdq->mURLTest = $helper->getStoreConfig('payment/simibarclays/sandbox_url');
        $barclaycardEpdq->mURLProduction = $helper->getStoreConfig('payment/simibarclays/live_url');
        $barclaycardEpdq->mTestMode = $helper->getStoreConfig('payment/simibarclays/is_sandbox')?true:false;

        $barclaycardEpdq->outputForm($formParams);
        exit();
    }
}
