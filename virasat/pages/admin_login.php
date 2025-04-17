<?php
session_start();
include '../db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT user_id, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                if ($role === 'admin') {
                    $_SESSION["user_id"] = $user_id;
                    $_SESSION["role"] = $role;
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $error = "You are not an admin.";
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Admin not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .login-box {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 18px;
            background: white;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            text-align: center;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 12px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background-color: #d62828;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
            font-size: 16px;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login as Admin</button>
        </form>
    </div>
</body>
</html>
