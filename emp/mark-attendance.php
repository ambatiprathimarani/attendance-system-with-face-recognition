<?php
session_start();
error_reporting(0);
require_once('include/config.php');

// List of allowed IP addresses
//$allowedIPs = ['127.0.0.1', '10.99.101.54', '10.99.101.95']; // Replace with actual allowed IPs

$allowedIPs = [];
try {
    $sql = "SELECT allowip FROM tblallowedip";
    $query = $dbh->prepare($sql);
    $query->execute();
    $allowedIPs = $query->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching allowed IPs: " . $e->getMessage() . "');</script>";
    exit();
}

// Get user's IP address
$userIP = $_SERVER['REMOTE_ADDR'];
if ($userIP == '::1') {
    $userIP = '127.0.0.1'; // Handle localhost IPv6 address
}

// Check if the user's IP is allowed
$isIPAllowed = in_array($userIP, $allowedIPs);

// Redirect if not logged in
if (strlen($_SESSION["Empid"]) == 0) {   
    header('location:index.php');
    exit();
}

// Handle check-out submission
if (isset($_POST['checkout']) && $isIPAllowed) {
    $empid = $_SESSION['id'];
    $checkoutTime = date('Y-m-d H:i:s', time());
    $currentDate = date('Y-m-d');
    $exitRemark = isset($_POST['remark']) ? trim($_POST['remark']) : '';

    $sql = "UPDATE tblattendance 
            SET checkOutTime = :checkoutTime, exitremark = :exitRemark
            WHERE DATE(checkInTime) = :currentDate 
            AND empId = :empid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':checkoutTime', $checkoutTime, PDO::PARAM_STR);
    $query->bindParam(':exitRemark', $exitRemark, PDO::PARAM_STR);
    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
    $query->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);

    if ($query->execute()) {
        session_destroy(); // End the session
        echo "<script>alert('Check-out successful. You will now be logged out.');</script>";
        echo "<script>window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error during check-out. Please try again.');</script>";
    }
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
    <!-- Font-icon CSS
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
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
                    <hr />

                    <!-- Check today's attendance -->
                    <?php
                    $cdate = date('Y-m-d');
                    $empid = $_SESSION['id'];
                    $sql = "SELECT id, checkInTime, checkInIP, checkOutTime FROM tblattendance WHERE empId = :empid AND DATE(checkInTime) = :cdate";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                    $query->bindParam(':cdate', $cdate, PDO::PARAM_STR);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                    if ($query->rowCount() == 0) { ?>
                        <!-- Check-in form -->
                        <form class="row" method="post" action="thank-you.php">
                            <h1 style="color:blue; padding-top:1%;">Hello, Please Mark Your Attendance for today</h1>
                            <div class="form-group col-md-6">
                                <label class="control-label">IP Address</label>
                                <?php 
                                $ip = $userIP;
                                ?>
                                <input type="text" name="ipaddress" id="ipaddress" class="form-control" value="<?php echo $ip; ?>" readonly autocomplete="off">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Check-in Time (Current Time)</label>
                                <input class="form-control" type="text" name="checkintime" id="checkintime" value="<?php echo date('d-m-Y H:i:s', time()); ?>" readonly autocomplete="off">
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label">Enter reason if late Checkin</label>
                                <textarea name="remark" id="remark" placeholder="Enter Remark (If any)" class="form-control" autocomplete="off"></textarea>
                            </div>
                            <div class="form-group col-md-4 align-self-end">
                                <input type="submit" name="Submit" id="Submit" class="btn btn-primary" value="Submit" <?php echo $isIPAllowed ? '' : 'disabled'; ?>>
                            </div>
                        </form>
                    <?php } else { ?>
                        <div align="center">
                            <img src="../img/attendance.png" alt="Attendance">
                            
                            <?php foreach ($results as $result) { ?>
                                <table class="table table-hover table-bordered">
                                    <tr>
                                        <th>Check-in IP</th>
                                        <td><?php echo $result->checkInIP; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Check-in Time</th>
                                        <td><?php echo date("d-m-Y H:i:s", strtotime($result->checkInTime)); ?></td>
                                    </tr>
                                    <?php if ($result->checkOutTime) { ?>
                                    <tr>
                                        <th>Check-out Time</th>
                                        <td><?php echo date("d-m-Y H:i:s", strtotime($result->checkOutTime)); ?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                            <form method="post">
                                <div class="form-group col-md-12">
                                        <label class="control-label">Enter reason if early checkout)</label>
                                        <textarea name="remark" id="remark" placeholder="Enter Remark (If any)" class="form-control" autocomplete="off"></textarea>
                                </div>
                                <input type="submit" name="checkout" value="Check-out" class="btn btn-danger btn-lg" <?php echo $isIPAllowed ? '' : 'disabled'; ?>>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>
    <!-- Essential JavaScripts -->
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/plugins/pace.min.js"></script>
</body>
</html>
