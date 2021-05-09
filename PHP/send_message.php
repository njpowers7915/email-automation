<?php
require 'format_body.php';
require 'gmail_auth.php';
require 'create_message.php';

function send_message($to, $vendor, $customer, $is_punchout, $account_no, $ccs, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live, $address, $isa=None, $attachment_file=None, $warning_message=None) {
  $message_body = format_body($vendor, $customer, $is_punchout, $account_no, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live, $address, $isa);
  if ($message_body === '' || $message_body === NULL) {
    trigger_error('Email body could not be formatted', E_USER_WARNING);
    exit();
  }
  $message = create_message('edi@company.com', $to, $message_body, $ccs, $vendor, $customer, $is_punchout, $attachment_file, $warning_message);

  //Generate authentication credentials for the Gmail API
  $gmail_auth_creds = gmail_auth();
  //Use auth creds to create a new Gmail Service instance
  $service = new \Google_Service_Gmail($gmail_auth_creds);
  try {
    //"me" is used to indicate the authenticated user
    $message = $service -> users_messages -> send('me', $message);
    print_r('Message ID: ' . $message -> getId() . ' sent.');
    return $message -> getId();
  } catch (Exception $e) {
    trigger_error('An error occurred: ' . $e->getMessage(), E_USER_WARNING);
    print_r('An error occurred: ' . $e->getMessage());
  }
  return NULL;
}

?>
