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
    "secret_key" => $_POST["secret_key"],
    "open_key"   => $_POST["open_key"]
);


/* convert 10.00 AED to cents */
$amount_in_cents = $_POST["value"]*100;
$currency = $_POST["currency"];
$customer_email = $_POST["user_email"];
$command = $_POST["command"];
$successurl = $_POST["successurl"];
?>

<form id="myForm" action="./Charge.php" method="post">
    <script src="https://beautiful.start.payfort.com/checkout.js"
        data-key="<?php echo $api_keys['open_key']; ?>"
        data-currency="<?php echo $currency ?>"
        data-amount="<?php echo $amount_in_cents ?>"
        data-email="<?php echo $customer_email ?>">
  </script>
  <input type="hidden" name="secret_key" value="<?php echo $api_keys['secret_key'] ?>">
  <input type="hidden" name="open_key" value="<?php echo $api_keys['open_key'] ?>">
  <input type="hidden" name="amount_in_cents" value="<?php echo $amount_in_cents ?>">
  <input type="hidden" name="command" value="<?php echo $command ?>">
  <input type="hidden" name="currency" value="<?php echo $currency ?>">
  <input type="hidden" name="customer_email" value="<?php echo $customer_email ?>">
  <input type="hidden" name="successurl" value="<?php echo $successurl ?>">
</form>
<script type="text/javascript">
    function showPopUp() {
        var button = document.getElementsByClassName("start-js-btn");
        button[0].click();
    }
    window.onload = showPopUp;
</script>