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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Veritabanına kullanıcıyı ekleyelim
    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $user, 'password' => $pass]);

    echo "Kayıt başarılı!";
}
?>

<form method="post">
    Kullanıcı Adı: <input type="text" name="username" required><br>
    Şifre: <input type="password" name="password" required><br>
    <button type="submit">Kayıt Ol</button>
</form>
