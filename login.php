<?php
session_start();
if (isset($_SESSION['auth']) || $_SESSION['auth'] == true) {
  header("Location: /home.php");
  exit();
}

$conn = new mysqli('localhost', 'authchecker', '5+*X-.Ka5/&HL&t{', 'USERS');

// Check connection
if($conn->connect_error){
  die("ERROR: Could not connect. " . mysqli_connect_error());
}

$error = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $error = "&nbsp;&nbsp;Enter your username";
    } else {
        $username = trim($_POST["username"]);

        $sql = "SELECT password, isAdmin FROM users WHERE username='" . $username ."'";
        $result = mysqli_query($conn, $sql);
        $res = mysqli_fetch_assoc($result);
        $conn -> close();

        if ($result->num_rows == 0 || hash('sha256', $_POST["password"]) != $res["password"]) {
            $error = "&nbsp;&nbsp;Username or password incorrect";
        } else {
            $_SESSION["username"] = $username;
            $_SESSION["auth"] = true;
            
            if ($res["isAdmin"]) {
              $_SESSION["admin"] = true;
            } else {
              $_SESSION["admin"] = false;
            }

            header("Location: /home.php");
            exit();
        }
    }
}

?>

<html>
<head>
<title> Reporting Dashboard </title>
<link rel="shortcut icon" type="image/png" href="https://cdn.iconscout.com/icon/free/png-256/red-among-us-3218512-2691060.png"/>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1> Reporting Dashboard </h1>
<div class="smallbox">
<h2> &nbsp;Please login here </h2>
<p class="err" style="color:crimson;"><?php echo $error; ?></p>
<form action="/login.php" method="POST">
<label>&nbsp;&nbsp;Username: 
  <input type="text" name="username" value="<?php echo $username; ?>">
</label><br>
<label>&nbsp;&nbsp;Password:&nbsp;
  <input type="password" name="password">
</label><br><br>
&nbsp;
<input type="submit" value="Log In">
</form>
</div>
</body>
</html>
