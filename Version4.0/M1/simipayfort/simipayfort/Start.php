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


/* convert 10.00 AED to cents */
$amount_in_cents = $_GET["value"];
$currency = $_GET["currency"];
$customer_email = $_GET["user_email"];

?>

<form id="myForm" action="./charge.php?value=10&currency=AED&user_email=cody@simicart.com" method="post">
    <script src="https://beautiful.start.payfort.com/checkout.js"
        data-key="<?php echo $api_keys['open_key']; ?>"
        data-currency="<?php echo $currency ?>"
        data-amount="<?php echo $amount_in_cents ?>"
        data-email="<?php echo $customer_email ?>">
  </script>
</form>
<script type="text/javascript">
    function showPopUp() {
        var button = document.getElementsByClassName("start-js-btn");
        button[0].click();
    }
    window.onload = showPopUp;
</script>