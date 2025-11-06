<?php
session_start();
include 'include/config.php';

if ($_GET['action'] == 'replace') {
    $empid = $_GET['empid'];
    $fdate = $_GET['fdate'];
    $remark = $_GET['remark'];

    $updateQuery = $dbh->prepare("UPDATE tblattendance SET remark = :remark WHERE empid = :empid AND DATE(checkInTime) = :fdate");
    $updateQuery->bindParam(':remark', $remark, PDO::PARAM_STR);
    $updateQuery->bindParam(':empid', $empid, PDO::PARAM_INT);
    $updateQuery->bindParam(':fdate', $fdate, PDO::PARAM_STR);
    $updateQuery->execute();

    echo "<script>alert('Remark replaced successfully.'); window.location.href='add-remark.php';</script>";
}
?>
