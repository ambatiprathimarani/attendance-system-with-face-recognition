<?php session_start();
//error_reporting(0);
require_once('include/config.php');
if(strlen( $_SESSION["adminid"])==0){   
header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html lang="en">
  <head>

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
      <div class="app-title">
        <div>
          <h1><i class="fa fa-dashboard"></i> Dashboard</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        </ul>
      </div>
      <div class="row">
  <?php
$query=$dbh->prepare("SELECT id FROM tblemployee");
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
$regemp=$query -> rowCount();
?>

        <div class="col-md-6 col-lg-6">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info"><a href="manage-employee.php">
              <h4>Registered Employees</h4>
              <p><b><?php echo $regemp;?></b></p></a>
            </div>
          </div>
        </div>
<?php
$ret=$dbh->prepare("SELECT id FROM tbldepartment");
$ret-> execute();
$resultss = $ret -> fetchAll(PDO::FETCH_OBJ);
$listeddept=$ret -> rowCount();
?>
        <div class="col-md-6 col-lg-6">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
            <div class="info">
              <a href="manage-department.php">
              <h4>Listed Departments</h4>
              <p><b><?php echo $listeddept;?></b></p>
            </a>
            </div>
          </div>
        </div>
      </div>
<!-------------------------------->
<hr />
<h3 align="center">Attendance Details </h3>
<hr />
<div class="row">
  <?php
$query=$dbh->prepare("SELECT * FROM tblattendance where date(checkInTime)=CURDATE()");
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
$todayattendance=$query -> rowCount();
?>
        <div class="col-md-6 col-lg-6">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
            <div class="info"><a href="todays-attendance.php">
              <h4>Employee CheckIn Today</h4>
              <p><b><?php echo $todayattendance;?></b></p>
            </a>
            </div>
          </div>
        </div>
<?php
 $sql=$dbh->prepare("SELECT * FROM tblattendance where date(checkInTime)=CURDATE()-1");
 $sql-> execute();
$result = $sql -> fetchAll(PDO::FETCH_OBJ);
$yesterdayattendance=$sql -> rowCount();
?>
        <div class="col-md-6 col-lg-6">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-calendar fa-3x"></i>
            <div class="info"> <a href="yesterday-attendance.php">
              <h4>Employee CheckIn Yesterday</h4>
              <p><b><?php echo $yesterdayattendance;?></b></p>
            </a>
            </div>
          </div>
        </div>        
      </div> 
    </div>
  <hr />
      <h3 align="center">Administration</h3>
    <hr />
  <div class="row">
    <div class="col-md-6 col-lg-6">
      <div class="widget-small danger coloured-icon"><i class="icon fa fa-users fa-3x"></i>
          <div class="info"><a href="add-remark.php">
              <h4>Add Remark</h4>              
          </div>
        </div>
    </div>    

    <div class="col-md-6 col-lg-6">
      <div class="widget-small primary coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
          <div class="info"><a href="manage-ip.php">
              <h4>Manage IP to mark attendance</h4>              
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
    <script src="../js/plugins/pace.min.js"></script>
    <script type="text/javascript" src="../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
    <script type="text/javascript" src="../js/plugins/chart.js"></script>
  </body>
</html>
<?php } ?>
