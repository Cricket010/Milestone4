<?php


// Include config file
require_once "config.php";
// Initialize the session
session_start();

// // Check if the user is already logged in, if not then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === false) {
    header("location: login.php");
    exit;
}



$sql0 = "SELECT * FROM accounts where frozen = true";
$sql1 = "ALTER TABLE accounts ADD frozen BOOLEAN  DEFAULT false";
 $results = mysqli_query($link , $sql0);


 if(empty($results)){
       mysqli_query($link  , $sql1);
       mysqli_close($link);
}



$id = $_SESSION['id'];

// Attempt select query execution

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {



    // $accountDest = filter_input(INPUT_POST, 'accountDest', FILTER_SANITIZE_STRING);
    $account_num = $_POST['account_number'];

    $userAccount = "SELECT * FROM accounts  WHERE account_number = '$account_num' ;";

    $results  = mysqli_query($link , $userAccount);
    if(!empty($results)){

    $action  = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

  

            if($action === "deactivate"){


$deactivatequery = "UPDATE accounts SET active=false WHERE account_id= '$account_num'";
    if (mysqli_query($link, $deactivatequery)) {
        $delete_success = "Account deactivated successfully.";

    } else {
        $delete_err = "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }





            }else{

    //Post to db
    $sql = "UPDATE accounts SET frozen = true WHERE account_id= '$account_num'";
    if (mysqli_query($link, $sql)) {
        $delete_success = "Account frozen successfully.";

    } else {
        $delete_err = "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
    // Close connection
    mysqli_close($link);
            }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transfer</title>
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

<!-- Add bootstrap navbar -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
    <!-- Container wrapper -->
    <div class="container-fluid">
        <!-- Toggle button -->
        <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarCenteredExample"
                aria-controls="navbarCenteredExample" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Collapsible wrapper -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarCenteredExample">
            <!-- Left links -->
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="welcome.php">Add Account</a>
                    <a class="nav-link active" aria-current="page" href="deposit.php">Deposit Money</a>

                </li>
                <li class="nav-item mx-5" >
                    <a class="nav-link active" aria-current="page" href="freezeAccount.php">Freeze Account</a>
                    <a class="nav-link active" aria-current="page" href="withdraw.php">Withdraw Money</a>

                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Grant loans</a>
                    <a class="nav-link active" aria-current="page" href="view.php">View Loans</a>

                </li>
                
                <li class="nav-item mx-5">
                    <a class="nav-link active" aria-current="page" href="transfer.php">Transfer</a>
                    <a class="nav-link active" aria-current="page" href="myAccount.php">View Accounts</a>

                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
                </li>
            </ul>
            <!-- Left links -->
        </div>
        <!-- Collapsible wrapper -->


        
    </div>
    <!-- Container wrapper -->
</nav>
<?php
if (!empty($delete_err)) {
    echo '<div class="alert alert-danger container">' . $delete_err . '</div>';
}
if (!empty($delete_success)) {
    echo '<div class="alert alert-success container">' . $delete_success . '</div>';
}
?>
<div class="Wrapper mx-5">
<form  name = "submit" value="submit" action="freezeAccount.php" method ='post'>
<!-- <div class  ="input-group mb-3"> -->
<div class="form-group">

<label>Enter Account number </label>
<input type="text" class = "form-control my-sm-2" name="account_number" placeholder="enter  account to freeze" aria-label="Search">

</div>
<div class="form-group">
            <label>Select action</label>
            <select class="form-control" name="action" id="action">
                <option value="deactivate">deactivate</option>
                <option value="freeze">freeze</option>
            </select>
        </div>
        <div class="form-group">
<button class="btn btn-outline-danger my-sm-2 m-2" type="submit">  freeze/deactivate </button>
</div>
</form>
</div>

</body>

</html>