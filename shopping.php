<?php
session_start();
require_once 'configg.php'; // Veritabanı bağlantısı

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: loginn.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının cüzdan bilgisini session'da tutmak için çekiyoruz
if (!isset($_SESSION['wallet'])) {
    $sql = "SELECT wallet FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['wallet'] = $row['wallet'];
    } else {
        $_SESSION['wallet'] = 0; // Kullanıcı bulunamazsa cüzdan 0 olarak ayarla
    }
}

// Kitapları tanımlıyoruz (gerçek veritabanı yerine sabit kitaplar)
$books = [
    ['id' => 1, 'name' => 'Kitap 1', 'price' => 20],
    ['id' => 2, 'name' => 'Kitap 2', 'price' => 50]
];

// Kullanıcı bir kitap almak istediğinde yapılacak işlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcı bir kitap almak için form gönderdiğinde
    if (isset($_POST['buy_book'])) {
        $book_id = $_POST['book_id'];
        $quantity = $_POST['quantity']; // Satın alınacak kitap miktarı
        
        // Kitap fiyatını al
        foreach ($books as $book) {
            if ($book['id'] == $book_id) {
                $book_price = $book['price'];
                break;
            }
        }
        
        // Kullanıcıda yeterli puan varsa, cüzdanı güncelle
        $total_price = $book_price * $quantity;

        if ($_SESSION['wallet'] >= $total_price) {
            $_SESSION['wallet'] -= $total_price; // Cüzdanı güncelle

            // Satın alma bilgilerini session'a kaydet
            if (!isset($_SESSION['purchases'])) {
                $_SESSION['purchases'] = [];
            }
            $_SESSION['purchases'][] = ['book_id' => $book_id, 'quantity' => $quantity, 'total_price' => $total_price];

            // Veritabanında cüzdanı güncelle
            $sql_update = "UPDATE users SET wallet = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $_SESSION['wallet'], $user_id);
            $stmt_update->execute();

            $message = "$quantity x $book[name] başarıyla alındı! $total_price puan harcandı.";
        } else {
            $message = "Yeterli puanınız yok!";
        }
    }
    
    // Fatura görüntüleme işlemi
    if (isset($_POST['view_invoice'])) {
        header("Location: invoice.php"); // Fatura sayfasına yönlendirme
        exit();
    }
}

// Çıkış işlemi
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: loginn.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alışveriş Sayfası</title>
</head>
<body>
    <h1>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>Mevcut Puanınız: <?php echo $_SESSION['wallet']; ?> puan</h2>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Kitaplar</h2>
    <ul>
        <?php foreach ($books as $book): ?>
            <li>
                <?php echo htmlspecialchars($book['name']); ?> - <?php echo $book['price']; ?> puan
                <form method="post" style="display:inline;">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" style="width: 40px;">
                    <button type="submit" name="buy_book">Satın Al</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Fatura Görüntüle Butonu -->
    <form method="post">
        <button type="submit" name="view_invoice">Fatura Görüntüle</button>
    </form>

    <form method="post">
        <button type="submit" name="logout">Çıkış Yap</button>
    </form>
</body>
</html>
