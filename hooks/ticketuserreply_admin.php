<?php
$hook = array(
    'hook' => 'TicketUserReply',
    'function' => 'TicketUserReply_admin',
    'description' => 'Executes when a reply is added to a ticket by an end user.<br>Client Related<br>First Name: {firstname}, Last Name: {lastname}, Email Address: {email}, Company Name: {companyname}, Credit Balance: {credit}, Country: {country}, Currency: {currency}<br>Support Ticket Related<br>Ticket Number: {tid}, Date Created: {date}, Subject: {title}, ID: {ticketid}, Message: {message}, Status: {status}, Priority: {urgency}, Department: {department}, Last Reply Message: {last_reply_message}, Last Reply Date: {last_reply_date}',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'User has replied on the ticket with the subject ({title})',
    'variables' => '{firstname}, {lastname},{email},{companyname},{credit},{country},{tid},{date},{title},{ticketid},{currency},{message},{status},{service},{lastreply},{urgency},{department},{last_reply_message},{last_reply_date}'
);

if(!function_exists('TicketUserReply_admin')){
    function TicketUserReply_admin($args){
        $api = new waclient();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_key'] || !$settings['api_token'] ){
            return null;
        }
        $admingsm = explode(",",$template['admingsm']);

        $result = $api->getClientAndTicketDetailsBy($args['ticketid']);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql=full_query('SELECT code FROM tblcurrencies WHERE id='.$UserInformation['currency']);
            $replace_currency="";
            if(mysql_num_rows($currency_sql) > 0){
                $currency_result=mysql_fetch_assoc($currency_sql);
                $replace_currency=$currency_result['code'];
            }
            $department_sql=full_query('SELECT name FROM tblticketdepartments WHERE id='.$UserInformation['did']);
            $replace_department="";
            if(mysql_num_rows($department_sql) > 0){
                $department_result=mysql_fetch_assoc($department_sql);
                $replace_department=$department_result['name'];
            }
            $reply_sql=full_query('SELECT date,message FROM tblticketreplies WHERE tid='.$args['ticketid'].' ORDER BY id DESC LIMIT 1');
            $replace_reply_message="";
            $replace_reply_date="";
            if(mysql_num_rows($reply_sql) > 0){
                $reply_result= mysql_fetch_assoc($reply_sql);
                $replace_reply_message= $reply_result['message'];
                $replace_reply_date= $reply_result['date'];
            }
            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['email'],$UserInformation['companyname'],$UserInformation['credit'],$UserInformation['country'],$UserInformation['tid'],$api->changeDateFormat($UserInformation['date']),$UserInformation['title'],$args['ticketid'],$replace_currency,$UserInformation['message'],$UserInformation['status'],$UserInformation['service'],$api->changeDateFormat($UserInformation['lastreply']),$UserInformation['urgency'],$replace_department,$replace_reply_message,$api->changeDateFormat($replace_reply_date));
            $message = str_replace($replacefrom,$replaceto,$template['template']);
    
            foreach($admingsm as $gsm){
                if(!empty($gsm)){
                    $api->setGsmnumber( trim($gsm));
                    $api->setUserid($args['userid']);
                    $api->setMessage($message);
                    $api->send();
                }
            }    
        }
    }
}

return $hook;
