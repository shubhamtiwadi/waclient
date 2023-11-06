<?php

add_hook("AdminAreaClientSummaryActionLinks", 1, function ($vars) {

    $return = [];

    $api = new waclient();

    $template = $api->getTemplateDetails('ClientAdd');

    $settings = $api->apiSettings();
    if (!$settings['api_key'] || !$settings['api_token']) {
        return null;
    }

    $result = $api->getClientDetailsBy($vars['userid']);

    if (isset($_POST['button1'])) {

        $UserInformation = mysql_fetch_assoc($result);
        $template['variables'] = str_replace(" ", "", $template['variables']);
        $replacefrom = explode(",", $template['variables']);
        $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email']);
        $message = str_replace($replacefrom, $replaceto, json_encode($_POST['message']));
        $gsmnumber = $UserInformation['gsmnumber'];
        if ($settings['gsmnumberfield'] > 0) {
            $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
        }

        $api->setCountryCode($UserInformation['country']);
        $api->setGsmnumber($gsmnumber);
        $api->setMessage($message);
        $api->setUserid($UserInformation['userid']);

        $result = $api->send();

        if ($result == false) {
            $responseToShow =  $api->getErrors();
        } else {
            $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
        }
    }

    if ($vars['userid']) {

        $return[] = '
            <form method="post" style="padding: 10px;">
            <div class="">
                <div class="title"><i class="fab fa-whatsapp" style="padding-right:6px;"></i>Send WhatsApp message</div>
                        <div class="form-group">
                        <textarea required name="message" rows="5" class="form-control bottom-margin-5"></textarea>
                        </div>

                    <div class="form-group">
                    <div style="margin-bottom:6px;">
                    <b>Parameters</b>
                    </div>
                        <table class="clientssummarystats" cellspacing="0" cellpadding="2">
                        <tbody>
                        <tr><td>User ID</td><td>{userid}</td></tr>
                        <tr class="altrow"><td>First Name</td><td>{firstname}</td></tr>
                        <tr><td>Last Name</td><td>{lastname}</td></tr>
                        <tr class="altrow"><td>Email</td><td>{email}</td></tr>
                        </tbody>
                        </table>
                    </div>

                        <button class="form-control btn btn-primary mt-2" type="submit" name="button1" value="Button1">Sent</button>
                        
            </div>

            <div align="center">' . $responseToShow . '</div>

        </form>

        <br>';
    }


    return $return;
});
