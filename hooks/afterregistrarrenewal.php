<?php
$hook = array(
    'hook' => 'AfterRegistrarRenewal',
    'function' => 'AfterRegistrarRenewal',
    'description' => 'Executes after a successful domain renewal command.<br>Client Related<br>User ID: {userid}, First Name: {firstname}, Last Name: {lastname}<br>Service Related<br>Domain: {domain}',
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Dear {firstname} {lastname}, Your domain {domain} is successfully renewed.',
    'variables' => '{userid}, {firstname}, {lastname}, {domain}'
);
if(!function_exists('AfterRegistrarRenewal')){
    function AfterRegistrarRenewal($args){
    $api = new waclient();
    $template = $api->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $api->apiSettings();
    if(!$settings['api_key'] || !$settings['api_token'] ){
        return null;
    }

    $result = $api->getClientDetailsBy($args['params']['userid']);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);

        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['userid'],$UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);
        $gsmnumber = $UserInformation['gsmnumber'];
        if($settings['gsmnumberfield'] > 0){
                $gsmnumber = $api->customfieldsvalues($args['params']['userid'], $settings['gsmnumberfield']);
        }

        $api->setCountryCode($UserInformation['country']);
        $api->setGsmnumber($gsmnumber);
        $api->setUserid($args['params']['userid']);
        $api->setMessage($message);
        $api->send();
    }

}
}

return $hook;