<?php

add_hook("AdminInvoicesControlsOutput", 1, function ($vars) {

    $api = new waclient();

    $settings = $api->apiSettings();
    if (!$settings['api_key'] || !$settings['api_token']) {
        return null;
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "1") {

        $template = $api->getTemplateDetails('InvoiceCreationPreEmail');


        if ($template['active'] == 0) {
            return null;
        }


        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "2") {

        $template = $api->getTemplateDetails('InvoicePaid');


        if ($template['active'] == 0) {
            return null;
        }


        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "3") {

        $template = $api->getTemplateDetails('InvoicePaymentReminder_Reminder');


        if ($template['active'] == 0) {
            return null;
        }

        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "4") {

        $template = $api->getTemplateDetails('InvoicePaymentReminder_Firstoverdue');


        if ($template['active'] == 0) {
            return null;
        }


        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "5") {

        $template = $api->getTemplateDetails('InvoicePaymentReminder_secondoverdue');


        if ($template['active'] == 0) {
            return null;
        }

        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "6") {

        $template = $api->getTemplateDetails('InvoicePaymentReminder_thirdoverdue');


        if ($template['active'] == 0) {
            return null;
        }

        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    if (isset($_POST['noticetype']) && $_POST["noticetype"] == "7") {

        $template = $api->getTemplateDetails('InvoiceCancelled');

        if ($template['active'] == 0) {
            return null;
        }


        $result = $api->getClientAndInvoiceDetailsBy($vars['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $vars['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $vars['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($vars['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $vars['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $vars['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $vars['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            
            $result = $api->send();

            if ($result == false) {
                $responseToShow =  $api->getErrors();
            } else {
                $responseToShow =  '<div class="success" style="padding:10px;">Message Sent Successfully</div>';
            }
        }
        logModuleCall('waclient', __FUNCTION__, $vars, $api);
    }

    $return = '
        <form method="post" action="" class="bottom-margin-5" style="margin-top:10px;">
            <select name = "noticetype" class="form-control select-inline">
            <option value = "1">Invoice Created</option>
            <option value = "2">Invoice Paid</option>
            <option value = "3">Invoice Payment Reminder</option>
            <option value = "4">First Invoice Overdue Notice</option>
            <option value = "5">Second Invoice Overdue Notice</option>
            <option value = "6">Third Invoice Overdue Notice</option>
            <option value = "7">Invoice Canceled</option>
            </select>
                <button class="btn btn-primary" type="submit" name="button1"><i class="fab fa-whatsapp" style="padding-right:6px;"></i>Send WhatsApp</button>
                </form>
        <div class="text-center">'. $responseToShow .'</div>
    ';
    return $return;
});