<?php 
session_start();
error_reporting(0);
require_once('include/config.php');

// Redirect to login page if session is not set
if(strlen($_SESSION["Empid"]) == 0) {   
    header('location:index.php');
    exit();
}

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
              <h2 align="center">Mark Today's Attendance</h2>
              <hr />
             <!-- Success Message -->
          <?php if($msg){ ?>
          <div class="alert alert-success" role="alert">
          <strong>Well done!</strong> <?php echo htmlentities($msg);?>
          </div>
          <?php } ?>

          <!-- Error Message -->
          <?php if($errormsg){ ?>
          <div class="alert alert-danger" role="alert">
          <strong>Oh snap!</strong> <?php echo htmlentities($errormsg);?></div>
          <?php } ?>
  
            <div class="tile-body">
<?php 
if(isset($_POST['Submit'])) {
    $empid = $_SESSION['id'];
    $checkintime = date('Y-m-d H:i:s', time());
    $checkinip = $_POST['ipaddress'];
    $remark = $_POST['remark'];

    $cdate = date('Y-m-d');
    $sql = "SELECT id FROM tblattendance WHERE empId=:empid and date(checkInTime)=:cdate";
    $query = $dbh->prepare($sql);
    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
    $query->bindParam(':cdate', $cdate, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() == 0) {
        $query = $dbh->prepare("INSERT INTO tblattendance(empId, checkInTime, checkInIP, remark) 
                                VALUES(:empid, :checkintime, :checkinip, :remark)");
        $query->bindParam(':empid', $empid, PDO::PARAM_STR);
        $query->bindParam(':checkintime', $checkintime, PDO::PARAM_STR);
        $query->bindParam(':checkinip', $checkinip, PDO::PARAM_STR);
        $query->bindParam(':remark', $remark, PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId > 0) { ?>
          <div align="center">
          <img src="../img/attendance.png">
          <h1 style="color:blue; padding-top:1%;">Thank you for Today!</h1>
          <hr />
          </div>

          <!-- Logout and Redirect -->
          <?php
          session_destroy();
          echo "<script>alert('Check-in successful. You will now be logged out.');</script>";
          echo "<script>window.location.href='index.php';</script>";
          exit();
        } 
    } else { 
        header('location:mark-attendance.php'); 
        exit();
    }
}
?>

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
</body>
</html>
