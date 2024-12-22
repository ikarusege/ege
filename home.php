<?php
session_start();

// Kullanıcı kontrolü
if (!isset($_SESSION['kullanici'])) {
    header('Location: login.php');
    exit;
}

// Giriş yapan kullanıcının adını al
$user = $_SESSION['kullanici'];

// Sayfa içeriği
echo "<h1>Hoşgeldiniz, $user</h1>";
echo "<p>Burada kullanıcı paneline, admin paneline ya da diğer sayfalara yönlendirebilirsiniz.</p>";

// Admin kontrolü
if ($user === 'admin') {
    echo "<p>Admin olarak giriş yaptınız. <a href='admin_panel.php'>Admin Paneli'ne</a> gidebilirsiniz.</p>";
} else {
    echo "<p>Normal kullanıcı olarak giriş yaptınız. <a href='user_panel.php'>Kullanıcı Paneli'ne</a> gidebilirsiniz.</p>";
}

echo "<p><a href='logout.php'>Çıkış Yap</a></p>";
?>
