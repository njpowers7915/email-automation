<?php
include_once 'vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

function post_to_slack($automated_vendor=NULL,
                       $vendor=NULL,
                       $site=NULL,
                       $ccs=NULL,
                       $vendor_email=NULL,
                       $warning_message=NULL,
                       $message_id=NULL,
                       $isa=NULL) {
    //$automated_vendor is a boolean flag that needs to be set to True or else failure messages will go out for vendors who haven't been automated.

    //Each time app is installed, this Webhook URL needs to be updated
    $dotenv = new Dotenv();
    $dotenv -> load('.env');
    $webhook_url = $_ENV['WEBHOOK_URL'];


    if ($warning_message != NULL) {
        //If warning_message = isa, alert employees to update a site's ISA value
        if ($warning_message === 'isa') {
            $username = 'EDI Automation - WARNING';
            $text = '@employee New ISA was created for site -> ' . $site . ' with vendor -> ' . $vendor .
                '.  Please add "' . $isa . '" as an ISA';
            $settings = [
                'channel' => '#edi-request-automation',
                'username' => $username,
                'link_names' => true
            ];
            $color = '#E9FC00';
            $emoji = ':warning:';

        //If $warning_message is anything other than "isa", it signals a request failure
        } else {
            print_r('Automation Failure');
            $username = 'EDI Automation - FAILURE';
            $text = '@employee  ' . $warning_message;
            $settings = [
                'channel' => '#edi-request-automation',
                'username' => $username,
                'link_names' => true
            ];
            $color = '#D30000';
            $emoji = ':rotating_light:';
            //$client = new Maknz\Slack\Client($webhook_url, $settings);
            //$client -> send($text);
            //rreturn(0);
        }

    } else {
        //Generate failure notification if an email isn't generated
        if (is_null($message_id)) {
            if ($automated_vendor === True) {
                print_r('fail');
                $username = 'EDI Automation - FAILURE';
                $ccs = implode(', ', $ccs);
                $text = "@employee Email could not be sent for the following request:\n" .
                    "Vendor: " . $vendor . "\n" .
                    "Site: " . $site . "\n" .
                    "Vendor Email: " . $vendor_email . "\n" .
                    "CCs: " . $ccs . "\n";
                $color = 'danger';
                $emoji = ':rotating_light:';
            } else {
                print_r('Vendor not configured for Automated Requests');
                exit();
            }
            //Generate "success" message if email is generated
        } else {
            print_r('Success!!');
            $username = 'EDI Automation Success';
            $text = "Request Sent for " . $site . " -- " . $vendor . " (GMail Message ID: " . $message_id . ")";
            $color = '#1DD300';
            $emoji = ':white_check_mark:';
        }
    }

    $settings = [
        'channel' => '#edi-request-automation',
        'username' => $username,
        'link_names' => true,
        'color' => $color
    ];

    //This object simplifies the POST request to Slack
    $client = new Maknz\Slack\Client($webhook_url, $settings);
    $client ->withIcon($emoji)->send($text);


}
