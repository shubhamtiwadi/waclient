<?php
$hook = array(
    'hook' => 'AcceptOrder',
    'function' => 'AcceptOrder_whatsapp',
    'description' => 'Runs when an order is accepted prior to any acceptance actions being executed.<br>Client Related<br>User ID: {userid}, First Name: {firstname}, Last Name: {lastname}<br>Order Related<br>Order Id: {orderid}, Order Number: {ordernum}, Date Created: {date}, Nameservers: {nameservers}, Amount: {amount}, Order Status: {status}',
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Dear {firstname} {lastname}, Your order associated with the ID {orderid} has been approved.',
    'variables' => '{userid}, {firstname}, {lastname}, {orderid}, {ordernum}, {date}, {nameservers}, {amount}, {status}'
);
if(!function_exists('AcceptOrder_whatsapp')){
    function AcceptOrder_whatsapp($args){

        $api = new waclient();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings(); 

        if(!$settings['api_key'] || !$settings['api_token'] ){
            return null;
        }


        $userSql = "SELECT a.ordernum,a.date, a.nameservers, a.amount, a.status, b.id as userid,b.firstname,b.lastname,`b`.`country`,b.credit,b.email,b.companyname,b.currency,`b`.`phonenumber` as `gsmnumber` FROM `tblorders` as `a`
        JOIN tblclients as b ON b.id = a.userid
        WHERE a.id = '".$args['orderid']."'
        LIMIT 1";
        


        $result = full_query($userSql);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);
            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['userid'],$UserInformation['firstname'],$UserInformation['lastname'],$args['orderid'], $UserInformation['ordernum'],$api->changeDateFormat($UserInformation['date']),$UserInformation['nameservers'],$UserInformation['amount'],$UserInformation['status']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);
            
            $gsmnumber = $UserInformation['gsmnumber'];
            if($settings['gsmnumberfield'] > 0){
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }

            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setUserid($UserInformation['userid']);
            $api->setMessage($message);
            $api->send();
        }
    }
}

return $hook;