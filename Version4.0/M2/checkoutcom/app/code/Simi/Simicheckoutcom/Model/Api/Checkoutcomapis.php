<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simicheckoutcom\Model\Api;

class Checkoutcomapis extends \Simi\Simiconnector\Model\Api\Apiabstract
{
    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    public function show() {
        $data = $this->getData();
        if ($data['resourceid'] == 'update_payment') {
            $info = $this->simiObjectManager->get('Simi\Simicheckoutcom\Model\Simicheckoutcom')->updatePayment((object) $data['params']);
            return $this->getDetail($info);
        } 
    }
}
