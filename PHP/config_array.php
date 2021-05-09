<?php
$config_array = array(
  "standard" => array(
    "header" => "EDI request for Comapny - AS2 connection",
    "body" => '
      Hello %s Team:<br />
      <br />
      Comapny Customer "%s" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with %s at this time so please use the existing Comapny AS2 connection. %s<br />
      <br />
      Account Number(s): %s%s<br />
      EDI Documents to Transfer: 810, 850, 855, 856%s%s%s<br />
      <br />
      Please let us know if you need any additional information.<br />
      <br />
      Thank you!<br />

      '
  ),
  "henry_schein" => array(
    "header" => "",
    "body" => '
      Hello %s Team:<br />
      <br />
      Comapny Customer "%s" is requesting to be brought live on EDI.  Please setup accounts for EDI orders, EDI invoices and punch-out.<br />
      <br />
      Account Number(s): %s%s%s<br />
      <br />
      Please let us know if you need any additional information.<br />
      <br />
      Thank you!<br />

      '
  ),
  "mckesson" => array(
    "existing_customer" => array(
      "header" => "",
      "body" => '
        Hello %s Team:<br />
        <br />
        Comapny Customer "%s" is requesting to add another account number to their existing EDI configuration.<br />
        <br />
        Account Number(s): %s%s<br />
        <br />
        Please let us know if you need any additional information.<br />
        <br />
        Thank you!<br />

        '
    ),
    "standard" => array(
      "header" => "",
      "body" => '
        Hello McKesson Team:<br />
        <br />
        Comapny Customer "%s" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with McKesson at this time so please use the existing Comapny AS2 connection. %s<br />
        <br />
        Account Number(s): %s%s<br />
        EDI Documents to Transfer: 810, 850, 855, 856%s%s<br />
        <br />
        Please let us know if you need any additional information.<br />
        <br />
        Thank you!<br />

        '
    )

  ),
  "medline" => array(
    "cxml" => array(
      "header" => "",
      "body" => '
        Hello Medline Team:<br />
        <br />
        Comapny Customer "%s" is requesting CXML / punch-out be configured in addition to EDI which weve already requested. Comapny has many EDI / CXML integrated clients with Medline at this time so please configure this customer like the other Comapny associated accounts.<br />
        <br />
        Account Number(s): %s%s%s<br />
        <br />
        Please let us know if you need any additional information.<br />
        <br />
        Thank you!<br />
      '
    ),
    "edi" => array (
      "header" => "",
      "body" => '
        Hello Medline Team:<br />
        <br />
        Comapny Customer "%s" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with Medline at this time so please use the existing Comapny AS2 connection.<br />
        <br />
        Account Number(s): %s%s%s<br />
        EDI Documents to Transfer: 810, 850, 855, 856%s<br />
        <br />
        Please let us know if you need any additional information.<br />
        <br />
        Thank you!<br />

        '
    ),
      "existing_customer" => array(
          "header" => "",
          "body" => '
        Hello %s Team:<br />
        <br />
        Comapny Customer "%s" is requesting to add another account number to their existing EDI / punch-out configuration.<br />
        <br />
        Account Number(s): %s%s<br />
        <br />
        Please let us know if you need any additional information.<br />
        <br />
        Thank you!<br />

        '
      )
  ),
    "olympus" => array(
        "header" => "EDI request for Comapny - AS2 connection",
        "body" => '
      Hello %s Team:<br />
      <br />
      Comapny Customer "%s" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with %s at this time so please use the existing Comapny AS2 connection. %s<br />
      <br />
      Account Number(s): %s%s<br />
      EDI Documents to Transfer: 810, 850, 855, 856%s%s<br />
      <br />
      Please let us know if you need any additional information.<br />
      <br />
      Thank you!<br />

      '
    ),
    "od_staples" => array(
        "header" => "EDI request for Comapny - AS2 connection",
        "body" => '
      Hello %s Team:<br />
      <br />
      Comapny Customer "%s" is requesting to be brought live on CXML.  We have multiple CXML integrated clients with Office Depot at this time. Please setup punch-out as well.<br />
      <br />
      Account Number(s): %s%s<br />
      <br />
      Please let us know if you need any additional information.<br />
      <br />
      Thank you!<br />

      '
    )
);

 ?>
