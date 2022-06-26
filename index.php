<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIC Lucky Draw</title>
    <link rel="icon" type="image/png" href="../public/images/icons/favicon.ico" />  
    <?php include 'links.php'; ?>
    <style>
        body{
            margin-bottom: 50px;
        }
        .scrollable{
            height: 700px;
            overflow-y: scroll;
        }
        .heading{
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .show{
            margin-top:20px;
            display:flex;
            align-items:center;
            justify-content:space-around;
        }

        @media(max-width:560px){
            .heading{
                flex-direction: column;
            }
            .show{
                flex-direction: column;
            }
            .show_number{
                margin-bottom:20px;
            }
        }
       
    </style>
</head>
<body>
 <!-- upload data -->
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4">
                <div class="heading">
                        <h5 class="mb-3">LIC Lucky Draw</h5>
                </div>   
                <hr>
                <?php
                if(isset($_SESSION['status']))
                {
                    echo "<h5 style='color:green'>".$_SESSION['status']."</h5>";
                    unset($_SESSION['status']);
                }
                if(isset($_SESSION['err']))
                {
                    echo "<h5 style='color:red'>".$_SESSION['err']."</h5>";
                    unset($_SESSION['err']);
                }
                ?>
                <form action="code.php" method="post" enctype="multipart/form-data">
                    <div class="card card-body shadow">
                        <div class="row">
                            <div class="col-md-2 my-auto">
                                <h5>Select File</h5>
                            </div>
                            
                            <div class="col-md-4">
                                <input type="file" accept=".csv ,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="import_file" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <input type="submit" name="import_file_btn" class="btn btn-primary" value="Upload File">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
       <!-- Show number of verify -->
       <div class="container show">
        <div class="card show_number" style="width: 18rem;">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total No. of People</h6>    
                <h4 class="card-title">
                    <?php
                        include 'conn.php';
                        $total_query = "SELECT DISTINCT agency_code FROM main";
                        $total_res = mysqli_query($con,$total_query);
                        echo mysqli_num_rows($total_res);
                    ?>
                </h4>
            </div>
        </div>
        <div class="card show_number" style="width: 18rem;">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">No. of Lucky People</h6>    
                <h4 class="card-title">
                <?php
                        $total_ver_query = "SELECT DISTINCT agency_code FROM main WHERE lucky=1 ";
                        $total_ver_res = mysqli_query($con,$total_ver_query);
                        echo mysqli_num_rows($total_ver_res);
                    ?>
                </h4>
            </div>
        </div>
    </div>
    <br>
    <!-- Random Number Button -->
    
        <?php
        if(mysqli_num_rows($total_ver_res)<300){
            ?>
            <div class="container">
    <div class="card card-body shadow">
                        <div class="row">
    <div class="col-md-12 mt-4">
<form action="rand.php" method="post">
                                <input type="submit" name="random_btn" class="btn btn-warning" value="Lucky No.">
            </form>
            </div>
            </div></div>
            </div>
            <?php
        }
        ?>
    
    <!-- Show/Download Data -->
<div class="container">
    <div class="col-md-12 mt-4">
        <div class="card shadow">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Lucky Draw Data</h5>
                        <?php
                            if(isset($_SESSION['up_status']))
                            {
                                echo "<h5 style='color:green'>".$_SESSION['up_status']."</h5>";
                                unset($_SESSION['up_status']);
                            }
                            if(isset($_SESSION['up_err']))
                            {
                                echo "<h5 style='color:red'>".$_SESSION['up_err']."</h5>";
                                unset($_SESSION['up_err']);
                            }
                ?>
                    </div>
                    <div class="col-md-6">
                                <form action="code.php" method="post">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="export_file_type" required class="form-control">
                                                <option value="">--Select Any One--</option>
                                                <option value="xlsx">xlsx</option>
                                                <option value="xls">xls</option>
                                                <option value="csv">csv</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" name="export_btn" class="btn btn-primary">Export</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive ">
    <table class="table table-fixed table-condensed table-striped table-bordered table-fluid" id="myTable">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Branch</th>
                <th scope="col">Branch Name</th>
                <th scope="col">Name</th>
                <th scope="col">Agency Code</th>
                <th scope="col">Total NOP</th>
                <th scope="col">Basic Coupon</th>
                <th scope="col">Coupon Early</th>
                <th scope="col">Coupon Extra</th>
                <th scope="col">Total Coupon</th>
                <th scope="col">Coupon Code</th>
                <!-- <th scope="col">Delete</th> -->
                <!-- <th scope="col">From</th>
                <th scope="col">To</th> -->
            </tr>
        </thead>
        <tbody>
            <?php 
            include 'conn.php';
            $lucky_query = "SELECT * FROM main WHERE lucky_code=1 ORDER BY coupon_time ASC ";
            $lucky_result = mysqli_query($con,$lucky_query);
            if(mysqli_num_rows($lucky_result)>0)
            {
                foreach($lucky_result as $row)
                {
                    ?>

            
            <tr>
                <th><?= $row['branch']; ?></th>
                <th><?= $row['b_name']; ?></th>
                <th><?= $row['name']; ?></th>
                <th><?= $row['agency_code']; ?></th>
                <th><?= $row['total_nop']; ?></th>
                <th><?= $row['basic_coupon']; ?></th>
                <th><?= $row['coupon_early']; ?></th>
                <th><?= $row['coupon_extra']; ?></th>
                <th><?= $row['total_coupon']; ?></th>
                <th><?= $row['coupon_code']; ?></th>               
            </tr>
            <?php
                }
            }else{?>
                    <tr>
        <td colspan="11" style="text-align: center; font-weight: bold">
            No Record Found
        </td>
        </tr>
            <?php
            }
            ?>
        </tbody>

</table>
    </div>
    </div>

    <script>
    $(document).ready( function () {
    $('#myTable').DataTable();
} );
    </script>
</body>
</html>