<?php
// Include config file
require_once "config.php";
// Initialize the session
session_start();
$amount = "";
$amount_err = "";

// Check if the user is logged in, if not then redirect him to login page
//Basic authentication
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

    
 $id = $_SESSION['id'];

    $querys = explode("\n", file_get_contents("initialSetup.sql"));
    foreach ($querys as $q) {
      $q = trim($q);
      if (strlen($q)) {
       mysqli_query($link, $q);
      }      
    }
   


//   ExecSqlFile("initialSetup.sql");
$sql0 = " CREATE TABLE IF NOT EXISTS Accounts(
    account_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    account_number INT(12) ZEROFILL NOT NULL UNIQUE,
    user_id INT NOT NULL,
    amount INT NOT NULL DEFAULT 0,
    account_type VARCHAR(10) NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";




$sql1 = "CREATE TABLE  IF NOT EXISTS Transactions (
    transaction_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    accountSrc INT NOT NULL,
    accountDest INT NOT NULL,
    balanceChange INT NOT NULL,
    transactionType VARCHAR(10) NOT NULL,
    memo TEXT NOT NULL,
    expectedTotal INT NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accountSrc) REFERENCES Accounts(account_id),
    FOREIGN KEY (accountDest) REFERENCES Accounts(account_id)
)";


   
    mysqli_query($link  , $sql0);

    mysqli_query($link  , $sql1);




$username = $_SESSION["username"];


$getUser = "SELECT * FROM users WHERE username = '$username';";
$user = null;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

   


    // Check if username is empty
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter  amount.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    echo "amount =  " .$amount;

    // Prepare an insert statement
    $sql = "INSERT INTO accounts (account_number, user_id, balance, account_type) VALUES (?, ?, ?, ?)";

     
    $stmt = mysqli_stmt_init($link);

      // $stmt = mysqli_prepare($link, $sql)
    if (mysqli_stmt_prepare($stmt ,$sql)) {
        // Bind variables to the prepared statement as parameters

        

        //Generate account number
        //Time will always be unique, add two digits infront of the 10 time() digits
        $param_acc_number = 110000 + time();
        $param_user_id = $id;
        echo "id = " .$param_user_id;
        $param_amount = $amount;
        $params_acc_type = "loan";

        mysqli_stmt_bind_param($stmt, "ssss", $param_acc_number, $param_user_id, $param_amount, $params_acc_type);

        // Set parameters


        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            //Make transaction



            // The id of the world account at this point is 2
            //Get id of the inserted account
            $last_id = mysqli_insert_id($link);

            $transaction1 = "INSERT INTO transactions (accountSrc, accountDest, balanceChange, transactionType, memo, expectedTotal)
                VALUES ($last_id, 1, $param_amount, 'loan', 'loan', $param_amount)";
            if (mysqli_query($link, $transaction1)) {

                echo "Transaction created successfully.";

                //Create the second part of the transaction
                $transaction2 = "INSERT INTO transactions (accountSrc, accountDest, balanceChange, transactionType, memo, expectedTotal)
                    VALUES (1, $last_id, $param_amount, 'loan', 'loan', $param_amount)";
                if (mysqli_query($link, $transaction2)) {
                    echo "Transaction created successfully.";
                    //If both are successful, redirect to myaccount page
                    header("location: myAccount.php");
                } else {
                    echo "ERROR: Could not able to execute " . mysqli_error($link);
                }
            } else {
                echo "ERROR: Could not able to execute" . mysqli_error($link);
            }


            echo "loan Apllication Successfull ";
        } else {
            echo "Oops! Something went wrong. Please try again later." . mysqli_error($link);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }else{
        echo "error" .mysqli_error($link);
    }


    // Close connection
    mysqli_close($link);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;

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
                    <a class="nav-link active" aria-current="page" href="apply.php">Apply for loan</a>
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
<!-- Display user profile Basically fetch user info from database -->
<div class="container">
    
    <h4 class="mt-3"><u>Apply for a loan</u></h4>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Loan Amount</label>
            <input type="text" name="amount"
                    placeholder="Minimum amount is $500"
                   class="form-control <?php echo (!empty($amount_err)) ? 'is-invalid' : ''; ?>"
                   value="<?php echo $amount; ?>">
            <span class="invalid-feedback"><?php echo $amount_err; ?></span>
        </div>
        
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="apply">
        </div>

    </form>
</div>

</body>

</html>
