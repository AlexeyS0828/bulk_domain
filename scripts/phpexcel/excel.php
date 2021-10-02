<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

echo date('H:i:s') , " Create new PHPExcel object" , EOL;


$excel2 = PHPExcel_IOFactory::createReader('Excel2007');
$excel2 = $excel2->load('AIC_LIST.xlsx'); // Empty Sheet
$excel2->setActiveSheetIndex(0);
$excel2->getActiveSheet()->setCellValue('C6', '4')
    ->setCellValue('C7', '5')
    ->setCellValue('C8', '684588')       
    ->setCellValue('C9', '7');


$excel2->setActiveSheetIndex(1);
$excel2->getActiveSheet()->setCellValue('A7', '4')
    ->setCellValue('C7', '5');
$objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
$objWriter->save('test.xlsx');




$sheet = $excel2->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();
$sheetname = $sheet->getTitle();
$start=1;
$table = "<table><caption>$sheetname</caption>";
$rows = $sheet->rangetoArray('A'.$start.':'.$highestColumn . $highestRow, NULL, True, True, False);
foreach ($rows as $row => $cols) {      
    $line = '<tr class="'.$row.'">';
    foreach($cols as $col => $cell){
        $line .= "<td>".$cell."</td>"; // ... or some other separator           
    }
    if($row == 0) $line = str_replace("td>","th>", $line);
    $table .= $line.'</tr>';
 } 
$table .= "</table>";

echo $table;