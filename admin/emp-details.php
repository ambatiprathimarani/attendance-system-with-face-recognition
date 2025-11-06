<?php session_start();
error_reporting(0);
require_once('include/config.php');
include  'include/config.php';
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
   <title>Employee management System</title>
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
               <?php
                  $empid=$_GET['empid'];
                $sql="SELECT tblemployee.id,EmpID,
fname,lname,tbldepartment.DepartmentName,fname,lname,email,mobile,country,state,city,address,photo,dob,date_of_joining FROM tblemployee
left join tbldepartment on tblemployee.department_name=tbldepartment.id
where tblemployee.id=:empid";
                $query= $dbh->prepare($sql);
                $query->bindParam(':empid',$empid, PDO::PARAM_STR);
                $query-> execute();
                $results = $query -> fetchAll(PDO::FETCH_OBJ);
                $cnt=1;
                if($query -> rowCount() > 0)
                {
                foreach($results as $result)
                {
                ?>
              <table class="table table-hover table-bordered">
                <tbody>
<tr>
  <th colspan="4" style="font-size:20px; color:blue;"># <?php echo $result->EmpID;?> Details</th>
</tr>


<tr>
<th>EmpId</th> <td><?php echo $result->EmpID;?></td>
<th>Photo</th> <td><img src="<?php echo $result->photo;?>"width=150px; height="130px;"></td>
</tr>

<tr>
<th>First Name</th> <td><?php echo $result->fname;?></td>
<th>Last Name</th> <td><?php echo $result->lname;?></td>
</tr>
<tr>
<th>Department</th><td><?php echo $result->DepartmentName;?></td>
<th>Email</th><td><?php echo $result->email;?></td>
</tr>

<tr>
<th>Dob</th>
<td><?php echo $result->dob;?></td>
<th>Date Of Joining</th>
<td><?php echo $result->date_of_joining;?></td>
</tr>


<tr>
<th>Address</th>
<td><?php echo $result->address;?></td>
  <th>City</th>
<td><?php echo $result->city;?></td>
</tr>
<tr>

<th>State</th>
<td><?php echo $result->state;?></td>
<th>country</th>
<td><?php echo $result->country;?></td>
</tr>


<tr>
<th>Mobile</th> 
<td colspan="3"><?php echo $result->mobile;?></td>

</tr>

      <tr> <td colspan="4">  
             
                   <a href="edit-employee.php?empid=<?php echo htmlentities($result->id);?>" class="btn btn-success" target="_blank">Edit Details</a>
        <a href="currentmonth-attendance.php?empid=<?php echo htmlentities($result->id);?>&&empname=<?php echo htmlentities($result->fname);?>"  class="btn btn-warning" target="_blank">Current month Attendance</a>
 


                   </td>
                  </tr>
</tbody>
</table>


            </div>
<?php  $cnt=$cnt+1; } } ?>
    
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
  
  </body>
</html>
<?php } ?>