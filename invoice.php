<?php
session_start();
require_once 'configg.php'; // Veritabanı bağlantısı

// Veritabanı bağlantısının düzgün çalıştığını kontrol et
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: loginn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının geçmişteki alımlarını (faturasını) almak için tek sorgu kullanıyoruz
$sql = "SELECT b.name AS book_name, p.total_price
        FROM purchases p
        JOIN books b ON p.book_id = b.id
        WHERE p.user_id = ?";

// Sorguyu hazırlama
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Sorgu hatası: " . $conn->error);  // Hata mesajını ekle
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Eğer kullanıcının herhangi bir alışverişi varsa, kitapları listele
if ($result->num_rows > 0) {
    $purchases = [];
    while ($row = $result->fetch_assoc()) {
        // Alışverişi ekle
        $purchases[] = [
            'book_name' => $row['book_name'],
            'total_price' => $row['total_price']
        ];
    }
} else {
    $message = "Henüz alışveriş yapmadınız.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura - Alışveriş</title>
</head>
<body>
    <h1>Alınan Kitaplar</h1>
    
    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Kitap Adı</th>
                <th>Toplam Fiyat</th>
            </tr>
            <?php foreach ($purchases as $purchase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($purchase['book_name']); ?></td>
                    <td><?php echo htmlspecialchars($purchase['total_price']); ?> puan</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="shopping.php">Alışveriş Sayfasına Geri Dön</a>
</body>
</html>
