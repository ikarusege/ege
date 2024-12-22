<?php
session_start();
require_once 'config.php'; // Veritabanı bağlantı dosyasını dahil et

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: loginn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının cüzdan bilgisini veritabanından alalım
$sql = "SELECT wallet FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($wallet);
$stmt->fetch();
$stmt->close();

// Eğer cüzdan bilgisi session'da yoksa, veritabanından alalım ve oturumda saklayalım
if (!isset($_SESSION['wallet'])) {
    $_SESSION['wallet'] = $wallet; // Cüzdan bilgisi oturumda saklanacak
}

// Rastgele puan kazanma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['earn_points'])) {
    $random_points = rand(1, 5);
    $_SESSION['wallet'] += $random_points; // Cüzdanı session'da güncelle

    // Veritabanına kaydedelim
    $sql = "UPDATE users SET wallet = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['wallet'], $user_id);
    $stmt->execute();
    $stmt->close();

    $message = "Tebrikler! $random_points puan kazandınız.";
}

// Çıkış işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Çıkış yapmadan önce cüzdan bilgilerini veritabanına kaydedelim
    $sql = "UPDATE users SET wallet = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['wallet'], $user_id);
    $stmt->execute();
    $stmt->close();

    // Oturumu kapat
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    
    // Çıkış sonrası cüzdan bilgisi session'da kalacak, kaybolmayacak
    $_SESSION['wallet'] = $_SESSION['wallet']; // Puanları kaybetmesin

    header("Location: loginn.php");
    exit();
}

// Diğer kullanıcıların cüzdan bilgilerini alalım
$sql = "SELECT id, username, wallet FROM users WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$other_users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Sayfası</title>
</head>
<body>
    <!-- Kullanıcı Cüzdan Bilgisi -->
    <div style="text-align: right;">
        <h2>Cüzdanınız: <?php echo $_SESSION['wallet']; ?> puan</h2>
    </div>

    <h1>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="earn_points">Puan Kazan</button>
    </form>

    <form method="post">
        <button type="submit" name="logout">Çıkış Yap</button>
    </form>

    <h3>Diğer Kullanıcıların Cüzdan Bilgisi:</h3>
    <ul>
        <?php foreach ($other_users as $user): ?>
            <li>
                <strong><?php echo htmlspecialchars($user['username']); ?></strong> - <?php echo $user['wallet']; ?> puan
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="get" action="shopping.php">
        <button type="submit">Alışverişe Başla</button>
    </form>
</body>
</html>
