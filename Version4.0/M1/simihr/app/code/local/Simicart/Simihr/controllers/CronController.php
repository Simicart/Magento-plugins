<?php
class CronController {
    function sendMail() {
        include 'C:/xampp/htdocs/magento1/app/Mage.php';
        $job = Mage::getResourceModel('simicart_simihr/jobOffers_collection')
            ->addFieldToSelect('*')
            ->getData();
//        print_r($job[0]['start_time']);die();
        //  the message
        $msg = "cron sent mail";

        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg,70);

        // send email
        mail("hieu@simicart.com","CRON",$job[0]['start_time']);
    }
}
$temp = new CronController();
$temp->sendMail();
