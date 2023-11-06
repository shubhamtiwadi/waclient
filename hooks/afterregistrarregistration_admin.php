<?php
$hook = array(
    'hook' => 'AfterRegistrarRegistration',
    'function' => 'AfterRegistrarRegistration_admin',
    'description' => 'Executes after a successful domain register command.<br>Service Related<br>Domain: {domain}',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'New domain named {domain} have been registered.',
    'variables' => '{domain}'
);
if(!function_exists('AfterRegistrarRegistration_admin')){
    function AfterRegistrarRegistration_admin($args){
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

        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $api->setGsmnumber( trim($gsm));
                $api->setUserid(0);
                $api->setMessage($message);
                $api->send();
            }
        }
    }
}

return $hook;