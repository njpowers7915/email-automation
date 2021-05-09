<?php
include 'config_array.php';

function format_body($vendor, $customer, $is_punchout, $account_no, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live, $address, $isa=NULL) {
  //Pull in global variable of email templates
  $config_array = $GLOBALS['config_array'];

  $isa = ($isa != NULL ? '<br />ISA: ' . $isa : '');
  $punch_out = ($is_punchout === 'yes' ? 'Please configure punch out as well.' : '');
  $consignment = ($is_consignment === 'yes' ? '<br />Consignment Customer: TRUE' : '');
  $ltc = ($is_ltc === 'yes' ? '<br />Long Term Care Customer: TRUE' : '');
  $go_live = ($go_live != '' ? '<br />Go Live Date: ' . $go_live : '');
  $contact_name = ($contact_name != '' ? '<br />Customer Contact: ' . $contact_name : '');
  $contact_email = ($contact_email != '' ? '<br />Customer Email: ' . $contact_email : '');
  $address = ($address != '' ? '<br />Address(s): ' . $address : '');

  //Unless specified otherwise, interpolate the standardized email
  //Vendors currently using Standard Body:
  //Alcon, Olympus
  $email = sprintf($config_array["standard"]["body"], $vendor, $customer, $vendor, $punch_out, $account_no, $isa, $consignment, $ltc, $address);

  //Customized McKesson emails
  if (strtolower($vendor) === 'mckesson'){
    $mckesson = $config_array['mckesson'];
    if ($existing_account === True) {
      $email = sprintf($mckesson['existing_customer']['body'], $vendor, $customer, $account_no, $isa);
    } else {
      $email = sprintf($mckesson['standard']['body'], $customer, $punch_out, $account_no, $isa, $consignment, $ltc);
    }
  }
  //Customized Medline emails
  if (strtolower($vendor) === 'medline'){
    $medline = $config_array['medline'];
    if ($existing_account === True) {
      $email = sprintf($medline['existing_customer']['body'], $vendor, $customer, $account_no, $isa);
    } else {
      if ($is_punchout === 'yes') {
        $email = sprintf($medline['cxml']['body'], $customer, $account_no, $go_live, $consignment);
      } else {
        $email = sprintf($medline['edi']['body'], $customer, $account_no, $isa, $go_live, $consignment);
      }
    }
  }
  //Customized Henry Schein emails
  if (strpos(strtolower($vendor), "henry schein") !== false){
    $body = $config_array['henry_schein']['body'];
    $email = sprintf($body, $vendor, $customer, $account_no, $contact_name, $contact_email);
  }
  //Customized Office Depot + Staples
  if (strtolower($vendor) === 'office depot'
    || strpos(strtolower($vendor), "staples") !== false){
    $body = $config_array['od_staples']['body'];
    $email = sprintf($body, $vendor, $customer, $account_no, $address);
  }

  return $email;
}






?>
