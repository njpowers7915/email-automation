<?php
include 'config_array.php';
include 'send_message.php';
include 'update_excel.php';
include_once 'post_to_slack.php';

function process_request_and_send_email($request) {
  print_r($request);
  $data = $request;

  $message_id = NULL;
  $automated_vendor = False;

  $date = $data['request_date'];
  $imp_email = $data['record_owner'];
  $site = $data['site'];
  $vendor = $data['vendor'];

  $contact_name = $data['customer_contact_name'];
  $contact_email = $data['customer_contact_email'];
  $account_no = $data['account_no'];
  $is_edi = $data['is_edi'];
  $is_punchout = $data['is_punchout'];
  $is_ltc = $data['is_ltc'];
  $is_consignment = $data['is_consignment'];
  $go_live_date = $data['go_live_date'];
  $isa_requirement = $data['isa_requirement'];
  $is_810 = $data['810'];
  $is_855 = $data['855'];
  $is_856 = $data['856'];

  //Olympus Requires Facility Address with their requests
  $address = $data['facility_address'];

  print_r($data);
  print_r($site);
  $ccs = [];
  // //Create list of EDI docs to request to be set up
  //   edi_docs = ('850' if is_edi === 'yes' else None)
  //   if edi_docs === '850':
  //       for doc in ['810', '855', '856']:
  //           if data[doc] === 'yes':
  //               edi_docs = edi_docs + ', ' + doc
  //   print(edi_docs)

  //This logic replaces site-specific ISA with vendor requirements
    if ($isa_requirement === 'confirm with vendor') {
      trigger_error('No ISA value provided', E_USER_WARNING);
      $isa = NULL;
    } elseif ($isa_requirement != '') {
      $isa = $isa_requirement;
    } else {
      if ($data['isa'] === '') {
        $isa = strtoupper($data['site_slug']);
      } else {
        try {
          $isa = explode(',', $data['isa'])[0];
        } catch (Exception $e) {
          trigger_error('ISA could not be generated from values provided', E_USER_WARNING);
        }
      }
    }

    if (strlen($isa) > 15) {
      $isa = substr($isa, 0, 15);
      //Post warning to Slack if a new ISA needs to be created
      post_to_slack($automated_vendor, $vendor, $site, $ccs, $vendor_email=NULL, $warning_message = 'isa', $message_id, $isa);
    }


    //employee to be cc'd on all requests
    $company_resource = $data['company_resource'];
    if ($company_resource === NULL) {
      $company_resource = 'employee@company.com';
    }

    //Verify vendor email addresses are valid
    //If no vendor email addresses are valid, send out "warning" to imp specialist
    $warning_message = NULL;
    function check($email) {
      $regex = "/^[a-z0-9]+[\._]?[a-z0-9]+[@]\w+[.]\w{2,3}$/";
      $email = (preg_match($regex, $email) === 1 ? $email : NULL);
      if ($email === NULL) {
        trigger_error('Invalid Email: ' . $email, E_USER_WARNING);
      }
      return $email;
    }

    $vendor_email = check(trim(strtolower($data['vendor_email'])));
    $vendor_email_2 = check(trim(strtolower($data['vendor_email_2'])));
    $vendor_rep_email = check(trim(strtolower($data['vendor_rep_email'])));
    if (($vendor_email === NULL) and ($vendor_rep_email != NULL)){
      $vendor_email = $vendor_rep_email;
      $vendor_rep_email = NULL;
    }
    if (($vendor_email === NULL) and ($vendor_rep_email === NULL)){
      $vendor_email = $imp_email;
      trigger_error("EDI request for " . $site . " - " . $vendor . " did not go out because a vendor email was not provided", E_USER_WARNING);
      $warning_message = "EDI request for " . $site . " - " . $vendor . " did not go out because a vendor email was not provided";
      post_to_slack($warning_message = $warning_message);
      exit();
    }

    //Add secondary vendor emails to list of ccs
    array_push($ccs, $imp_email, $company_resource);
    foreach ([$vendor_email, $vendor_email_2, $vendor_rep_email] as $email) {
      if ($email != NULL) {
        array_push($ccs, $email);
      }
    }

    // Specify whether EDI request is for an existing configuration
      $existing_account = False;
      if ($data['request_type'] === "Add Account # to Existing Config") {
        $existing_account = True;
      }
      if ($existing_account === True) {
        if (strtolower($vendor) === 'mckesson') {
          $vendor_email = 'email@vendor.com';
          $ccs = [$imp_email, $company_resource];
        }
      }

    //Only attach excel file for McKesson
    if (strtolower($vendor) === 'mckesson' or strtolower($vendor) === 'medline') {
      $excel_file_path = update_excel($vendor, $site, $account_no, $is_punchout, $date, $isa, $contact_name, $contact_email);
    } else {
      $excel_file_path = NULL;
    }

    //Code to generate and send emails
    //$automated_vendor is a boolean flag that needs to be set to True or else failure messages will go out for vendors that have not yet been automated
    if (strtolower($vendor) === 'medline') {
      $automated_vendor = True;
      if ($is_punchout === 'yes') {
        $is_punchout = False;
        $vendor_email = 'EDISupport@medline.com';
        $ccs = [$imp_email, $company_resource];
        //$vendor_email = 'employee@company.com';
        //ccs = ['njpowers7915@gmail.com', 'njp3jk@virginia.edu'];
        $message_id_edi = send_message($vendor_email, $vendor, $site, $is_punchout, $account_no, $ccs, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live_date, $address, $isa, $excel_file_path, $warning_message);
        $is_punchout = 'yes';
        $vendor_email = 'helpdesk@medline.com';
        $ccs = [$imp_email, $company_resource];
        //$vendor_email = 'employee@company.com';
        //$ccs = ['njpowers7915@gmail.com', 'njp3jk@virginia.edu'];
        $message_id_cxml = send_message($vendor_email, $vendor, $site, $is_punchout, $account_no, $ccs, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live_date, $address, $isa, $excel_file_path, $warning_message);
        $message_id = $message_id_edi . ',' . $message_id_cxml;
      } else {
        //$vendor_email = 'employee@company.com';
        //$ccs = ['njpowers7915@gmail.com', 'njp3jk@virginia.edu'];
        $message_id = send_message($vendor_email, $vendor, $site, $is_punchout, $account_no, $ccs, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live_date, $address, $isa, $excel_file_path, $warning_message);
      }
    }

    if (strtolower($vendor) === 'mckesson'
        || strtolower($vendor) === 'office depot'
        || strpos(strtolower($vendor), "henry schein") !== false
        || strpos(strtolower($vendor), "alcon") !== false
        || strpos(strtolower($vendor), "olympus") !== false
        || strpos(strtolower($vendor), "staples") !== false
    ) {
      $automated_vendor = True;
      //$vendor_email = 'employee@company.com';
      //$ccs = ['njpowers7915@gmail.com', 'njp3jk@virginia.edu'];
      $message_id = send_message($vendor_email, $vendor, $site, $is_punchout, $account_no, $ccs, $is_consignment, $is_ltc, $existing_account, $contact_name, $contact_email, $go_live_date, $address, $isa, $excel_file_path, $warning_message);
    }

    post_to_slack($automated_vendor, $vendor, $site, $ccs, $vendor_email, $warning_message, $message_id, $isa);

    return '200';

    //Work on Archiving Process of old excel files
    // if ($excel_file_path !== NULL) {
    //   rename($excel_file_path, "/archive_files" . $excel_file_path);
    // }

}

?>
