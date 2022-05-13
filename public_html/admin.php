<?php


// Include config file
require_once "config.php";
// Initialize the session
session_start();

$admin = $_SESSION['username'];


// // Check if the user is already logged in and is admin, if not then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === false  || $admin != "admin") {
    header("location: login.php");
    exit;
}

$id = $_SESSION['id'];

// Attempt select query execution
$sql = "SELECT * FROM accounts where user_id = $id";
if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        $accountNumbers = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($accountNumbers, $row['account_id']);
        }

        // Free result set
        mysqli_free_result($result);
    } else {
        echo "No records matching your query were found.";
    }
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {


   


    //Post to db
    $sql = "UPDATE accounts SET active=false WHERE account_id=$accountDest";
    if (mysqli_query($link, $sql)) {
        $delete_success = "Account deleted successfully.";

    } else {
        $delete_err = "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
    // Close connection
    mysqli_close($link);
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
                    <a class="nav-link active" aria-current="page" href="#">View Loans</a>

                </li>
                
                <li class="nav-item mx-5">
                    <a class="nav-link active" aria-current="page" href="transfer.php">Transfer</a>
                    <a class="nav-link active" aria-current="page" href="view.php">View Accounts</a>

                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="profile.php">Profile</a>
                <a class="nav-link active" aria-current="page" href="freezeAccount.php">freeze Account</a>

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



</body>

</html>