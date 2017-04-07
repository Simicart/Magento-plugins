<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Paypalmobile\Model\Api;

use Simi\Simiconnector\Model\Api\Apiabstract;

class Paypalmobiles extends Apiabstract {

    public function setBuilderQuery() {
        $this->builderQuery = $this->simiObjectManager->get('Simi\Paypalmobile\Model\Paypalmobile');
    }

    /**
     * @return override
     */
    public function store() {
        $data = $this->getData();
        $content = $data['contents'];
        $paypal = $this->builderQuery->updatePaypalPaymentv2($content);
        $detail = array();
        if (isset($paypal['order'])) {
            $entity = $paypal['order'];
            $info = $entity->toArray();
            $detail = $this->getDetail($info);
        }
        if (isset($paypal['message']))
            $detail['message'] = $paypal['message'];
        return $this->getDetail($detail);
    }

}
