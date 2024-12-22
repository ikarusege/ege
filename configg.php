<?php
$servername = "localhost"; // Veritabanı sunucusu (genellikle 'localhost' olur)
$username = "root"; // Veritabanı kullanıcı adı (kendi kullanıcı adınızı yazın)
$password = ""; // Veritabanı şifresi (kendi şifrenizi yazın)
$dbname = "books"; // Kullanacağınız veritabanının adı (kendi veritabanı adınızı yazın)

// Bağlantıyı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
