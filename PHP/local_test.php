<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('error_log', '/tmp/php.log');


require 'process_request_and_send_email.php';

$test_request = '{
           "request_date":"02-24-2021",
           "record_owner":"employee@company.com",
           "site":"OHCC",
           "vendor":"henry schein",
           "vendor_email":"employee@company.com",
           "vendor_email_2":"employee@company.com",
           "vendor_rep_email":"employee@company.com",
           "account_no":"1927846, 1916751",
           "is_edi":"yes",
           "is_punchout":"yes",
           "isa":"TEST",
           "customer_contact_name":"wer",
           "customer_contact_email":"contact@gmail.com",
           "company_resource":"employee@company.com",
           "is_ltc":"no",
           "is_consignment":"no",
           "go_live_date":"",
           "isa_requirement":"",
           "810":"yes",
           "855":"yes",
           "856":"yes",
           "request_type":"Add Account # to Existing Config",
           "facility_address":"Test Address"
        }';

//$data = $test_request -> get_json_params();
$data = json_decode($test_request, true);
//print_r($test_request);
echo $data;

process_request_and_send_email($data);

?>
