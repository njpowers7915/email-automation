<?php
require 'vendor/autoload.php';

function update_excel($vendor, $site, $account_no, $is_punchout, $date, $isa, $customer_contact_name, $customer_contact_email) {
  if (strtolower($vendor) === 'medline') {
    $fileName = 'excel/template.xlsx';
    $inputType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($fileName);
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputType);
    $phpExcel = $reader -> load($fileName);
    //sheet active refers to the most recently opened sheet in the workbook
    //ALWAYS close the request sheet last when editing template excel files!!!
    $sheet = $phpExcel -> getActiveSheet();

    $sheet -> getCell('A3') -> setValue($site);
    $sheet -> getCell('B3') -> setValue($isa);
    $sheet -> getCell('C3') -> setValue($isa);

    try {
      $account_list = explode(',', $account_no);
      $i = 2;
      foreach ($account_list as $account) {
        $account = trim($account);
        $cell = 'D' . ++$i;
        $sheet -> getCell($cell) -> setValue($account);
        print_r($cell . "\n");
        print_r($account . "\n");
      }
    } catch (Exception $e) {
      trigger_error('Account #s could not be added to spreadsheet', E_USER_WARNING);
    }

    //Fixes issue with wonky colors being generated
    $sheet -> getStyle('A1:M500') -> getFont() -> getColor() -> setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);

  }

  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($phpExcel, "Xlsx");
  $filepath = "excel/" . strtolower($vendor) . "/" . $date . "_" . $vendor ."_". $site . ".xlsx";
  $writer -> save($filepath);
  return $filepath;
}


?>
