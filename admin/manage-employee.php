<?php session_start();
//error_reporting(0);
require_once('include/config.php');
if(strlen( $_SESSION["adminid"])==0){   
header('location:logout.php');
} else {
?><!DOCTYPE html>
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
                    <h2 align="center"> Manage Employee</h2>
              <hr />
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>Emp Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Country</th>
                    <th>Department</th>
                    <th>Action</th>
                  </tr>
                </thead>
              
                   <?php
                  $sql="SELECT tblemployee.id,EmpId, fname, lname, department_name, email, mobile, country, state, city, address, photo, 
dob, date_of_joining, create_date,tbldepartment.DepartmentName FROM tblemployee
left join tbldepartment on tblemployee.department_name=tbldepartment.id";
                  $query= $dbh->prepare($sql);
                 // $query->bindParam(':st',$st, PDO::PARAM_STR);
                  $query-> execute();
                  $results = $query -> fetchAll(PDO::FETCH_OBJ);
                  $cnt=1;
                  if($query -> rowCount() > 0)
                  {
                  foreach($results as $result)
                  {
                  ?>

            
                  <tr>
                    <td><?php echo($cnt);?></td>
                    <td><?php echo htmlentities($result->EmpId);?></td>
                    <td><?php echo htmlentities($result->fname);?> <?php echo htmlentities($result->lname);?></td>
                  <td><?php echo htmlentities($result->email);?></td>
                  <td><?php echo htmlentities($result->mobile);?></td>
                  <td><?php echo htmlentities($result->country);?></td>
                  <td><?php echo htmlentities($result->DepartmentName);?></td>
                 <?php  $id=$result->id;?>
                  <td>  
    <a href="emp-details.php?empid=<?php echo htmlentities($result->id);?>" class="btn btn-info">view</a>
    <a href="edit-employee.php?empid=<?php echo htmlentities($result->id);?>" class="btn btn-success">Edit</a>
    <a href="currentmonth-attendance.php?empid=<?php echo htmlentities($result->id);?>&&empname=<?php echo htmlentities($result->fname);?>"  class="btn btn-warning">Current month Attendance</a>



                   </td>
                  </tr>
                   
                    <?php  $cnt=$cnt+1; } } ?>
         
                         </table>

               

     <!--    // end modal popup code........ -->
              
     
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