<?php
/**
 * Copyright 2012 Klarna AB
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Bootstrap file to include the klarna checkout library
 *
 * PHP version 5.3
 *
 * @category  Payment
 * @package   Klarna_Checkout
 * @author    Klarna <support@klarna.com>
 * @copyright 2012 Klarna AB
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache license v2.0
 * @link      http://developers.klarna.com/
 */

define('KLARNA_CHECKOUT_DIR', dirname(__FILE__) . '/Checkout');

require_once KLARNA_CHECKOUT_DIR . '/ConnectorInterface.php';
require_once KLARNA_CHECKOUT_DIR . '/ResourceInterface.php';
require_once KLARNA_CHECKOUT_DIR . '/Connector.php';
require_once KLARNA_CHECKOUT_DIR . '/BasicConnector.php';
require_once KLARNA_CHECKOUT_DIR . '/Order.php';
require_once KLARNA_CHECKOUT_DIR . '/Digest.php';
require_once KLARNA_CHECKOUT_DIR . '/Exception.php';
require_once KLARNA_CHECKOUT_DIR . '/ConnectionErrorException.php';
require_once KLARNA_CHECKOUT_DIR . '/ConnectorException.php';
require_once KLARNA_CHECKOUT_DIR . '/UserAgent.php';

require_once KLARNA_CHECKOUT_DIR . '/HTTP/TransportInterface.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLHandleInterface.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/Request.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/Response.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/Transport.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLTransport.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLHeaders.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLHandle.php';
require_once KLARNA_CHECKOUT_DIR . '/HTTP/CURLFactory.php';
pdate);
    } catch (Exception $e) {
        // Reset session
        $order = null;
        unset($_SESSION['klarna_checkout']);
    }
}

if ($order == null) {
    // Start new session
    $create['purchase_country'] = 'SE';
    $create['purchase_currency'] = 'SEK';
    $create['order_amount'] = 10000;
    $create['order_tax_amount'] = 2000;
    $create['locale'] = 'sv-se';
    $create['merchant']['id'] = $eid;
    $create['merchant']['terms_uri'] = 'http://example.com/terms.html';
    $create['merchant']['checkout_uri'] = 'http://example.com/checkout.php';
    $create['merchant']['confirmation_uri']
        = 'http://example.com/confirmation.php' .
        '?sid=123&klarna_order={checkout.order.uri}';
    // You can not receive push notification on non publicly available uri
    $create['merchant']['push_uri'] = 'http://example.com/push.php' .
        '?sid=123&klarna_order={checkout.order.uri}';
    $create['cart'] = array();

    foreach ($cart as $item) {
        $create['cart']['items'][] = $item;
    }

    $order = new Klarna_Checkout_Order($connector);
    $order->create($create);
    $order->fetch();
}

// Store location of checkout session
$_SESSION['klarna_checkout'] = $sessionId = $order->getLocation();

// Display checkout
$snippet = $order['gui']['snippet'];
// DESKTOP: Width of containing block shall be at least 750px
// MOBILE: Width of containing block shall be 100% of browser window (No
// padding or margin)
echo "<div>{$snippet}</div>";
