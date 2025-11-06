<?php 
session_start();
error_reporting(0);
require_once('include/config.php');
if(strlen($_SESSION["adminid"])==0)
    {   
header('location:index.php');
}
else{ 


  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="">
  
    <title>Employee Attendance System</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Navbar-->
 <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
     <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
      
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
                        <h2 align="center"><?PHP echo $_GET['empname'];?>'s <?php echo date('F, Y');?> Attendance</h2>
                        <hr />
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>Date</th>
                    <th>CheckIn IP</th>
                    <th>CheckIn Time</th>
                    <th>CheckOut Time</th>
                    <th>Hours</th>
                    <th>Remark (If any)</th>
             
                  </tr>
                </thead>
              
                   <?php
                  $empid=$_GET['empid'];
                  $sql="SELECT * FROM tblattendance where empId=:empid and month(checkInTime)=MONTH(CURRENT_DATE())";
                  $query= $dbh->prepare($sql);
                  $query->bindParam(':empid',$empid, PDO::PARAM_STR);
                  $query-> execute();
                  $results = $query -> fetchAll(PDO::FETCH_OBJ);
                  $cnt=1;
                  if($query -> rowCount() > 0)
                  {
                  foreach($results as $result)
                  {$checkInTime = strtotime($result->checkInTime);
                    $checkOutTime = strtotime($result->checkOutTime);

                    // Calculate hours difference
                    $hoursWorked = 'NA';
                    if ($checkOutTime) {
                        $timeDiff = $checkOutTime - $checkInTime;
                        $hours = floor($timeDiff / 3600);
                        $minutes = floor(($timeDiff % 3600) / 60);
                        $hoursWorked = sprintf("%02d:%02d", $hours, $minutes);}
                  ?>

                <tbody>
                  <tr>
                    <td><?php echo($cnt);?></td>
                    <td><?php $adate=$result->checkInTime;
                  echo date("d-m-Y", strtotime($adate));?></td>
                  <td><?php echo htmlentities($result->checkInIP);?></td>
                  <td><?php $cintime=$result->checkInTime;
                  echo date("d-m-Y H:i:s", strtotime($cintime));?></td>
                  <td>
                    <?php $couttime=$result->checkOutTime;
                    if($couttime!=''):
                  echo date("d-m-Y H:i:s", strtotime($couttime));
                else:
                   echo "NA";
                 endif; ?>

                  </td>
                  <td><?php echo $hoursWorked; ?></td>
                   <td><?php echo htmlentities($result->remark);?></td>
                    <!-- <td><?php echo htmlentities($result->create_date);?></td> -->
                 
           
                  </tr>
                   
                 
                </tbody>

                
                 <?php  $cnt=$cnt+1; } } else { ?>
                  <tr>
                    <th colspan="7" style="color:red; font-size:16px;"> No Record Found</th>
                  </tr>
                 <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <!-- Data table plugin-->
    <script type="text/javascript" src="../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
   
  </body>
</html>
<?php } ?>