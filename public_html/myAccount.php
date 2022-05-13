<?php
// Include config file
require_once "config.php";

// Check if the user is logged in, if not then redirect him to login page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === false) {
    header("location: login.php");
    exit;
}
session_start();
$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $account = $_POST['delete'];
        $sql = "UPDATE accounts SET active = 0 WHERE account_id = (?) AND user_id = (?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $account, $id);
            if ((mysqli_stmt_execute($stmt))) {
                $delete_success = "Successfully Inactivated account $account";
            } else {
                $delete_err = "ERROR: Could not able to execute " . mysqli_error($link);
            }
        }
    }
    header("Refresh:0");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyAccount</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<?php
if (!empty($delete_err)) {
    echo '<div class="alert alert-danger container">' . $delete_err . '</div>';
}
if (!empty($delete_success)) {
    echo '<div class="alert alert-success container">' . $delete_success . '</div>';
}
?>
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
                    <a class="nav-link active" aria-current="page" href="welcome.php">Create Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="myAccount.php">My Accounts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="deposit.php">Deposit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="withdraw.php">Withdraw</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="transfer.php">Transfer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="transactionHistory.php">Transaction</a>
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
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <?php

    // Attempt select query execution
    $sql = "SELECT * FROM accounts where user_id = $id and active = true";
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table class='table'>";
            echo "<tr>";
            echo "<th scope='col'>id</th>";
            echo "<th scope='col'>Account number</th>";
            echo "<th scope='col'>Account type</th>";
            echo "<th scope='col'>Balance</th>";
            echo "</tr>";
            while ($row = mysqli_fetch_array($result)) {
                $account_id = $row['account_id'];
                echo "<tr>";
                echo "<td>" . $account_id . "</td>";
                echo "<td>" . $row['account_number'] . "</td>";
                echo "<td>" . $row['account_type'] . "</td>";
                echo "<td>" . $row['balance'] . "</td>";
                echo "<td><button type='submit' name='delete' value='$account_id' class='btn btn-danger ml-3'>";
                if ($row['active'] == 1) {
                    echo 'Delete';
                } else {
                    echo 'InActive';
                }
                "</button></td>";
                echo "</tr>";
            }
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
        } else {
            echo "No records matching your query were found.";
        }
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }

    // Close connection
    mysqli_close($link);
    ?>
</form>
</body>

</html>