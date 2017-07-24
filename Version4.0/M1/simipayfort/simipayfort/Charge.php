<?php

//File to use when using lib without composer.
require_once dirname(__FILE__) . '/src/Start.php';
require_once dirname(__FILE__) . '/src/Start/Net/Curl.php';
require_once dirname(__FILE__) . '/src/Start/Net/Stream.php';
require_once dirname(__FILE__) . '/src/Start/Request.php';
require_once dirname(__FILE__) . '/src/Start/Charge.php';
require_once dirname(__FILE__) . '/src/Start/Capture.php';
require_once dirname(__FILE__) . '/src/Start/Refund.php';
require_once dirname(__FILE__) . '/src/Start/Error.php';
require_once dirname(__FILE__) . '/src/Start/Customer.php';
require_once dirname(__FILE__) . '/src/Start/Error/Authentication.php';
require_once dirname(__FILE__) . '/src/Start/Error/Banking.php';
require_once dirname(__FILE__) . '/src/Start/Error/Processing.php';
require_once dirname(__FILE__) . '/src/Start/Error/Request.php';
require_once dirname(__FILE__) . '/src/Start/Error/SSLError.php';



$api_keys = array(
    "secret_key" => "test_sec_k_020d8c9d205845873ba88",
    "open_key"   => "test_open_k_b40fa0f5355fd17064fc"
);

$amount_in_cents = $_POST["amount_in_cents"];
$currency = $_POST["currency"];
$customer_email = $_POST["customer_email"];
$command = $_POST["command"];
$successurl = $_POST["successurl"];

# Read the fields that were automatically submitted by beautiful.js
$token = $_POST["startToken"];
$email = $_POST["startEmail"];

# Setup the Start object with your private API key
Start::setApiKey($api_keys["secret_key"]);
# Process the charge
try {
    $charge = Start_Charge::create(array(
        "amount"      => $amount_in_cents,
        "currency"    => $currency,
        "card"        => $token,
        "email"       => $email,
        "ip"          => $_SERVER["REMOTE_ADDR"],
        "description" => $command
    ));

    echo "<h1>Successfully charged 10.00 AED</h1>";
    echo "<p>Charge ID: ".$charge["id"]."</p>";
    echo "<p>Charge State: ".$charge["state"]."</p>";
    echo "<script> window.location = '".$successurl."';</script>";
} catch (Start_Error $e) {
    $error_code = $e->getErrorCode();
    $error_message = $e->getMessage();

    /* depending on $error_code we can show different messages */
    if ($error_code === "card_declined") {
        echo "<h1>Charge was declined</h1>";
    } else {
        echo "<h1>Charge was not processed</h1>";
    }
    echo "<p>".$error_message."</p>";
}

?>

<a href="index.php">Try Again!</a>