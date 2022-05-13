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


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Transactions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                    <a class="nav-link active" aria-current="page" href="welcome.php">Create Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="myAccount.php">MyAccount</a>
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
<div class="container">
    <h4>My Recent Tansactions</h4>

    <?php
    // Attempt select query execution
    $sql = "SELECT account_id FROM accounts where user_id = $id";
    if ($_result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($_result) > 0) {
            echo "<table class='table'>";
            echo "<tr>";
            echo "<th>Transaction id</th>";
            echo "<th>AccountSrc</th>";
            echo "<th>AccountDest</th>";
            echo "<th>Transaction type</th>";
            echo "<th>Memo</th>";
            echo "<th>BalanceChange</th>";
            echo "<th>Dated</th>";
            echo "</tr>";

            while ($row = mysqli_fetch_array($_result)) {
                //Get account id
                $accountId = $row['account_id'];
                $transactions_query = "SELECT * FROM transactions where accountSrc = $accountId ";
                if ($result = mysqli_query($link, $transactions_query)) {
                    if (mysqli_num_rows($result) > 0) {

                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['transaction_id'] . "</td>";
                            echo "<td>" . $row['accountSrc'] . "</td>";
                            echo "<td>" . $row['accountDest'] . "</td>";
                            echo "<td>" . $row['transactionType'] . "</td>";
                            echo "<td>" . $row['memo'] . "</td>";
                            echo "<td>" . $row['balanceChange'] . "</td>";
                            echo "<td>" . explode(" ", $row['created'])[0] . "</td>";

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


            }

            // Free result set
            mysqli_free_result($_result);
        } else {
            echo "No records matching your query were found.";
        }
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }

    // Close connection
    mysqli_close($link);
    ?>
</div>
</body>
</html>