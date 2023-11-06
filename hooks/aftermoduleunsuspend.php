<?php
$hook = array(
    'hook' => 'AfterModuleUnsuspend',
    'function' => 'AfterModuleUnsuspend',
    'description' => 'Executes upon successful completion of the module function.<br>Client Related<br>User ID: {userid}, First Name: {firstname}, Last Name: {lastname}<br>Service Related<br>Domain: {domain}',
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => ' Hello! The services for the domain ({domain}) have now been made active.',
    'variables' => '{userid}, {firstname}, {lastname}, {product}, {domain}'
);
if(!function_exists('AfterModuleUnsuspend')){
    function AfterModuleUnsuspend($args){
        $type = $args['params']['producttype'];

        if($type == "hostingaccount"){
            $api = new waclient();
            $template = $api->getTemplateDetails(__FUNCTION__);
            if($template['active'] == 0){
                return null;
            }
            $settings = $api->apiSettings();
            if(!$settings['api_key'] || !$settings['api_token']){
                return null;
            }
        }else{
            return null;
        }

        $result = $api->getClientDetailsBy($args['params']['clientsdetails']['userid']);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);

            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['userid'],$UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['product'],$args['params']['domain']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if($settings['gsmnumberfield'] > 0){
                $gsmnumber = $api->customfieldsvalues($args['params']['clientsdetails']['userid'], $settings['gsmnumberfield']);
            }

            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setUserid($args['params']['clientsdetails']['userid']);
            $api->setMessage($message);
            $api->send();
        }
    }
}
return $hook;