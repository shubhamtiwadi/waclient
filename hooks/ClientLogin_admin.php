<?php
        use WHMCS\Database\Capsule;

$hook = array(
    'hook' => 'UserLogin',
    'function' => 'UserLogin_admin',
    'description' => 'Executes when a user logs in.<br>Client Related<br>First Name: {firstname}, Last Name: {lastname}, Email: {email}',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'User with the name- {firstname} {lastname} logged in to the site.',
    'variables' => '{firstname},{lastname},{email}'
);

if(!function_exists('ClientLogin_admin')){
    function ClientLogin_admin($args){
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
        

        $result = $api->getClientDetailsBy(\WHMCS\Session::get("uid"));
        $num_rows = mysql_num_rows($result);

        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);
			
			$template['variables'] = str_replace(" ","",$template['variables']);
			$replacefrom = explode(",",$template['variables']);
			$replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['email']);
			$message = str_replace($replacefrom,$replaceto,$template['template']);

			foreach($admingsm as $gsm){
				if(!empty($gsm)){
					$api->setGsmnumber( trim($gsm));
					$api->setUserid(0);
					$api->setMessage($message);
				}	$api->send();
            }
        }
    }
}

return $hook;
