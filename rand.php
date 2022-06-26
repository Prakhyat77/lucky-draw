<?php
include 'conn.php';
if(isset($_POST['random_btn']))
{
    // echo "pro";
    while(true){
        $random=rand(1,7170);
        $check_query = "SELECT agency_code,coupon_code FROM main where s_no='$random' and lucky=0";
        $check_result = mysqli_query($con,$check_query);
        if(mysqli_num_rows($check_result)>0){
            foreach($check_result as $luckys_peo){
                $agency_code =  $luckys_peo['agency_code'];
                $coupon_code =  $luckys_peo['coupon_code'];
                $coupon_time = date('Y-m-d H:i:s');
                // echo $agency_code.'<br>';
                $up_query = "UPDATE main SET lucky=1 WHERE agency_code='$agency_code'";
                // echo $up_query;
                // echo $coupon_time;
                $up_result = mysqli_query($con,$up_query);
                $update_query = "UPDATE main SET lucky_code=1,coupon_time = '$coupon_time' WHERE coupon_code='$coupon_code'";
                $update_result = mysqli_query($con,$update_query);
                // echo $update_query;
                $luck =1;
            }
            break;
        }
    }
    if($luck==1){
        header("Location: index.php");
    }
    
}
?>