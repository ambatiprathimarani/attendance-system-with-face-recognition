<?php session_start();
error_reporting(0);
include  'include/config.php';
if(strlen($_SESSION["Empid"])==0) {   
    header('location:index.php');
} else {
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
<body class="app sidebar-mini">
    <!-- Navbar-->
    <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">

    <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">B/w Dates Attendance Report</h3>
            <div class="tile-body">
              <form class="row" method="post">
                <div class="form-group col-md-6">
                  <label class="control-label">From Date</label>
                  <input class="form-control" type="date" name="fdate" id="fdate" placeholder="Enter From Date" required>
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">To Date</label>
                  <input class="form-control" type="date" name="todate" id="todate" placeholder="Enter To Date" required>
                </div>
                <div class="form-group col-md-4 align-self-end">
                  <input type="Submit" name="Submit" id="Submit" class="btn btn-primary" value="Submit">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <?php 
if(Isset($_POST['Submit'])){
    $fdate=$_POST['fdate'];
    $tdate=$_POST['todate'];
?>
       <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
                <h2 align="center">Attendance Report from <?php echo date("d-m-Y", strtotime($fdate)); ?> To  <?php echo date("d-m-Y", strtotime($tdate)); ?></h2>
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
                  $empid=$_SESSION['id'];
                  $sql="SELECT * FROM tblattendance WHERE empId=:empid AND date(checkInTime) BETWEEN '$fdate' AND '$tdate'";
                  $query= $dbh->prepare($sql);
                  $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt=1;
                  if($query->rowCount() > 0) {
                      foreach($results as $result) {
                          $checkInTime = new DateTime($result->checkInTime);
                          $checkOutTime = new DateTime($result->checkOutTime);
                          $interval = $checkInTime->diff($checkOutTime);
                          $hoursWorked = $interval->format('%h hours %i minutes');
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
                        if($couttime != '') {
                            echo date("d-m-Y H:i:s", strtotime($couttime));
                        } else {
                            echo "NA";
                        } ?>
                    </td>
                    <td><?php echo $couttime != '' ? $hoursWorked : 'NA'; ?></td>
                    <td><?php echo htmlentities($result->remark);?></td>
                    
                  </tr>
                </tbody>
                <?php $cnt=$cnt+1; } } else { ?>
                  <tr>
                    <th colspan="8" style="color:red; font-size:16px;"> No Record Found</th>
                  </tr>
                 <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    </main>
    <!-- Essential javascripts for application to work-->
     <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/plugins/pace.min.js"></script>
    <script type="text/javascript" src="../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
  
  </body>
</html>
<?php } ?>
