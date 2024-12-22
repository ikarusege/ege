<?php
// Bağlantı ayarları
$host = "localhost";
$dbname = "users_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Admin kontrolü (Hardcoded değerlerle)
    if ($user === 'admin' && $pass === 'admin') {
        $_SESSION['username'] = 'admin';
        header("Location: admin_panell.php"); // Admin paneline yönlendirme
        exit();
    }

    // Diğer kullanıcılar için veritabanı kontrolü
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $user]);
    $result = $stmt->fetch();

    if ($result && password_verify($pass, $result['password'])) {
        // Kullanıcı oturumunu başlat
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];

        // Home sayfasına yönlendir (ID ile)
        header("Location: homee.php?user_id=" . $_SESSION['user_id']);
        exit();
    } else {
        echo "Geçersiz kullanıcı adı veya şifre!";
    }
}
?>

<form method="post">
    Kullanıcı Adı: <input type="text" name="username" required><br>
    Şifre: <input type="password" name="password" required><br>
    <button type="submit">Giriş Yap</button>
</form>
