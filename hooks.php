<?php

/* WhatsApp Notifications WHMCS Module By Avneesh Mishra

 * WA Client - https://smppking.com/

 * Version 2.0.4

 * */

 if (!defined("WHMCS"))
 die("This file cannot be accessed directly");

require_once(ROOTDIR . '/modules/addons/waclient/api.php');

$api = new waclient();
$lists = $api->getLists();
$settings = $api->apiSettings();
$disable = $settings['disable'];

if($disable != 1){
 foreach($lists as $lists){
     add_hook($lists['hook'], 1, $lists['function'], "");
 }
}