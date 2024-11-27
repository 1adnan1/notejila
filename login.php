<?php 
session_start();
include_once("root/config.php");


$emailErr = $passErr = "";
$email = $password = "";
$call_login = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required.";
    } else {
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Password is required.";
    } else {
        $password = trim($_POST["password"]);
    }

 
    if (empty($emailErr) && empty($passErr)) {
      
        $query = "SELECT * FROM `customer` WHERE `c_email` = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

          
            if (password_verify($password, $user['c_pass'])) {
                $_SESSION['user_namee'] = $user['c_name'];
                header("Location: http://localhost/notesjila/"); // Redirect to Home Page
                exit();
            } else {
                $call_login = "Incorrect password. Please try again.";
            }
        } else {
            $call_login = "No account found with this email.";
        }

        $stmt->close();
    }
}

$users = mysqli_query($conn, "SELECT * FROM `customer`");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>User Registration</title>
</head>
<body id="logpage">
<style>
    #logpage {
      
        background-image: url('bg.jpg');
       
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Login</h2>
            <div class="registration-form">
                <p class="text-success text-center"><?php echo $call_login; ?></p>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <!-- Email -->
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="text" class="form-control" placeholder="Enter Email" name="email">
                        <p class="err-msg text-danger">
                            <?php if (!empty($emailErr)) { echo $emailErr; } ?>
                        </p>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" class="form-control" placeholder="Enter Password" name="password">
                        <p class="err-msg text-danger">
                            <?php if (!empty($passErr)) { echo $passErr; } ?>
                        </p>
                    </div>

                    <button type="submit" class="btn btn-secondary" name="login">Login</button>
                </form>
                <p class="text-center mt-3">Dont have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</div>
</div>
</div>
</body>
</html>

