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

$id = $_SESSION['id'];

// Attempt select query execution
$sql = "SELECT * FROM accounts where user_id = $id AND active = true";
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

// Attempt select query execution
$world = "world";
$worldaccount = "SELECT account_id FROM accounts where account_type = $world";
if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result) > 0) {

        $worldaccount = mysqli_num_rows($result);
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


    $accountSrc = intval(filter_input(INPUT_POST, 'accountSrc', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $accountDest = $accountSrc;
    $amount = trim($_POST["amount"]);
    if ($amount == "") {
        $amount = 0;
    } else {
        $amount = intval(trim($_POST["amount"]));
    }
    $memo = trim($_POST["memo"]);
    $getBalance = "SELECT balance FROM accounts WHERE account_id = '$accountSrc'";
    if ($result = mysqli_query($link, $getBalance)) {
        $accountSrcBalance = intval(mysqli_fetch_array($result)[0]) - $amount;
        if ($accountSrcBalance <= 0) {
            $accountBalance_err = "Transaction Failed: You don't have enough money!";
        } else {

            $transfer_err = $accountSrc;
            $transactionType = "withdraw";
            $transaction1 = mysqli_prepare($link, "INSERT INTO transactions (accountSrc, accountDest, balanceChange, transactionType, memo, expectedTotal)
                VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($transaction1, "iiissi", $accountSrc, $accountDest, $amount, $transactionType, $memo, $accountSrcBalance);
            $update1 = "UPDATE accounts SET balance = $accountSrcBalance WHERE account_id = $accountSrc";
            if (mysqli_stmt_execute($transaction1)) {
                mysqli_query($link, $update1);
                $transfer_success = "Transaction created successfully.";
            } else {
                $transfer_err = "ERROR: Could not able to execute ->" . mysqli_error($link);
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
<?php
if (!empty($transfer_success)) {
    echo '<div class="alert alert-sucess container">acc' . $transfer_success . '</div>';
}
else if (!empty($transfer_err)) {
    echo '<div class="alert alert-danger container">tra' . $transfer_err . '</div>';
}
else if (!empty($accountBalance_err)) {
    echo '<div class="alert alert-danger container">' . $accountBalance_err . '</div>';
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
                    <a class="nav-link active" aria-current="page" href="myAccount.php">MyAccount</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="deposit.php">Deposit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Withdraw</a>
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

<div class="container mx-5">
    <h2>Withdraw funds</h2>
    <p>Please fill in your credentials to withdraw funds.</p>


    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="form-group">
            <label>Account</label>
            <select class="form-control" name="accountSrc" id="accountSrc">
                <?php
                $i = 0;
                while ($i < count($accountNumbers)) {
                    echo "<option value='" . $accountNumbers[$i] . "'>" . $accountNumbers[$i] . "</option>";  // displaying data in option menu
                    $i++;
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input type="text" name="amount" class="form-control ">
        </div>
        <div class="form-group">
            <label>Memo</label>
            <input type="text" name="memo" class="form-control ">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Withdraw funds">
        </div>
    </form>
</div>
</body>

</html>