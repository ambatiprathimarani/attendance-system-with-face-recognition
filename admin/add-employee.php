<?php 
error_reporting(0);
include 'include/config.php';

if (isset($_POST['Submit'])) {
    $empid = $_POST['empcode'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $Department = $_POST['Department'];
    $email = $_POST['email'];
    $mobNumber = $_POST['mobNumber'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $dob = $_POST['dob'];
    $dateofjoining = $_POST['dateofjoining'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $errormsg = '';
    $msg = '';

    // Validate required fields
    if (empty($fname)) {
        $errormsg = "Please Enter First Name";
    } elseif (empty($lname)) {
        $errormsg = "Please Enter Last Name";
    } elseif ($Department == 'NA') {
        $errormsg = "Please select a Department";
    } elseif (empty($email)) {
        $errormsg = "Please Enter Email";
    } elseif (empty($password)) {
        $errormsg = "Please Enter Password";
    } elseif (empty($confirmpassword)) {
        $errormsg = "Please Confirm Password";
    } elseif ($password != $confirmpassword) {
        $errormsg = "Password and Confirm Password do not match";
    } else {
        // Check if Employee ID or Email already exists
        $checkEmp = $dbh->prepare("SELECT * FROM tblemployee WHERE EmpId=:empid OR email=:email");
        $checkEmp->bindParam(':empid', $empid, PDO::PARAM_STR);
        $checkEmp->bindParam(':email', $email, PDO::PARAM_STR);
        $checkEmp->execute();
        $existingRecord = $checkEmp->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            $errormsg = "Employee ID or Email already exists!";
        } else {
            // Handle photograph
            if (empty($_FILES["photograph"]["name"])) {
                $path = "../uploads/default.jpeg";
            } else {
                $fileName = time() . "-" . $_FILES["photograph"]["name"];
                $tmpName = $_FILES["photograph"]["tmp_name"];
                $path = "../uploads/" . $fileName;
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $allowed = array("jpg", "png", "jpeg");

                if (in_array($fileExt, $allowed)) {
                    move_uploaded_file($tmpName, $path);
                } else {
                    $errormsg = "Only image files (jpg, png) are allowed.";
                }
            }

            if (empty($errormsg)) {
                $hashedPassword = md5($password);

                $sql = "INSERT INTO tblemployee (EmpId, fname, lname, department_name, email, mobile, country, state, city, address, photo, dob, date_of_joining, password) 
                        VALUES (:empid, :fname, :lname, :Department, :email, :mobNumber, :country, :state, :city, :address, :Photo, :dob, :dateofjoining, :password)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                $query->bindParam(':fname', $fname, PDO::PARAM_STR);
                $query->bindParam(':lname', $lname, PDO::PARAM_STR);
                $query->bindParam(':Department', $Department, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':mobNumber', $mobNumber, PDO::PARAM_STR);
                $query->bindParam(':country', $country, PDO::PARAM_STR);
                $query->bindParam(':state', $state, PDO::PARAM_STR);
                $query->bindParam(':city', $city, PDO::PARAM_STR);
                $query->bindParam(':address', $address, PDO::PARAM_STR);
                $query->bindParam(':Photo', $path, PDO::PARAM_STR);
                $query->bindParam(':dob', $dob, PDO::PARAM_STR);
                $query->bindParam(':dateofjoining', $dateofjoining, PDO::PARAM_STR);
                $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

                $query->execute();
                $lastInsertId = $dbh->lastInsertId();
                if ($lastInsertId > 0) {
                    $msg = "Information Added Successfully";
                } else {
                    $errormsg = "Data not inserted successfully";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="../css/main.css">
        <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h2 align="center">Add Employee</h2>
                    <hr />
                    <?php if ($msg) { ?>
                        <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
                    <?php } ?>
                    <?php if ($errormsg) { ?>
                        <div class="alert alert-danger"><?php echo htmlentities($errormsg); ?></div>
                    <?php } ?>
                    <form method="post" enctype="multipart/form-data" class="row">
                        <div class="form-group col-md-6">
                            <label>Employee ID</label>
                            <input type="text" class="form-control" name="empcode" value="<?php echo htmlentities($empid); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="fname" value="<?php echo htmlentities($fname); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="lname" value="<?php echo htmlentities($lname); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Department</label>
                            <select name="Department" class="form-control" required>
                                <option value="NA">-- Select --</option>
                                <?php
                                $stmt = $dbh->prepare("SELECT * FROM tbldepartment ORDER BY DepartmentName");
                                $stmt->execute();
                                $departList = $stmt->fetchAll();
                                foreach ($departList as $departname) {
                                    $selected = ($Department == $departname['id']) ? 'selected' : '';
                                    echo "<option value='".$departname['id']."' $selected>".$departname['DepartmentName']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlentities($email); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mobile Number</label>
                            <input type="text" class="form-control" name="mobNumber" value="<?php echo htmlentities($mobNumber); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Country</label>
                            <input type="text" class="form-control" name="country" value="<?php echo htmlentities($country); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>State</label>
                            <input type="text" class="form-control" name="state" value="<?php echo htmlentities($state); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" value="<?php echo htmlentities($city); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" name="dob" value="<?php echo htmlentities($dob); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Date of Joining</label>
                            <input type="date" class="form-control" name="dateofjoining" value="<?php echo htmlentities($dateofjoining); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Photo</label>
                            <input type="file" class="form-control" name="photograph">
                        </div>
                        <div class="form-group col-md-12">
                            <label>Address</label>
                            <textarea class="form-control" name="address"><?php echo htmlentities($address); ?></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="confirmpassword" required>
                        </div>
                        <div class="form-group col-md-12">
                            <button type="submit" name="Submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
