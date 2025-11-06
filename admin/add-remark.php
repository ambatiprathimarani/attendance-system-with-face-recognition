<?php
session_start();
error_reporting(0);
include 'include/config.php';

if (strlen($_SESSION["adminid"]) == 0) {
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
                    <h3 class="tile-title">Add Remark</h3>
                    <div class="tile-body">
                        <form class="row" method="post">
                            <div class="form-group col-md-12">
                                <label class="control-label">Employee</label>
                                <select name="empid" id="empid" class="form-control" required>
                                    <option value="">--select--</option>
                                    <?php 
                                    $stmt = $dbh->prepare("SELECT *, tblemployee.id as empid FROM tblemployee LEFT JOIN tbldepartment ON tblemployee.department_name = tbldepartment.id ORDER BY fname");
                                    $stmt->execute();
                                    $results = $stmt->fetchAll();
                                    foreach ($results as $result) {
                                        echo "<option value='".$result['empid']."'>".$result['fname']." ".$result['lname']." (".$result['DepartmentName'].")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Date</label>
                                <input class="form-control" type="date" name="fdate" id="fdate" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Remark</label>
                                <input class="form-control" type="text" name="remark" id="remark" placeholder="Enter Remark" required>
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
        if (isset($_POST['Submit'])) {
            $empid = $_POST['empid'];
            $fdate = $_POST['fdate'];
            $remark = $_POST['remark'];

            // Check if a record exists for the given employee and date
            $query = $dbh->prepare("SELECT * FROM tblattendance WHERE empid = :empid AND DATE(checkInTime) = :fdate");
            $query->bindParam(':empid', $empid, PDO::PARAM_INT);
            $query->bindParam(':fdate', $fdate, PDO::PARAM_STR);
            $query->execute();
            $existingRecord = $query->fetch(PDO::FETCH_ASSOC);

            if ($existingRecord) {
                // Check if a remark already exists
                if (!empty($existingRecord['remark'])) {
                    // Prompt user to replace remark
                    echo "<script>
                        if (confirm('A remark already exists for this date. Do you want to replace it?')) {
                            window.location.href = 'process-remark.php?action=replace&empid=$empid&fdate=$fdate&remark=$remark';
                        } else {
                            alert('Remark not updated.');
                        }
                    </script>";
                } else {
                    // Add remark since it doesn't exist
                    $updateQuery = $dbh->prepare("UPDATE tblattendance SET remark = :remark WHERE empid = :empid AND DATE(checkInTime) = :fdate");
                    $updateQuery->bindParam(':remark', $remark, PDO::PARAM_STR);
                    $updateQuery->bindParam(':empid', $empid, PDO::PARAM_INT);
                    $updateQuery->bindParam(':fdate', $fdate, PDO::PARAM_STR);
                    $updateQuery->execute();
                    echo "<script>alert('Remark added successfully.');</script>";
                }
            } else {
                // Record not found
                echo "<script>alert('Record not found for the selected employee and date.');</script>";
            }
        }
        ?>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/plugins/pace.min.js"></script>
</body>
</html>
<?php } ?>
