<?php
session_start(); // Oturum başlat

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "users_db.sql");

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Giriş bilgilerini al
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($username) || empty($password)) {
    echo "Lütfen kullanıcı adı ve şifre girin!";
    exit();
}

// Kullanıcı bilgilerini kontrol et
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Kullanıcı oturumu başlat
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id']; // Kullanıcı ID'si
    $_SESSION['username'] = $user['username']; // Kullanıcı adı

    // Admin kontrolü
    if ($user['is_admin'] == 1) {
        header("Location: admin_panell.php"); // Admin paneline yönlendir
    } else {
        header("Location: home.php"); // Kullanıcı ana sayfasına yönlendir
    }
    exit();
} else {
    echo "Hatalı kullanıcı adı veya şifre!";
    echo "<br><a href='loginn.php'>Geri dön</a>";
}

// Bağlantıyı kapat
$conn->close();
?>
