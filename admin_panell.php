<?php
session_start();

// Eğer giriş yapılmamışsa admin paneline yönlendir
if (!isset($_SESSION['user_id']) && !isset($_GET['user_id'])) {
    header("Location: loginn.php");
    exit();
}

// URL parametrelerinden user_id'yi alıyoruz
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];

// Veritabanından kullanıcıyı alıyoruz
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

// Kullanıcıyı veritabanından alalım
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

echo "Hoş geldiniz, " . $user['username'] . "!<br>";
?>

<a href="logutt.php">Çıkış Yap</a>
