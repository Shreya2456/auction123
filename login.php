<?php
// Set page title
$page_title = "Login - BidPulse";

// Include config file
require_once "includes/config.php";
require_once "includes/functions.php";

// Check if the user is already logged in
if(is_logged_in()){
    redirect("index.php");
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, is_admin FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $is_admin);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["is_admin"] = $is_admin;

                            // Redirect user to home page
                            redirect("index.php");
                        } else{
                            // Password is not valid
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist
                    $login_err = "Invalid username or password.";
                }
            } else{
                $_SESSION["error_message"] = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Include header
include "includes/header.php";
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden mt-4">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-center text-primary mb-6">Login to Your Account</h2>

        <?php
        if(!empty($login_err)){
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
            echo '<span class="block sm:inline">' . $login_err . '</span>';
            echo '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" name="username" id="username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($username_err)) ? 'border-red-500' : 'border-gray-300'; ?>" value="<?php echo $username; ?>">
                <span class="text-red-500 text-sm"><?php echo $username_err; ?></span>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary <?php echo (!empty($password_err)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <span class="text-red-500 text-sm"><?php echo $password_err; ?></span>
            </div>
            <div class="mb-4">
                <button type="submit" class="w-full btn-primary">Login</button>
            </div>
            <p class="text-center text-gray-600">Don't have an account? <a href="register.php" class="text-primary hover:underline">Sign up now</a>.</p>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
