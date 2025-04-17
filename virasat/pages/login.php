<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
                $_SESSION["user_id"] = $user_id;
                $_SESSION["role"] = $role;

                if ($role === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with this email.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Virasat</title>
    <link rel="stylesheet" href="../admin/styles.css"> <!-- if you have styles -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fefbf6;
            margin: 0;
            padding: 0;
        }
        header nav {
            background-color: #2d6a4f;
            padding: 15px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        .login-section {
            width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2d6a4f;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        button {
            margin-top: 25px;
            padding: 12px;
            background-color: #e63946;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
        }
        button:hover {
            background-color: #d62828;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 15px;
        }
        p {
            text-align: center;
        }
        footer {
            margin-top: 50px;
            text-align: center;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
        </nav>
    </header>

    <section class="login-section">
        <h1>Login</h1>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
<p style="font-size: 13px; margin-top: 8px; text-align: center; color: #000;">
    <a href="admin_login.php" style="color: #000; text-decoration: underline;">Are you an admin?</a>
</p>
    </section>

    <footer>
        <p>&copy; 2024 Virasat | Embrace Tradition, Celebrate Art</p>
    </footer>
</body>
</html>
