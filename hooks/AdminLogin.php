<?php
$hook = array(
    'hook' => 'AdminLogin',
    'function' => 'AdminLogin_admin',
    'description' => 'Executes post successful authentication of an admin user.<br>Client Related<br>Username: {username}',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'A user with the username {username} has entered the admin panel.',
    'variables' => '{username}'
);
if(!function_exists('AdminLogin_admin')){
    function AdminLogin_admin($args){
        $api = new waclient();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_key'] || !$settings['api_token']){
            return null;
        }

        $admingsm = explode(",",$template['admingsm']);
        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($args['username']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $api->setGsmnumber( trim($gsm));
                $api->setUserid($args['adminid']);
                $api->setMessage($message);
                $api->send();
            }
        }
    }
}

return $hook;
