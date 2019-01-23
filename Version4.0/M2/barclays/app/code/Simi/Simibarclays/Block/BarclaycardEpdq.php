<?php


namespace Simi\Simibarclays\Block;

class BarclaycardEpdq {


    /* Get these from your account area */
    // account no / PSPID
    public $mPSPID = 'HarlowBros';
    // SHA-IN pass phrase
    public $mPassword = '2e2dcec0-3345-482a-960f-febd08883439';


    // static configs
    public $mURLTest = 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp';
    public $mURLProduction = 'https://payments.epdq.co.uk/ncol/prod/orderstandard.asp';

    // set this to false once you've run some tests
    public $mTestMode = true;

    //private $mCurrency = 'GBP';
    public $mLanguage = 'en_US';

    private $mGenerateHash = false;
    private $mFormParams = array();

    private $mFailReason = false;

    private $mNotificationEmails = array('merchant@tapita.com');

    /*
        Builds a SHA1 hash of order info
    */
    function generateHash(){
        $this->generateHash = true;

        ksort($this->mFormParams);

        $out = array();
        foreach($this->mFormParams as $key => $param){
            $out[] = strtoupper($key) . "=" . $param;
        }
        $out = implode($this->mPassword, $out) . $this->mPassword;

        $this->mFormParams['SHASIGN'] = strtoupper(hash('SHA256', $out));
    }

    /*
        Builds a form for submitting to Barclaycard
    */
    function outputForm($formParams = array()){
        $formParams['PSPID'] = $this->mPSPID;
        //$formParams['CURRENCY'] = $this->mCurrency;
        $formParams['LANGUAGE'] = $this->mLanguage;

        $this->mFormParams = $formParams;

        $this->cleanParams();
        $this->generateHash();

        if(!$this->generateHash){
            echo 'You must generate the hash before outputting the form';
            return;
        }

        echo '<form method="post" id="barclays-payment-form" action="' . (($this->mTestMode) ? $this->mURLTest : $this->mURLProduction) . '">';

        foreach($this->mFormParams as $key => $param){
            echo '<input type="hidden" name="' . strtoupper($key) . '" value="' . $param . '" />' . "\n";
        }

        echo '<input type="submit" value="Pay by Barclaycard EPDQ" style="display: none"/>';
        echo '
          <script type="text/javascript">
                document.getElementById("barclays-payment-form").submit(); 
          </script>
        ';
        echo '</form>';

    }

    function cleanParams(){
        foreach($this->mFormParams as $key => $param){
            if(strlen($param) == 0){
                unset($this->mFormParams[$key]);
            }
        }
    }

    /*
    Handles Barclaycard respose (GET or POST)
    If updateOrder is set to true, paymentSuccess and paymentFailed are called

    Response contains:
        ORDERID Your order reference
        AMOUNT Order amount (not multiplied by 100)
        CURRENCY Currency of the order
        PM Payment method
        ACCEPTANCE Acceptance code returned by acquirer
        STATUS Transaction status
        CARDNO Masked card number
        PAYID Payment reference in our system
        NCERROR Error code
        BRAND Card brand (our system derives it from the card number) or similar
        information for other payment methods.
        SHASIGN SHA signature composed by our system, if SHA-1-OUT is configured by you.
    */
    function handleResponse($response, $updateOrder){

        if(!isset($response['orderID'])){
            return false;
        }

        if(!isset($response['STATUS'])){
            return false;
        }

        $cartRef = $response['orderID'];
        $status = $response['STATUS'];

        if($status == '5'){
            if($updateOrder){
                $this->paymentSuccess($cartRef, $response['PAYID']);
            }
            return true;
        }

        $this->mFailReason = "Unknown payment error occurred";
        $this->mWarning = false;

        switch($status){
            case "0":
                "Some required details were missing - " . $response['NCERROR'];
                break;
            case "1":
            case "6":
            case "64":
                "You cancelled the transaction";
                break;
            case "84":
            case "93":
                $this->mFailReason = "Payment refused";
                break;
            case "2":
            case "52":
                $this->mFailReason = "Unable to authorise";
                break;
            case "4":
            case "9":
            case "40":
            case "91":
            case "50":
            case "51":
            case "52":
            case "59":
            case "92":
            case "95":
            case "99":
                $this->mWarning = true;
                $this->mFailReason = "There was a delay processing this transaction. We'll update you when your order is successful, or be in contact if there is an issue with the payment";
                break;
            case "93":
                $this->mFailReason = "Barclaycard technical issue - please retry your payment at a later date";
                break;
        }

        if($updateOrder){
            $this->paymentFailed($cartRef);
        }

        return false;

    }

    /*
        Payment failed, overide with your code to handle fail
        This is only called if the handleResponse method is called with updateOrder set to true
    */
    function paymentFailed($cartRef){
        payment_failed('Failed', 'barclaycard', $cartRef);
    }

    /*
        Payment success, overide with your code to handle success
        This is only called if the handleResponse method is called with updateOrder set to true
    */
    function paymentSuccess($cartRef, $payID){

        global $CONFIG;

        payment_success($CONFIG, 'barclaycard', $cartRef, $payID);
    }

    function getFailReason(){
        return $this->mFailReason;
    }
}

?>