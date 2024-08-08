<?php
$servername = "127.0.0.1"; // localhost yerine IP adresi kullanabilirsiniz
$username = "root"; // Doğru kullanıcı adı
$password = ""; // Varsayılan şifre genellikle boştur
$dbname = "veritabani_adiniz"; // Veritabanı adınız

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
