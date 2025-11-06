<?php 
session_start();
error_reporting(0);
require_once('include/config.php');
if(strlen( $_SESSION["Empid"])==0)
    {   
header('location:index.php');
}
else{
   $empid=$_SESSION['Empid'];
if(isset($_POST['Submit'])){


$fname=$_POST['fname'];
$lname=$_POST['lname'];
$country=$_POST['country'];
$state=$_POST['state'];
$city=$_POST['city'];
$dob=$_POST['dob'];
$address=$_POST['address'];
//$Photo=$_POST['Photo'];


if (empty($fname)) {
   $ferrormsg="Please Enter First Name";
}
elseif (empty($lname)) {
   $lerrormsg="Please Enter Last Name";
 }
 


else{
$dbimage=$_POST['cimage'];
$cimage=$_FILES["photograph"]["name"];
$fileboard_resolution=time()."-".$_FILES["photograph"]["name"];
$tmp_namefileboard_resolution=$_FILES["photograph"]["tmp_name"];
$filepath="../uploads/".$fileboard_resolution;

$fileboard_resolution1=explode(".",$fileboard_resolution);
$ext=$fileboard_resolution1[1];
$allowed=array("jpg","png","JPEG","jpeg","JPG","PNG");
if($cimage==''){
$path=$dbimage;
}else{
  $path=$filepath;
if(in_array($ext,$allowed))
{
move_uploaded_file($tmp_namefileboard_resolution,$path);
}

}

$sql="update tblemployee set fname=:fname,lname=:lname,country=:country,state=:state,city=:city,address=:address,dob=:dob,photo=:Photo where EmpId=:empid";

$query = $dbh -> prepare($sql);
$query->bindParam(':empid',$empid,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':lname',$lname,PDO::PARAM_STR);
$query->bindParam(':country',$country,PDO::PARAM_STR);
$query->bindParam(':state',$state,PDO::PARAM_STR);
$query->bindParam(':city',$city,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
$query->bindParam(':dob',$dob,PDO::PARAM_STR);
$query->bindParam(':Photo',$path,PDO::PARAM_STR);
$lastInsertId= $query->execute();
if($lastInsertId)
{
$msg= "Information Update Successfully";
 }

else {

$errormsg= "Information Update not Successfully";
 }
}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <title>Employee Management System</title>
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
            <h2 align="center">Update Employee</h2>
            <hr />
             <!---Success Message--->  
          <?php if($msg){ ?>
          <div class="alert alert-success" role="alert">
          <strong>Well done!</strong> <?php echo htmlentities($msg);?>
          </div>
          <?php } ?>

          <!---Error Message--->
          <?php if($errormsg){ ?>
          <div class="alert alert-danger" role="alert">
          <strong>Oh snap!</strong> <?php echo htmlentities($errormsg);?></div>
          <?php } ?>
            
            <div class="tile-body">
              <?php
                  
          $sql="SELECT tblemployee.id, EmpId,fname, lname, department_name, email, mobile, country, state, city, address, photo, 
          dob, date_of_joining, create_date,tbldepartment.DepartmentName as DeptName,tbldepartment.id as Deptid FROM tblemployee join tbldepartment 
          on tblemployee.department_name=tbldepartment.id
          where tblemployee.EmpId=:pid";

                $query= $dbh->prepare($sql);
                $query->bindParam(':pid',$empid, PDO::PARAM_STR);
                $query-> execute();
                $results = $query -> fetchAll(PDO::FETCH_OBJ);
                $cnt=1;
                if($query -> rowCount() > 0)
                {
                foreach($results as $result)
                {
                ?>
              <form class="row" method="post" enctype="multipart/form-data">
                  <div class="form-group col-md-12">
                  <label class="control-label">Emp ID</label>
                  <input class="form-control" name="EmpId" id="EmpId" type="text" placeholder="Enter your First Name" autocomplete="off" value="<?php echo $result->EmpId;?>" readonly>
                  <span style="color: red;"><?php echo $ferrormsg;?></span>
                </div>

                 <div class="form-group col-md-6">
                  <label class="control-label">First Name</label>
                  <input class="form-control" name="fname" id="fname" type="text" placeholder="Enter your First Name" autocomplete="off" value="<?php echo $result->fname;?>">
                  <span style="color: red;"><?php echo $ferrormsg;?></span>
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Last Name</label>
                  <input class="form-control" name="lname" id="lname" type="text" placeholder="Enter your Last Name" autocomplete="off" value="<?php echo $result->lname;?>">
                  <span style="color: red;"><?php echo $lerrormsg;?></span>
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Department</label>
                 <select name="Department" id="Department" class="form-control" onChange="getdistrict(this.value);" readonly >
                  <option value="<?php echo $result->Deptid;?>"><?php echo $result->DeptName;?></option>
           
                  </select>
                <span style="color:red;"><?php echo $deperrormsg;?></span>
                </div>
                

               

                 <div class="form-group col-md-6">
                  <label class="control-label">Email ID</label>
                  <input class="form-control" type="text" name="email" id="email" placeholder="Enter your Email" readonly  autocomplete="off" value="<?php echo $result->email;?>">
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Mobile No</label>
                  <input type="text" name="mobNumber" id="mobNumber" class="form-control" placeholder="Enter your Mobile No" maxlength="10" autocomplete="off" value="<?php echo $result->mobile;?>" readonly >
                </div>

                 <div class="form-group col-md-6">
                  <label class="control-label">Country</label>
                  <input type="text" name="country" placeholder="Enter your Country" id="country" class="form-control" value="<?php echo $result->country;?>">
                   
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">State</label>
                  <input type="text" name="state" id="state" placeholder="Enter your State" class="form-control" value="<?php echo $result->state;?>">
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">City</label>
                 <input type="text"  name="city" id="city" placeholder="Enter your City" class="form-control" value="<?php echo $result->city;?>">
                    
                </div>

                <div class="form-group col-md-6">
                  <label class="control-label">DOB</label>
                  <input type="date"  name="dob" id="dob" placeholder="Enter your Date Of Birth" class="form-control" autocomplete="off" value="<?php echo $result->dob;?>">
                   </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Date of Joining</label>
                  <input type="date" name="dateofjoining" id="dateofjoining" placeholder="Enter your Date of Joining" class="form-control" autocomplete="off" value="<?php echo $result->date_of_joining;?>" readonly >
                   </div>

                <div class="form-group col-md-6">
                  <label class="control-label">Photo</label>
                  <input type="hidden" name="cimage" value="<?php  echo $result->photo;?>">
                  <input type="file"  name="photograph" id="photograph" value="" class="form-control">
                   <?php if($result->photo != ""): ?>
                  <img src="<?php  echo $result->photo;?>"  width="100px" height="100px" style="border:1px solid #333333;">

<?php endif; ?>  
                   </div>


                              
                    <div class="form-group col-md-6">
                  <label class="control-label">Address</label>
                  <textarea name="address" id="address" placeholder="Enter your Address" class="form-control" autocomplete="offs"><?php echo $result->address;?></textarea> 
                   </div>

                <div class="form-group col-md-4 align-self-end">
                  <input type="Submit" name="Submit" id="Submit" class="btn btn-primary" value="Update">
                </div>
              </form>
             <?php  $cnt=$cnt+1; } } ?>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/plugins/pace.min.js"></script>
       -->
  </body>
</html>

<?php } ?>

<!-- Script -->
 <script>
function getdistrict(val) {
$.ajax({
type: "POST",
url: "ajaxfile.php",
data:'category='+val,
success: function(data){
$("#package").html(data);
}
});
}
</script>