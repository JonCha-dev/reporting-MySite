<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] != true) {
  header("Location: /login.php");
  exit();
} else if (!isset($_SESSION['admin']) || $_SESSION['admin'] != true) {
    header("Location: /home.php");
    exit();
}
?>

<?php
$conn = new mysqli('localhost', 'crudacct', 'HE&gf33DrN62wT2w', 'USERS');

// Check connection
if($conn->connect_error){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 
// Define variables and initialize with empty values
$username = $password = $isAdmin = $isAdminInput = "";
$username_err = $password_err = $isAdmin_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_name = trim($_POST["username"]);
    if(empty($input_name)){
        $username_err = "Please enter a username.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9_\s]+$/")))){
        $username_err = "Please enter a valid username.";
    } else{
        $username = $input_name;
    }
    
    // Validate pw
    $input_pass = trim($_POST["password"]);
    if(empty($input_pass)){
        $password_err = "Please enter a password.";     
    } else{
        $password = $input_pass;
    }
    
    // Validate perms
    $input_perms = trim($_POST["isAdmin"]);
    if(empty($input_perms)){
        $isAdmin_err = "Please enter the user's perms.";     
    } elseif($input_perms != "true" && $input_perms != "false"){
        $isAdmin_err = "Please enter either true (admin user) or false (normal user).";
    } else{
        $isAdminInput = $input_perms;
        $isAdmin = ($input_perms == "true") ? 1 : 0;
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($isAdmin_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET username=?, password=?, isAdmin=? WHERE id=?";
 
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssdi", $param_username, $param_password, $param_isAdmin, $param_id);
            
            // Set parameters
            $param_username = $username;
            $param_password = hash('sha256', $password);
            $param_isAdmin = $isAdmin;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: users.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM users WHERE id = ?";
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["username"];
                    $address = $row["password"];
                    $salary = $row["isAdmin"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        $stmt->close();
        
        // Close connection
        $conn->close();
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!--template from https://www.tutorialrepublic.com/php-tutorial/php-mysql-crud-application.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the user record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <span class="invalid-feedback"><?php echo $username_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                            <span class="invalid-feedback"><?php echo $password_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>isAdmin</label>
                            <input type="text" name="isAdmin" class="form-control <?php echo (!empty($isAdmin_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $isAdminInput; ?>">
                            <span class="invalid-feedback"><?php echo $isAdmin_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="users.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>