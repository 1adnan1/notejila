<?php
session_start();
include_once("root/config.php");



// Initialize variables
$nameErr = $emailErr = $passErr = $confirmPassErr = "";
$name = $email = $password = $confirm_password = "";
$registration_msg = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required.";
    } else {
        $name = trim($_POST["name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed.";
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required.";
    } else {
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
        } else {
            // Check if email already exists
            $query = "SELECT * FROM `customer` WHERE `c_email` = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $emailErr = "This email is already registered.";
            }
            $stmt->close();
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passErr = "Password is required.";
    } else {
        $password = trim($_POST["password"]);
        if (strlen($password) < 6) {
            $passErr = "Password must be at least 6 characters.";
        }
    }

    // Validate confirm password
    if (empty($_POST["confirm_password"])) {
        $confirmPassErr = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirmPassErr = "Passwords do not match.";
        }
    }

    // If no errors, insert user into the database
    if (empty($nameErr) && empty($emailErr) && empty($passErr) && empty($confirmPassErr)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO `customer` (`c_name`, `c_email`, `c_pass`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $registration_msg = "Registration successful! You can now <a href='login.php'>log in</a>.";
            $name = $email = $password = $confirm_password = ""; // Clear inputs
        } else {
            $registration_msg = "An error occurred. Please try again later.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>User Registration</title>
</head>
<body id="regpage">
<style>
    #regpage {
      
        background-image: url('bg.jpg');
       
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Register</h2>
            <p class="text-success text-center"><?php echo $registration_msg; ?></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- Name -->
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" class="form-control" name="name"  placeholder="Enter your name">
                    <p class="text-danger"><?php echo $nameErr; ?></p>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email:</label>
                    <input type="text" class="form-control" name="email" placeholder="Enter your email">
                    <p class="text-danger"><?php echo $emailErr; ?></p>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter your password">
                    <p class="text-danger"><?php echo $passErr; ?></p>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm your password">
                    <p class="text-danger"><?php echo $confirmPassErr; ?></p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>
</body>
</html>
