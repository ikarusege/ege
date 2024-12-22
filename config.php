<?php
$servername = "localhost"; // Veritabanı sunucu adı
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı şifresi
$dbname = "veri"; // Veritabanı adı (".sql" uzantısı kullanılmaz)

// Veritabanı bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
} else {
    echo "Veritabanına başarıyla bağlanıldı!";
}
?>
