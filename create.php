<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] != true) {
  header("Location: /login.php");
  exit();
} else if (!isset($_SESSION['admin']) || $_SESSION['admin'] != true) {
    header("Location: /home.php");
    exit();
}

$conn = new mysqli('localhost', 'crudacct', 'HE&gf33DrN62wT2w', 'USERS');

// Check connection
if($conn->connect_error){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Define variables and initialize with empty values
$username = $password = $isAdmin = $isAdminInput = "";
$username_err = $password_err = $isAdmin_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

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
    } elseif($input_perms != "true" && $input_perms != "false") {
        $isAdmin_err = "Please enter either true (admin user) or false (normal user).";
    } else{
        $isAdminInput = $input_perms;
        $isAdmin = ($input_perms == "true") ? 1 : 0;
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($isAdmin_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, isAdmin) VALUES (?, ?, ?)";
 
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssd", $param_username, $param_password, $param_isAdmin);
            
            // Set parameters
            $param_username = $username;
            $param_password = hash('sha256', $password);
            $param_isAdmin = $isAdmin;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: users.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // close statement
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
}
?>
 
<!--template from https://www.tutorialrepublic.com/php-tutorial/php-mysql-crud-application.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
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
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add user to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="users.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>