<?php 
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// include 'config.php';
require_once "config.php";
// include 'functions.php';
 

 $username = $password = "";
$username_err = $password_err = $login_err = "";

error_reporting(0);
if (isset($_POST['submit'])){


 if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = mysqli_real_escape_string($link , trim($_POST["username"]));
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = mysqli_real_escape_string($link , trim($_POST["password"]));
    }
    if (empty($username_err) && empty($password_err)) {


        $password = md5($password);
        echo $password;

            $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

        // $sql = "SELECT id, username, password FROM users WHERE username  =  '$username' AND password = '$password' ";


        $result = mysqli_query($link,$sql);
    if($result->num_rows >0){
       $row = mysqli_fetch_assoc($result);

       if($username == "admin"){
           $_SESSION['username']= $row['username'];

                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row['id'];

                $_SESSION["username"] = $username;
      
           header("location: admin.php");
       }else{
       $_SESSION['username']= $row['username'];

                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $row['id'];

                $_SESSION["username"] = $username;
      
           header("location: welcome.php");
       }
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }

           }else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid --- username or password.";
                }
          
// Close connection
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>

    <?php
    if (!empty($login_err)) {
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }
    ?>

    <form action="log.php" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username"
                   class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                   >
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password"
                   class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" name="submit" value="Login">
        </div>
        <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
    </form>
</div>
</body>
</html>