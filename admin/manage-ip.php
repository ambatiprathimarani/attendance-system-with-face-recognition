<?php
session_start();
include 'include/config.php';

if (strlen($_SESSION["adminid"]) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['Allow'])) {
        $allowip = $_POST['allowip'];

        // Check if IP already exists
        $sql = "SELECT * FROM tblallowedip WHERE allowip = :allowip";
        $query = $dbh->prepare($sql);
        $query->bindParam(':allowip', $allowip, PDO::PARAM_STR);
        $query->execute();
        
        if ($query->rowCount() > 0) {
            $errormsg = "This IP is already allowed.";
        } else {
            // Insert the new IP
            $sql = "INSERT INTO tblallowedip (allowip) VALUES (:allowip)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':allowip', $allowip, PDO::PARAM_STR);
            $query->execute();
            $msg = "IP added successfully!";
        }
    }

    if (isset($_REQUEST['del'])) {
        $uid = intval($_GET['del']);
        $sql = "DELETE FROM tblallowedip WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $uid, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location.href='manage-ip.php'</script>";
    }
}
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
        <div class="col-md-6">
          <div class="tile">
            <h2 align="center">Allow IP</h2>
            <hr />
            <!-- Success Message -->
            <?php if (isset($msg)) { ?>
              <div class="alert alert-success" role="alert">
                <strong><?php echo htmlentities($msg); ?></strong>
              </div>
            <?php } ?>

            <!-- Error Message -->
            <?php if (isset($errormsg)) { ?>
              <div class="alert alert-danger" role="alert">
                <?php echo htmlentities($errormsg); ?>
              </div>
            <?php } ?>

            <div class="tile-body">
              <form method="post">
                <div class="form-group col-md-12">
                  <label class="control-label">Enter IP</label>
                  <input class="form-control" name="allowip" id="allowip" type="text" placeholder="Enter IP">
                </div>
                <div class="form-group col-md-4 align-self-end">
                  <input type="submit" name="Allow" id="submit" class="btn btn-primary" value="Submit">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <h2 align="center">Allowed IP List</h2>
              <hr />
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>IP</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT * FROM tblallowedip";
                  $query = $dbh->prepare($sql);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt = 1;
                  if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                      <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo htmlentities($result->allowip); ?></td>
                        <td>
                          <a href="manage-ip.php?del=<?php echo htmlentities($result->id); ?>" class="btn btn-danger">Delete</a>
                        </td>
                      </tr>
                    <?php $cnt++; } } ?>
                </tbody>
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
    <!-- Data table plugin-->
    <script type="text/javascript" src="../js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
  </body>
</html>
