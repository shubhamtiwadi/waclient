<?php
$hook = array(
    'hook' => 'DailyCronJob',
    'function' => 'DomainRenewalNotice',
    'description' => 'Domain Renewal Notice before {x} days.<br>Client Related<br>User ID: {userid}, First Name: {firstname}, Last Name: {lastname}, Currency: {currency}<br>Service Related<br>Domain: {domain}, Registration Date: {registrationdate}, First Payment Amount: {firstpaymentamount}, Recurring Amount: {recurringamount}, Registrar: {registrar}, Next Duedate: {nextduedate}, Next Invoice Date: {nextinvoicedate}, Payment Method: {paymentmethod}, Expiry Date: {expirydate}, Number of Days: {x}',
    'type' => 'client',
    'extra' => '15',
    'defaultmessage' => 'Hi {firstname} {lastname}, your domain- {domain} will expire in {x} days i.e. on {expirydate} . Kindly visit site  to renew it. Thank You!',
    'variables' => '{userid}, {firstname}, {lastname}, {domain}, {registrationdate}, {firstpaymentamount}, {recurringamount}, {registrar}, {nextduedate}, {nextinvoicedate}, {paymentmethod}, {expirydate}, {x}, {currency}'
);
if(!function_exists('DomainRenewalNotice')){
    function DomainRenewalNotice($args){

        $api = new waclient();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_key'] || !$settings['api_token'] ){
            return null;
        }

        $extra = $template['extra'];
        $sqlDomain = "SELECT  `userid` ,  `domain` ,  `expirydate`, registrationdate, firstpaymentamount, recurringamount, registrar, nextduedate, nextinvoicedate, paymentmethod
           FROM  `tbldomains`
           WHERE  `status` =  'Active'";
        $resultDomain = full_query($sqlDomain);
        while ($data = mysql_fetch_array($resultDomain)) {
            $tarih = explode("-",$data['expirydate']);
            $yesterday = mktime (0, 0, 0, $tarih[1], $tarih[2] - $extra, $tarih[0]);
            $today = date("Y-m-d");
            if (date('Y-m-d', $yesterday) == $today){
                $result = $api->getClientDetailsBy($data['userid']);
                $num_rows = mysql_num_rows($result);
                if($num_rows == 1){
                    $UserInformation = mysql_fetch_assoc($result);
                    $currency_sql=full_query('SELECT code FROM tblcurrencies WHERE id='.$UserInformation['currency']);
                    $replace_currency="";
                    if(mysql_num_rows($currency_sql) > 0){
                        $currency_result=mysql_fetch_assoc($currency_sql);
                        $replace_currency=$currency_result['code'];
                    }
                    $template['variables'] = str_replace(" ","",$template['variables']);
                    $replacefrom = explode(",",$template['variables']);
                    $replaceto = array($UserInformation['userid'],$UserInformation['firstname'],$UserInformation['lastname'],$data['domain'],$data['paymentmethod'],$api->changeDateFormat($data['registrationdate']),$data['firstpaymentamount'],$data['recurringamount'],$data['registrar'],$api->changeDateFormat($data['nextduedate']),$api->changeDateFormat($data['nextinvoicedate']),$api->changeDateFormat($data['expirydate']),$extra,$replace_currency);
                    $message = str_replace($replacefrom,$replaceto,$template['template']);
                    $gsmnumber = $UserInformation['gsmnumber'];
                    if($settings['gsmnumberfield'] > 0){
                        $gsmnumber = $api->customfieldsvalues($data['userid'], $settings['gsmnumberfield']);
                    }
        
                    $api->setCountryCode($UserInformation['country']);
                    $api->setGsmnumber($gsmnumber);
                    $api->setMessage($message);
                    $api->setUserid($data['userid']);
                    $api->send();
                }
            }
        }
    }
}
return $hook;