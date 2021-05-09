<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('error_log', 'logs/errors.log');
require 'process_request_and_send_email.php';
include_once 'post_to_slack.php';

include_once 'vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv -> load('.env');

$t = time();

file_put_contents($t, print_r(file_get_contents('php://input'), true));

$data = json_decode(file_get_contents('php://input'), true);

file_put_contents($t.'.data', print_r($data, true));

if (!empty($_POST)) {
  $params = join(" ", $_POST);
  //print_r($params);
  echo "|$params|";
}

if(!empty($_GET["testing"]))
{
    if($_GET["testing"] == 1)
    {

        $test_request = '{
           "request_date":"02-24-2021",
           "record_owner":"employee@company.com",
           "site":"OHCC",
           "vendor":"mckesson",
           "vendor_email":"employee@company.com",
           "vendor_email_2":"employee@company.com",
           "vendor_rep_email":"employee@company.com",
           "account_no":"1927846, 1916751",
           "is_edi":"yes",
           "is_punchout":"yes",
           "isa":"OHCC",
           "customer_contact_name":"wer",
           "customer_contact_email":"werw",
           "company_resource":"employee@company.com",
           "is_ltc":"no",
           "is_consignment":"no",
           "go_live_date":"",
           "isa_requirement":"",
           "810":"yes",
           "855":"yes",
           "856":"yes",
           "request_type":"New Configuration"
        }';
        echo $test_request;

    }
}

if(!empty($data["data"]) && isset($_GET["request_key"]))
{

    if($_GET["request_key"] == $_ENV['REQUEST_KEY'])
    {
        $request_key = $_ENV['REQUEST_KEY'];
        $request_json = $data["data"];
        $request = $request_json;
        print_r($request);
    }else{
        $warning_text = "EDI Request Failure: Incorrect or Missing Request Key from Quick Base";
        post_to_slack($warning_message = $warning_text);
        die("</br><code>An incorrect request key was provided, or no request key was provided at all.</code></br>");
    }

}


if(isset($test_request))
{
    //POST request will trigger process_request_and_send_email()
    process_request_and_send_email($test_request);
}

if(isset($request))
{
    //POST request will trigger process_request_and_send_email()
    process_request_and_send_email($request);
}


?>
