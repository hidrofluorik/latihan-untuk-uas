<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    // Hashing password wajib biar aman
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Gagal registrasi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h2>Register</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <label>Role (Buat testing):</label><br>
        <select name="role">
            <option value="student">Student</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <button type="submit">Daftar</button>
    </form>
    <a href="login.php">Sudah punya akun? Login</a>
</body>
</html>