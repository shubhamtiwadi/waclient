<?php

/* WhatsApp Notifications WHMCS Module By Avneesh Mishra

 * WA Client - https://notify.designfactri.biz

 * Version 2.0.4

 * */

if (!defined("WHMCS"))

    die("This file cannot be accessed directly");

use WHMCS\Database\Capsule;

function waclient_config()
{

    $configarray = [

        "name" => "WhatsApp Notifications",

        "description" => "WA Client - WHMCS WhatsApp Notifications Addon. To Create Account <a href=\"https://notify.designfactri.biz/login\" target='_blank'>WA Client</a>",

        "version" => "2.0.4",

        "author" => "<img src='../modules/addons/waclient/logo.png' height='48'>",

        "language" => "english",

    ];

    return $configarray;
}



function waclient_activate()
{


    $query = "CREATE TABLE IF NOT EXISTS `mod_waclient_messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`group_id` varchar(40) NOT NULL,`to` varchar(15) DEFAULT NULL,`text` text,`uid` varchar(50) DEFAULT NULL,`attachment` varchar(25) DEFAULT NULL,`attachmentfile` varchar(10000) DEFAULT NULL,`status` varchar(150) DEFAULT NULL,`errors` text,`logs` text,`user` int(11) DEFAULT NULL,`datetime` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

    full_query($query);



    $query = "CREATE TABLE IF NOT EXISTS `mod_waclient_settings` (`id` int(11) NOT NULL AUTO_INCREMENT,`api_key` varchar(100) CHARACTER SET utf8 NOT NULL,`api_token` varchar(500) CHARACTER SET utf8 NULL,`wantwhatsappfield` int(11) DEFAULT NULL,`gsmnumberfield` int(11) DEFAULT NULL,`dateformat` varchar(12) CHARACTER SET utf8 DEFAULT NULL,`version` varchar(6) CHARACTER SET utf8 DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

    full_query($query);



    $query = "INSERT INTO `mod_waclient_settings` (`api_key`, `api_token`, `wantwhatsappfield`, `gsmnumberfield`,`dateformat`, `version`) VALUES ('none', 'none', 0, 0,'%d.%m.%y','1.0.0');";

    full_query($query);



    $query = "CREATE TABLE IF NOT EXISTS `mod_waclient_templates` (`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(50) CHARACTER SET utf8 NOT NULL,`type` enum('client','admin') CHARACTER SET utf8 NOT NULL,`admingsm` varchar(255) CHARACTER SET utf8 NOT NULL,`template` varchar(1500) CHARACTER SET utf8  NOT NULL,`variables` varchar(1000) CHARACTER SET utf8 NOT NULL,`active` tinyint(1) NOT NULL,`extra` varchar(3) CHARACTER SET utf8 NOT NULL,`attachment` tinyint(1) NOT NULL,`description` text CHARACTER SET utf8,PRIMARY KEY (`id`))  ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

    mysql_query($query);


    $query = "CREATE TABLE IF NOT EXISTS `mod_waclient_otp` (`id` int(11) NOT NULL AUTO_INCREMENT,`otp` varchar(50) CHARACTER SET utf8 NOT NULL,`type` enum('client','admin') CHARACTER SET utf8 DEFAULT 'client',`relid` int(10) DEFAULT 0,`request` varchar(50) CHARACTER SET utf8 NOT NULL,`text` text,`status` tinyint(1) DEFAULT 0, `datetime` datetime NOT NULL, `phonenumber` text, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

    full_query($query);


    require_once("api.php");

    require_once("lib/waclient.class.php");

    $api = new waclient();

    $api->checkLists();



    return array('status' => 'success', 'description' => 'WHMCS WhatsApp Notifications Addon successfully activated');
}



function waclient_deactivate()
{



    $query = "DROP TABLE `mod_waclient_templates`";

    mysql_query($query);

    $query = "DROP TABLE `mod_waclient_settings`";

    mysql_query($query);

    $query = "DROP TABLE `mod_waclient_messages`";

    mysql_query($query);

    //DROP Table for OTP

    $query = "DROP TABLE `mod_waclient_otp`";

    mysql_query($query);



    return array('status' => 'success', 'description' => 'WHMCS WhatsApp Notifications Addon successfully deactivated');
}



function Newsletters_upgrade($vars)
{

    $version = $vars['version'];



    switch ($version) {

        case "1":

            break;
    }



    $api = new waclient();

    $api->checkLists();
}









function waclient_output($vars)
{

    $modulelink = $vars['modulelink'];

    $version = $vars['version'];

    $LANG = $vars['_lang'];

    putenv("TZ=Asia/Colombo");

    $api = new waclient();

    $tab = $_GET['tab'];

    echo '<div id="newsletters_sms_system">

    

    <style>
    
    table.form {
        padding: 20px !important;
        border-radius: 0 !important;
    }

    .card {
        background:#FFFFFF;
        transition: 0.3s;
        padding:20px;
        width: 100%;
        border: 1px solid #ddd;
    }

    
    table.form td {
        padding: 8px 12px;
    }

    .contentarea{

        background: #f5f5f5 !important;

    }

    #clienttabs *{

        margin: inherit;

        padding: inherit;

        border: inherit;

        color: inherit;

        background: inherit;

        background-color: inherit;

    }

    .form-control {
        color: #000 !important;
        font-weight: 400 !important;
    }


    #clienttabs{position: relative; z-index: 99;}

     #clienttabs ul li {

        display: inline-block;

        margin-right: 3px;

        border: 1px solid #ddd;

        border-bottom:0px;

        padding: 12px;

        margin-bottom: -1px;

     }

     #clienttabs ul a {

     border: 0px;;

     }

     #clienttabs ul {

        float:left;

        margin-bottom:0px;

     }

     #clienttabs{

        float:left;

     }

     .success-box {
         background-repeat: no-repeat;
         background-color: #d9e6c3;
         border: 1px solid #77ab13;
         color: #69990f;
         padding: 1rem 1.25rem;
         margin-bottom: 1rem;
         text-align: left;
         -moz-border-radius: 5px;
         -webkit-border-radius: 5px;
         -o-border-radius: 5px;
         border-radius: 5px;
     }
     .success-box p {
         color: #69990f;
         margin:0;
     }
     
     .error-box {
         background-repeat: no-repeat;
         background-color: #f2d4ce;
         border: 1px solid #ae432e;
         color: #c00;
         padding: 1rem 1.25rem;
         margin-bottom: 1rem;
         text-align: left;
         -moz-border-radius: 5px;
         -webkit-border-radius: 5px;
         -o-border-radius: 5px;
         border-radius: 5px;
     }
     .error-box p {
         color: #c00;
         margin:0;
     }
     

    </style>

    <div id="clienttabs">

        <ul>

            <li class="' . (($tab == "settings" || (@$_GET['type'] == "" && $tab == "")) ? "tabselected" : "tab") . '"><a href="addonmodules.php?module=waclient&tab=settings">' . $LANG['settings'] . '</a></li>

            <li class="' . ((@$_GET['type'] == "client") ? "tabselected" : "tab") . '"><a href="addonmodules.php?module=waclient&tab=templates&type=client">' . $LANG['clientsmstemplates'] . '</a></li>

            <li class="' . ((@$_GET['type'] == "admin") ? "tabselected" : "tab") . '"><a href="addonmodules.php?module=waclient&tab=templates&type=admin">' . $LANG['adminsmstemplates'] . '</a></li>

            <li class="' . (($tab == "sendtoclients") ? "tabselected" : "tab") . '"><a href="addonmodules.php?module=waclient&tab=sendtoclients">' . $LANG['sendsms'] . '</a></li>

            <li class="' . (($tab == "messages") ? "tabselected" : "tab") . '"><a href="addonmodules.php?module=waclient&amp;tab=messages">' . $LANG['messages'] . '</a></li>

            <li class="' . (($tab == "c") ? "tabselected" : "tab") . '"><a href="addonmodules.php?module=waclient&amp;tab=support">' . $LANG['support'] . '</a></li>

        </ul>

    </div>

    <div style="clear:both;"></div>

    ';



    if (!isset($tab) || $tab == "settings") {

        /* UPDATE SETTINGS */

        if (isset($_POST['params'])) {

            $update = array(

                "api_key" => $_POST['api_key'],

                "api_token" => $_POST['api_token'],

                'wantwhatsappfield' => $_POST['wantwhatsappfield'],

                'gsmnumberfield' => $_POST['gsmnumberfield'],

                'dateformat' => $_POST['dateformat']

            );

            update_query("mod_waclient_settings", $update, "");

            if ($update == false) {
                $responseToShow =  '<div class="error-box"><p>' . $LANG['error'] . '</p></div>';
            } else {

                $responseToShow =  '<div class="success-box"><p>' . $LANG['saved-successfully'] . '</p></div>';
            }
        }

        /* UPDATE SETTINGS */



        $settings = $api->apiSettings();

        $api_key = $settings['api_key'];

        $api_token = $settings['api_token'];


        $result = Capsule::table('tblcustomfields')->where([
            ['fieldtype', '=', 'text'],
            ['type', '=', 'client'],
        ])->get();

        $gsmnumber = '<option value="0" selected >' . $LANG['defaultmobile'] . '</option>';

        foreach ($result as $customfield) {
            if ($customfield->id == $settings['gsmnumberfield']) {
                $selected = 'selected="selected"';
            } else {
                $selected = "";
            }
            $gsmnumber .= '<option value="' . $customfield->id . '" ' . $selected . '>Custom Field : ' . $customfield->fieldname . '</option>';
        }


        echo '

        <style>


.container {

  padding: 20px 16px;

  display: flex;

  justify-content: center;

  flex-direction: row;

}

</style>

        <div class="card">
        ' . $responseToShow . ' 

         <div class="container">

        <form action="" method="post" id="form">

        <input type="hidden" name="action" value="save" />

            <div class="internalDiv">

            <span id="responsemsg"></span>

			<input type="hidden" name="params" value="0"/>

 

                            <td class="fieldlabel" width="30%">' . $LANG['accesstoken'] . '</td>

                            <div class="">

                            <input type="text" name="api_key" class="form-control" size="40" value="' . $settings['api_key'] . '">

                            </div>

                            <td class="fieldlabel" width="30%">' . $LANG['instanceid'] . '</td>

                            <div class="">

                            <input type="text" name="api_token" class="form-control" size="40" value="' . $settings['api_token'] . '">

                            </div>

                            <div class="btn-container"><a class="btn btn-warning" href="https://notify.designfactri.biz/" target="_blank">Signup to get your instance ID and Access Token</a></div>

                            <td class="fieldlabel" width="30%">' . $LANG['gsmnumberfield'] . '</td>

                                <select name="gsmnumberfield" class="form-control">
                                    ' . $gsmnumber . '
                                </select>

                                <div class="btn-container"><a class="" href="configcustomfields.php">' . $LANG['addgsmnumberfield'] . '</a></div>

                            </tr>

                            <td class="fieldlabel" width="30%">' . $LANG['dateformat'] . '</td>

                            <div class="">

                            <input type="text" name="dateformat" class="form-control" size="40" value="' . $settings['dateformat'] . '">  </div> e.g:  %d.%m.%y (27.01.2014)

                           

                            </div>

            <div class="btn-container">

                <input type="submit" value="' . $LANG['save'] . '" class="btn btn-primary btn-block" />

            </div>

        </form>

        </div>

        </div>

        ';
    } elseif ($tab == "templates") {

        echo  '
        <link href="https://fonts.googleapis.com/css2?family=Noto+Emoji:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </script>


        ';

        function json_encode_unicode($data)
        {
            return preg_replace_callback(
                '/(?<!\\\\)\\\\u([0-9a-f]{4})/i',
                function ($m) {
                    $d = pack("H*", $m[1]);
                    $r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
                    return $r !== "?" && $r !== "" ? $r : $m[0];
                    $data = str_replace('"', "", json_encode($data, JSON_HEX_APOS));
                },
                json_encode($data)
            );
        }

        if (isset($_POST['params'])) {

            $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));

            $result = select_query("mod_waclient_templates", "*", $where);

            while ($data = mysql_fetch_array($result)) {

                if ($_POST[$data['id'] . '_active'] == "on") {

                    $tmp_active = 1;
                } else {

                    $tmp_active = 0;
                }

                if ($_POST[$data['id'] . '_attachment'] == "on") {

                    $tmp_attachment = 1;
                } else {

                    $tmp_attachment = 0;
                }

                $update = array(

                    "template" => json_encode_unicode($_POST[$data['id'] . '_template']),

                    "active" => $tmp_active,

                    "attachment" => $tmp_attachment

                );


                if (isset($_POST[$data['id'] . '_extra'])) {

                    $update['extra'] = trim($_POST[$data['id'] . '_extra']);
                }

                if (isset($_POST[$data['id'] . '_admingsm'])) {

                    $update['admingsm'] = $_POST[$data['id'] . '_admingsm'];

                    $update['admingsm'] = str_replace(" ", "", $update['admingsm']);
                }

                update_query("mod_waclient_templates", $update, "id = " . $data['id']);
            }

            if ($update == false) {
                $responseToShow =  '<div class="error-box"><p>' . $LANG['error'] . '</p></div>';
            } else {

                $responseToShow =  '<div class="success-box"><p>' . $LANG['saved-successfully'] . '</p></div>';
            }
        }



        echo '
        <div class="card">
        ' . $responseToShow . ' 
        <form action="" method="post">

        <input type="hidden" name="action" value="save" />

        <input type="hidden" name="params" value="0"/>

            <div class="internalDiv">

                <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3" style="margin:0px;border: 0px;">

                    <tbody>';

        $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));

        $result = select_query("mod_waclient_templates", "*", $where);


        while ($data = mysql_fetch_array($result)) {

            if ($data['active'] == 1) {

                $active = 'checked = "checked"';
            } else {

                $active = '';
            }

            if ($data['attachment'] == 1) {
                $attachment = 'checked = "checked"';
            } else {
                $attachment = '';
            }

            $desc = json_decode($data['description']);

            if (isset($desc->$LANG['lang'])) {

                $name = $desc->$LANG['lang'];
            } else {

                $name = $data['name'];
            }

            echo '

                <tr>

                    <td class="fieldlabel" width="30%">' . $name . '</td>
                    <td class="fieldarea">
                            <textarea rows="4" cols="70" name="' . $data['id'] . '_template">' .  json_decode($data['template']) . '</textarea>
                    </td>

                </tr>';

            echo '

            <tr>

                <td class="fieldlabel"  style="float:right;">' . $LANG['parameter'] . '</td>

                <td>' . $data['variables'] . '</td>

            </tr>

            ';

            if (!empty($data['extra'])) {

                echo '

                <tr>

                    <td class="fieldlabel" width="30%">' . $LANG['ekstra'] . '</td>

                    <td class="fieldarea">

                        <input type="text" name="' . $data['id'] . '_extra" value="' . $data['extra'] . '">

                    </td>

                </tr>

                ';
            }

            if ($_GET['type'] == "admin") {

                echo '

                <tr>

                    <td class="fieldlabel" width="30%">' . $LANG['admingsm'] . '</td>

                    <td class="fieldarea">

                        <input type="text" class="extraField" name="' . $data['id'] . '_admingsm" placeholder="Ex :  88017XXXXXXX,88018XXXXXXX" value="' . $data['admingsm'] . '">

                    </td>

                </tr>

                ';
            }

            echo '

            <tr>

                <td class="fieldlabel" width="30%" style="float:right;">' . $LANG['active'] . '</td>

                <td><input type="checkbox" value="on" name="' . $data['id'] . '_active" ' . $active . '></td>

            </tr>

            ';


            if (str_contains($data['name'], 'Invoice')) {
                echo '
            <tr>
                <td class="fieldlabel"  style="float:right;">' . $LANG['attachment'] . '</td>
                <td><input type="checkbox" value="on" name="' . $data['id'] . '_attachment" ' . $attachment . '></td>
            </tr>
            ';
            }



            echo '<tr>

                <td colspan="2"><hr></td>

            </tr>';
        }

        echo '
        </tbody>

                </table>

        

            </div>

            <div class="btn-container">

                <input type="submit" value="' . $LANG['save'] . '" class="btn btn-primary" />

            </div>

            </form>
            </div>
            ';
    } elseif ($tab == "messages") {

        if (!empty($_GET['deletesms'])) {

            $smsid = (int) $_GET['deletesms'];

            $sql = "DELETE FROM mod_waclient_messages WHERE id = '$smsid'";

            mysql_query($sql);
        }

        if (isset($_GET['resend'])) {

            $gsmnumber = $_GET['resend'];

            $message = json_encode($_GET['text']);

            $userid = $_GET['userid'];

            $invoiceid = $_GET['invoiceid'];

            $attachmentfile = $_GET['attachmentfile'];

            if ($invoiceid) {

                $invoice_pdf_file = $api->pdfInvoice($invoiceid);
            }

            $api->setGsmnumber($gsmnumber);

            $api->setMessage($message, $attachmentfile, $invoice_pdf_file, $invoiceid);

            $api->setUserid($userid);


            $result = $api->send();


            if ($result == false) {
                $responseToShow =  '<div class="error-box"><p>' . $LANG['errorwhatsappsent'] . '</p></div>';
            } else {

                $responseToShow =  '<div class="success-box"><p>' . $LANG['smssent'] . '</p></div>';
            }
        }

        echo  '

        <!--<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>

        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" type="text/css">

        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css" type="text/css">

        <script type="text/javascript">

            $(document).ready(function(){

                $(".datatable").dataTable();

            });

        </script>-->


        <div class="card">

        ' . $responseToShow . ' 
        
        <table class="datatable" border="0" cellspacing="1" cellpadding="3" style="margin: 0px; border: 0px;">

        <thead>

            <tr>

                <th>#</th>

                <th>' . $LANG['client'] . '</th>

                <th>' . $LANG['gsmnumber'] . '</th>

                <th width="50%" >' . $LANG['message'] . '</th>

                <th>' . $LANG['status'] . '</th>

                <th>' . $LANG['datetime'] . '</th>

                <th>' . $LANG['tempate'] . '</th>

                <th width="40"></th>

            </tr>

        </thead>

        <tbody>

        ';



        // Getting pagination values.

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $limit = (isset($_GET['limit']) && $_GET['limit'] <= 50) ? (int)$_GET['limit'] : 10;

        $start  = ($page > 1) ? ($page * $limit) - $limit : 0;

        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

        /* Getting messages order by date desc */

        $sql = "SELECT `m`.*,`user`.`firstname`,`user`.`lastname`

        FROM `mod_waclient_messages` as `m`

        JOIN `tblclients` as `user` ON `m`.`user` = `user`.`id`

        ORDER BY `m`.`datetime` {$order} limit {$start},{$limit}";

        $result = mysql_query($sql);

        $i = 0;



        //Getting total records

        $total = "SELECT count(id) as toplam FROM `mod_waclient_messages`";

        $sonuc = mysql_query($total);

        $sonuc = mysql_fetch_array($sonuc);

        $toplam = $sonuc['toplam'];



        //Page calculation

        $sayfa = ceil($toplam / $limit);



        while ($data = mysql_fetch_array($result)) {

            if ($data['group_id'] && $data['status'] == "") {

                $status = $api->getReport($data['phid']);

                mysql_query("UPDATE mod_waclient_messages SET status = '$status' WHERE id = " . $data['id']);
            } else {

                $status = $data['status'];
            }



            $i++;


            echo  '<tr>

            <td>' . $data['id'] . '</td>

            <td><a href="clientssummary.php?userid=' . $data['user'] . '">' . $data['firstname'] . ' ' . $data['lastname'] . '</a></td>

            <td>' . $data['to'] . '</td>

            <td>' . json_decode($data['text']) . '</td>

            <td><center>' . $data['status'] . '</center></td>

            <td><center>' . $data['datetime'] . '</center></td>

            <td><center><a href="addonmodules.php?module=waclient&tab=messages&resend=' . $data['to'] . '&text=' . json_decode($data['text']) . '&userid=' . $data['user'] . '&invoiceid=' . $data['attachment'] . '&attachmentfile=' . $data['attachmentfile'] . '" class="btn btn-warning">' . $LANG['resend'] . '</a></center></center></td>

            <td><center><a href="addonmodules.php?module=waclient&tab=messages&deletesms=' . $data['id'] . '" title="' . $LANG['delete'] . '"><img src="images/delete.gif" width="16" height="16" border="0" alt="Delete"></a></center></td></tr>';
        }

        /* Getting messages order by date desc */



        echo '

        </tbody>

        </table>

        ';

        $list = "";

        for ($a = 1; $a <= $sayfa; $a++) {

            $selected = ($page == $a) ? 'selected="selected"' : '';

            $list .= "<option value='addonmodules.php?module=waclient&tab=messages&page={$a}&limit={$limit}&order={$order}' {$selected}>{$a}</option>";
        }

        echo "<select  onchange=\"this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);\">{$list}</select></div>";
    } elseif ($tab == "sendtoclients") {

        $settings = $api->apiSettings();



        if (!empty($_POST['client'])) {
            foreach ($_POST['client'] as $value) {
                // $userinf = explode("_",$_POST['client']);
                $userinf = explode("_", $value);
                $userid = $userinf[0];
                $gsmnumber = $userinf[1];
                $country = $userinf[4];
                if ($settings['gsmnumberfield'] > 0) {
                    $gsmnumber = $api->customfieldsvalues($userid, $settings['gsmnumberfield']);
                }
                $replacefrom = array("{userid}", "{firstname}", "{lastname}");
                $replaceto = array($userinf[0], $userinf[2], $userinf[3]);
                $message = str_replace($replacefrom, $replaceto, json_encode($_POST['message']));

                $attachmentfile = $_POST['attachmentfile'];
                $api->setCountryCode($api->getCodeBy($country));
                $api->setGsmnumber($gsmnumber);
                $api->setMessage($message, $attachmentfile);
                $api->setUserid($userid);

                $result = $api->send();

                // echo $attachmentfile;

                if ($result == false) {
                    $responseToShow =  '<div class="error-box"><p>' . $LANG['errorwhatsappsent'] . '</p></div>';
                } else {

                    $responseToShow =  '<div class="success-box"><p>' . $LANG['smssent'] . '</p></div>';
                }

                if ($_POST["debug"] == "ON") {
                    $debug = 1;
                }
            }
        }

        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`country`, `a`.`phonenumber` as `gsmnumber`

        FROM `tblclients` as `a` order by `a`.`firstname`";



        $clients = '';

        $result = mysql_query($userSql);

        while ($data = mysql_fetch_array($result)) {

            $clients .= '<option value="' . $data['id'] . '_' . $data['gsmnumber'] . '_' . $data['firstname'] . '_' . $data['lastname'] . '_' . $data['country'] . '">' . $data['firstname'] . ' ' . $data['lastname'] . ' (#' . $data['id'] . ')</option>';
        }



        echo '

        <script>

        jQuery.fn.filterByText = function(textbox, selectSingleMatch) {

          return this.each(function() {

            var select = this;

            var options = [];

            $(select).find("option").each(function() {

              options.push({value: $(this).val(), text: $(this).text()});

            });

            $(select).data("options", options);

            $(textbox).bind("change keyup", function() {

              var options = $(select).empty().scrollTop(0).data("options");

              var search = $.trim($(this).val());

              var regex = new RegExp(search,"gi");



              $.each(options, function(i) {

                var option = options[i];

                if(option.text.match(regex) !== null) {

                  $(select).append(

                     $("<option>").text(option.text).val(option.value)

                  );

                }

              });

              if (selectSingleMatch === true && 

                  $(select).children().length === 1) {

                $(select).children().get(0).selected = true;

              }

            });

          });

        };

        $(function() {

          $("#clientdrop").filterByText($("#textbox"), true);

        });  

        </script>
        ';


        echo '
        <div class="card">

        <form action="" method="post">

        <input type="hidden" name="action" value="save" />

            <div class="internalDiv" >' . $responseToShow . '

                <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3" style="margin:0px;border: 0px;">

                    <tbody>

                        <tr>

                            <td class="fieldlabel" width="30%">' . $LANG['client'] . '</td>

                            <td class="fieldarea">
                                <input id="textbox" type="text" placeholder="' . $LANG['typeclient'] . '" style="width:498px;padding:5px; margin-bottom:10px;"><br>
                                <select name="client[]" required class="sel" multiple id="clientdrop" style="padding:5px">
                                    <option value="">' . $LANG['selectclient'] . '</option>
                                    ' . $clients . '
                                </select>
                            </td>

                        </tr>

                        <tr>

                            <td class="fieldlabel" width="30%">' . $LANG['message'] . '</td>

                            <td class="fieldarea">

                               <textarea cols="70" required rows="5" name="message" style="width:498px;padding:5px"></textarea>

                            </td>

                        </tr>

                        
                    </tr>

                    <tr>

                        <td class="fieldlabel" width="30%">Parameters:</td>
                        <td>
                        {userid},{firstname},{lastname}
                        </td>

                    </tr>

                        <tr>

                        <td class="fieldlabel" width="30%">' . $LANG['attachmentfile'] . '</td>

                        <td class="fieldarea">

                           <input type="text" placeholder="' . $LANG['file-url'] . '" name="attachmentfile" style="width:498px;padding:5px">

                        </td>
                        </tr>
                        <tr>

                        <td class="fieldlabel" width="30%">' . $LANG['media-types'] . '</td>
                        <td>
                        ' . $LANG['attachment-extension'] . '
                        </td>

                    </tr>

                    </tbody>

                </table>

           

            </div>

            <div class="btn-container">

                <input type="submit" value="' . $LANG['sent'] . '" class="btn btn-primary" />

            </div>

        </form>
        
        </div>
        ';
    } elseif ($tab == "support") {



        echo '<div class="card">';

        echo $LANG['cmodulesversion'];

        echo $LANG['phoneus'];

        echo $LANG['emailus'];

        echo $LANG['website'];

        echo $LANG['clientportal'];

        echo $LANG['smsportal'];



        echo '</div>';
    }



    $credit =  $api->getBalance();

    if ($credit['balance']) {

        echo '

            <div style="text-align: left;background-color: whiteSmoke;margin: 0px;padding: 5px; border: 1px solid #ddd;">

            <b>' . $LANG['balance'] . ':</b>

            </div>';
    }

    echo $LANG['lisans'];

    echo '</div>';
}
