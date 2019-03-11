<?php

/**
 * Connector data helper
 */

namespace Simi\Simibarclays\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Simi\Simiconnector\Helper\Data
{
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function updateTransaction($orderId, $token, $status) {

    }
}
