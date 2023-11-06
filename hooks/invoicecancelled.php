<?php
$hook = array(
    'hook' => 'InvoiceCancelled',
    'function' => 'InvoiceCancelled',
    'description' => 'Executed when an invoice has left “Draft” status and is available to its respective client.<br>Client Related<br>User ID: {userid}, First Name: {firstname}, Last Name: {lastname}, Email Address: {email}, Company Name: {companyname}, Credit Balance: {credit}, Country: {country}, Currency: {currency}<br>Invoice Related<br> Due Date: {duedate}, Total: {total}, Invoice ID: {invoiceid} , Date Created: {date}, Date Paid: {datepaid}, Sub Total: {subtotal}, Tax: {tax}, Tax Rate: {taxrate}, Invoice Status: {status}, Invoice Payment Method: {paymentmethod}, Invoice Payment Link: {invoice_payment_link}, Invoice Pdf Link: {invoice_file}, Invoice items: {invoice_items}, Invoice items with amount: {invoice_items_amount}',
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Dear {firstname} {lastname}, your invoice has been cancelled',
    'variables' => '{userid}, {firstname}, {lastname}, {email}, {companyname}, {credit}, {country}, {duedate}, {total}, {invoiceid}, {currency} ,{date}, {datepaid}, {subtotal}, {tax}, {taxrate}, {status}, {paymentmethod}, {invoice_payment_link}, {invoice_file}, {invoice_items}, {invoice_items_amount}'
);
if (!function_exists('InvoiceCancelled')) {
    function InvoiceCancelled($args)
    {

        $api = new waclient();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if ($template['active'] == 0) {
            return null;
        }
        $settings = $api->apiSettings();
        if (!$settings['api_key'] || !$settings['api_token']) {
            return null;
        }

        $result = $api->getClientAndInvoiceDetailsBy($args['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql = full_query('SELECT code FROM tblcurrencies WHERE id=' . $UserInformation['currency']);
            $replace_currency = "";
            if (mysql_num_rows($currency_sql) > 0) {
                $currency_result = mysql_fetch_assoc($currency_sql);
                $replace_currency = $currency_result['code'];
            }


            $items = mysql_query('SELECT * FROM tblinvoiceitems WHERE invoiceid=' . $args['invoiceid']);

            while ($item_result = mysql_fetch_array($items)) {
                $invoice_items .= $item_result['description'].'\r\n';
                $invoice_items_amount .= $item_result['description']. ' - ' . $item_result['amount']. $replace_currency.'\r\n';
            }


            global $CONFIG;
            $URL = $CONFIG['SystemURL'];
            $invoice_payment_link = $URL . '/viewinvoice.php?id=' . $args['invoiceid'];
            $invoice_pdf_file = "";
            if ($template['attachment'] == 1) {
                $invoice_pdf_file = $api->pdfInvoice($args['invoiceid']);
            }
            $invoice_file = $URL . '/dl.php?type=i&id=' . $args['invoiceid'] . '&viewpdf=1';
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom = explode(",", $template['variables']);

            $replaceto = array($UserInformation['userid'], $UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['email'], $UserInformation['companyname'], $UserInformation['credit'], $UserInformation['country'], $api->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $args['invoiceid'], $replace_currency, $api->changeDateFormat($UserInformation['date']), $api->changeDateFormat($UserInformation['datepaid']), $UserInformation['subtotal'], $UserInformation['tax'], $UserInformation['taxrate'], $UserInformation['status'], $UserInformation['paymentmethod'], $invoice_payment_link, $invoice_file, $invoice_items, $invoice_items_amount);
            $message = str_replace($replacefrom, $replaceto, $template['template']);
            $gsmnumber = $UserInformation['gsmnumber'];
            if ($settings['gsmnumberfield'] > 0) {
                $gsmnumber = $api->customfieldsvalues($UserInformation['userid'], $settings['gsmnumberfield']);
            }


            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message, $invoice_pdf_file, $args['invoiceid']);
            $api->setUserid($UserInformation['userid']);
            $api->send();
        }
        logModuleCall('waclient', __FUNCTION__, $args, $api);
    }
}

return $hook;
