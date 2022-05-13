<?php
// Include config file
require_once "config.php";
// Initialize the session
session_start();
$balance = "";
$balance_err = "";

// Check if the user is logged in, if not then redirect him to login page
//Basic authentication
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$profile = "public";

$getUserProfile = "SELECT * FROM users WHERE profile_type = '$profile'; ";

$addProfile = "ALTER TABLE  users ADD profile_type VARCHAR(10) DEFAULT public";
$result = mysqli_query($link, $getUserProfile);
if(empty($result)){

mysqli_query($link, $addProfile);
nysqli_close($link);
}


$username = $_SESSION["username"];
$getUser = "SELECT * FROM users WHERE username = '$username';";
$user = null;
if ($result = mysqli_query($link, $getUser)) {
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

    } else {
        echo "No records matching your query were found.";
    }
} else {
    echo mysqli_error($link);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = $_POST['firstName'];
    $lastName = $_POST['firstName'];
    $profile_type = $_POST['type'];
    $email = $_POST['email'];
    if (!(empty($firstName) && empty($lastName) && empty($email) && empty($profile_type))) {

        // Prepare an insert statement
        $sql = "UPDATE users SET firstName = ?, lastName = ?, email = ?, profile_type = ? WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, 'sssss', $firstName, $lastName, $email, $profile_type, $username);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Update Successful";
                header("Refresh:1");
            }else {
                $fail = "Oops! Something went wrong. Please try again later.";
            }
        }
    }
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
<?php
if (!empty($success)) {
    echo '<div class="alert alert-success container">' . $success . '</div>';
}
else if (!empty($fail)) {
    echo '<div class="alert alert-danger container">' . $fail . '</div>';
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
    <!-- Display user profile Basically fetch user info from database -->
    <h4>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h4>
    <h4>Your profile is : <b><?php echo htmlspecialchars($user['profile_type']); ?></b></h4>
    <!-- Check if profile type, if public don't show email -->
    <?php
    if ($user['profile_type'] == "private") {
        echo "<h4>Your email is : <b>" . $user['email'] . "</b></h4>";
    }
    ?>

    <h5 class="mt-3"><u>Update your profile</u></h5>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="firstName"
                   class="form-control"
                   value="<?php echo $user['firstname']; ?>">
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lastName"
                   class="form-control"
                   value="<?php echo $user['lastname'] ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   class="form-control"
                   value="<?php echo $user['email']; ?>">
        </div>

        <select class="form-control" name="type" id="type" >
            <option value="public" <?php if($user['profile_type'] == 'public') echo 'selected'; ?>>public</option>
            <option value="private" <?php if($user['profile_type'] == 'private') echo 'selected'; ?>>private</option>
        </select>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Update Profile">
        </div>

    </form>

    <div class="d-flex justify-content-center">
        <a href="deleteAccount.php" class="btn btn-danger btn-lg">Delete Your Profile</a>
    </div>

</div>

</body>

</html>