<?php

session_start();
include 'conn.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

if(isset($_POST['export_btn'])){
    $ext = $_POST['export_file_type'];
    $fileName = "LIC-lucky-".time();

    $export_query = "SELECT * FROM main WHERE lucky_code=1 ORDER BY coupon_time ASC";
    // $export_query = "SELECT DISTINCT agency_code FROM main WHERE lucky=1";
    $export_query_result = mysqli_query($con,$export_query);

    if(mysqli_num_rows($export_query_result)>0)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // $sheet->setCellValue('A1', 'Sr. No.');
        $sheet->setCellValue('B1', 'Branch');
        $sheet->setCellValue('C1', 'Branch Name');
        $sheet->setCellValue('D1', 'Name');
        $sheet->setCellValue('E1', 'Agency Code');
        $sheet->setCellValue('F1', 'Total NOP');
        $sheet->setCellValue('G1', 'Basic Coupon');
        $sheet->setCellValue('H1', 'Coupon Early');
        $sheet->setCellValue('I1', 'Coupon Extra');
        $sheet->setCellValue('J1', 'Total Coupon');
        $sheet->setCellValue('K1', 'Coupon Code');
        $rowcount = 2;
        $sr_no = 1;

        foreach($export_query_result as $ex_data){
            // $sheet->setCellValue('A' . $rowcount, $sr_no);
            $sheet->setCellValue('B' . $rowcount, $ex_data['branch']);
            $sheet->setCellValue('C' . $rowcount, $ex_data['b_name']);
            $sheet->setCellValue('D' . $rowcount, $ex_data['name']);
            $sheet->setCellValue('E' . $rowcount, $ex_data['agency_code']);
            $sheet->setCellValue('F' . $rowcount, $ex_data['total_nop']);
            $sheet->setCellValue('G' . $rowcount, $ex_data['basic_coupon']);
            $sheet->setCellValue('H' . $rowcount, $ex_data['coupon_early']);
            $sheet->setCellValue('I' . $rowcount, $ex_data['coupon_extra']);
            $sheet->setCellValue('J' . $rowcount, $ex_data['total_coupon']);
            $sheet->setCellValue('K' . $rowcount, $ex_data['coupon_code']);
            // $sheet->setCellValue('K' . $rowcount, $ex_data['lucky']);
            $rowcount++;
        }

        if($ext == 'xlsx')
        {
            $writer = new Xlsx($spreadsheet);
            $final_fileName = $fileName.'.xlsx';
        }
        elseif($ext == 'xls')
        {
            $writer = new Xls($spreadsheet);
            $final_fileName = $fileName.'.xls';
        }
        elseif($ext == 'csv')
        {
            $writer = new Csv($spreadsheet);
            $final_fileName = $fileName.'.csv';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.urlencode($final_fileName).'"');
        $writer->save('php://output');
    }
    else{
        $_SESSION['err'] = "No Record Found To Export";
        header("Location: index.php");
    }
}

if(isset($_POST['import_file_btn']))
{
    $allowed_ext = ['xls','csv','xlsx'];

    $fileName = $_FILES['import_file']['name'];
    $checking = explode(".",$fileName);
    $file_ext = end($checking);

    if(in_array($file_ext,$allowed_ext))
    {
        
        $targetPath = $_FILES['import_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetPath);
        $data = $spreadsheet -> getActiveSheet() -> toArray();

        // if($data[0][0]=="VSN Code" and $data[0][1]=="Sr. No.")
        // {

        

        for($row=6;$row<sizeof($data);$row++)
        {
            $branch = $data[$row][1];
            $b_name = $data[$row][2];
            $name = $data[$row][3];
            $agency_code = $data[$row][4];
            $total_nop = $data[$row][5];
            $basic_coupon = $data[$row][6];
            $coupon_early = $data[$row][7];
            $coupon_extra = $data[$row][8];
            $total_coupon = $data[$row][9];
            $coupon_from = $data[$row][10];
            $coupon_to = $data[$row][11];
            $coupon_code = $coupon_from;

            // $coupon_to = $data[$row][12];
            // $check_query = "SELECT * FROM main WHERE vsn='$vsn'";
            // $check_result = mysqli_query($con,$check_query);
    
            // if(mysqli_num_rows($check_result)>0){
                //Already Exists
            //     $up_query = "UPDATE verify SET vsn='$vsn',serials='$serials',batch='$batch',mfg='$mfg',company='$company',product='$product' WHERE vsn ='$vsn' ";
            //     $up_result = mysqli_query($con,$up_query);
            //     $msg=1;
            // }
            // else{
                //New Data
                // if($vsn){
            for($x=0;$x<$total_coupon;$x++) {
                    $in_query = "INSERT INTO main(branch,b_name,name,agency_code,total_nop,basic_coupon,coupon_early,coupon_extra,total_coupon,coupon_code) VALUES('$branch','$b_name','$name','$agency_code','$total_nop','$basic_coupon','$coupon_early','$coupon_extra','$total_coupon','$coupon_code')";
                    // echo $in_query.'<br>';
                    $in_result = mysqli_query($con,$in_query);
                    $coupon_code++;
                    $msg=1;
                    // echo $in_query;
                }
            // }
        // }
        }

        if($msg)
        {
            $_SESSION['status'] = "File Imported Successfully";
            header("Location: index.php");
        }
        else
        {
            $_SESSION['err'] = "File Failed To Import";
            header("Location: index.php"); 
        }
    // }
    // else{
    //         $_SESSION['err'] = "Wrong Format Excel File Is Imported";
    //         header("Location: index.php"); 
    //     }
}
else
{
        $_SESSION['err'] = "Invalid File";
        header("Location: index.php");
        exit(0);
    }
}





?>