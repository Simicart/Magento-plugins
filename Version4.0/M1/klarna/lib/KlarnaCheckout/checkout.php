<?php

/**
 * Copyright 2015 Klarna AB
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
 * Example of a checkout page
 *
 * PHP version 5.3.4
 *
 * @category  Payment
 * @package   Klarna_Checkout
 * @author    Klarna <support@klarna.com>
 * @copyright 2015 Klarna AB
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache license v2.0
 * @link      http://developers.klarna.com/
 */
require_once 'Checkoutlib.php';

session_start();

$order = null;
$sharedSecret = Mage::registry('klarna_secret');

if (Mage::registry('klarna_is_test') == '1')
    $connector = Klarna_Checkout_Connector::create(
                    $sharedSecret, Klarna_Checkout_Connector::BASE_URL
    );
else
    $connector = Klarna_Checkout_Connector::create(
                    $sharedSecret, Klarna_Checkout_Connector::BASE_TEST_URL
    );
$data = Mage::registry('simicart_data');
$cart = $data->simiklarnaapi;
$cart = $cart->params;
// Start new session
$create = Mage::registry('klarna_checkout_data');
foreach ($cart as $item) {
    $create['cart']['items'][] = (array) $item;
}

if (Mage::getSingleton('checkout/session')->getSimiKlarnaCheckoutId()) {
    // Resume session
    $order = 1;
}
if ($order == null) {
    $order = new Klarna_Checkout_Order($connector);
    try {
        $order->create($create);
        $order->fetch();
        // Store location of checkout session
    } catch (Klarna_Checkout_ApiErrorException $e) {
        var_dump($e->getMessage());
        var_dump($e->getPayload());
        die;
    }
	$_SESSION['klarna_order_id'] = $sessionID = $order['id'];
	Mage::getSingleton('checkout/session')->setSimiKlarnaCheckoutId($sessionID);
}

if (isset($order['gui']['snippet'])) {
    // Display checkout
    $snippet = $order['gui']['snippet'];
    // DESKTOP: Width of containing block shall be at least 750px
    // MOBILE: Width of containing block shall be 100% of browser window (No
    // padding or margin)
    echo "<div>{$snippet}</div>";
}
