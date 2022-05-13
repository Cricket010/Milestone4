<?php

include  "config.php";





$firstLastName = $username = $email =$first=$last= $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";


$sql  = "CREATE TABLE IF NOT EXISTS users(
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        firstName VARCHAR(255),
        lastName VARCHAR(255),
        username VARCHAR(255) ,
        email VARCHAR(255),
        password VARCHAR(60) NOT NULL,
        created TIMESTAMP NOT NULL DEFAULT  CURRENT_TIMESTAMP,
        modified TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
)";

//Initial db setUp
mysqli_query($link ,$sql);
   


if(isset($_POST['submit'])){


    echo $_POST['firstLastName'];
    echo $_POST['username'];
    echo $_POST['email'];
    echo $_POST['password'];
    echo $_POST['confirm_password'];
$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email";
    }else{
        
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    
        // $email = mysqli_real_escape_string($link , trim($_POST["email"]));
    }

if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    }else{
        
    $username = trim($_POST["username"]);

    }


    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = mysqli_real_escape_string($link ,trim($_POST["password"]));

        // echo "param_password = " .$param_password;
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = mysqli_real_escape_string($link , trim($_POST["confirm_password"]));
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }



    // Check input errors before inserting in database
    $name = explode(" ", $_POST['firstLastName']);
    $first = $name[0];
    $last = $name[1];


    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        echo "empty";

        $password = md5($password);
        // $param_password = password_hash($password, PASSWORD_DEFAULT)
$sql = " INSERT INTO  users (firstName , lastName , username , email , password ) VALUES ('$first' , '$last' , '$username' ,'$email', '$password')"; 

echo "name = " . $first ."last = " .$last ."username = " . $username . "email = " .$email;
echo "name = " . $first ;

if(mysqli_query($link, $sql)){
    echo "Records added successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 
// close connection
mysqli_close($link);

    }



     








}







?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form action="reg.php" method="POST">
        <div class="form-group">
            <label>First & Last Name</label>
            <input type="text" name="firstLastName"
                   class="form-control"
                   >
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username"
                   class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                   >
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                   >
            <span class="invalid-feedback"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password"
                   class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                   >
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password"
                   class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                   >
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary"  name="submit">
            <input type="reset" class="btn btn-secondary ml-2" value="Reset">
        </div>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>

</html>