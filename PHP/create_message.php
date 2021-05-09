<?php

function create_message($sender, $to, $message_text, $ccs, $vendor, $customer, $is_punchout, $file=NULL, $warning_message=NULL){

  $user_to_impersonate = "edi@company.com";
  putenv("GOOGLE_APPLICATION_CREDENTIALS=google-api-php-client/service-account-credentials.json");
  $client = new Google_Client();
  $client->useApplicationDefaultCredentials();
  $client->setSubject($user_to_impersonate);
  $client->setApplicationName("My Mailer");
  $client->setScopes(["https://www.googleapis.com/auth/gmail.compose"]);
  $service = new Google_Service_Gmail($client);

  $subject = "EDI request for company customer";
  //If a warning was generated, then we send an error email to implementation
  if ($warning_message != NULL) {
    $message_text = $warning_message;
    $subject = "EDI REQUEST COULD NOT BE SENT";
  //All valid requests go through this flow
  } else {
    if ($is_punchout === "yes") {
      if (strtolower($vendor) === "medline") {
        $subject = "Punch-out request for company - " . $customer;
      } else {
        $subject = "EDI / CXML request for company - " . $customer;
      }
    } else {
      $subject = "EDI request for company - " . $customer;
    }
  }

  //Use Swift_Message to easily create and encode an email
  $msg = (new Swift_Message($subject))
    -> setFrom($sender )
    -> setTo($to)
    -> setCc($ccs)
    -> setContentType('text/html')
    -> setCharset('utf-8')
    -> setBody($message_text);
  if ($file !== NULL) {
    $msg -> attach(Swift_Attachment::fromPath($file));
  }
  try {
    $msg_base64 = base64_encode($msg -> toString());
  } catch (Exception $e) {
    trigger_error('Email message not be encoded', E_USER_WARNING);
    exit();
  }

  $msg = new Google_Service_Gmail_Message();
  $msg->setRaw($msg_base64);

  //print_r($msg);
  return $msg;

}

?>
