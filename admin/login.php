<?php
require_once "../includes/config.php";
require_once "../includes/functions.php";
require_once "../includes/db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check admin credentials
    $query = "SELECT * FROM admins WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        // Set session variables for admin
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['user_type'] = 'admin'; // Set user type as admin
        header("Location: dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex justify-center items-center h-screen">
        <form method="POST" class="bg-white p-8 rounded shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Admin Login</h2>
            <?php if (isset($error_message)): ?>
                <p class="text-red-500 text-sm mb-4"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" class="w-full px-4 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded" required>
            </div>
            <button type="submit" name="login" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
        </form>
    </div>
</body>
</html>