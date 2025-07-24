<?php
header('Content-Type: application/json; charset=utf-8'); // Yanıtın JSON ve UTF-8 olacağını belirt

$servername="localhost";
$username="root";
$password="";
$dbname="sozluk";


// Veritabanı bağlantısı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die(json_encode(["error" => "Veritabanı bağlantısı başarısız: " . $conn->connect_error]));
}

// Türkçe karakter sorunlarını çözmek için karakter setini ayarlama
$conn->set_charset("utf8mb4");

// GET isteği ile gelen kelimeyi alma
$arananKelime = isset($_GET['kelime']) ? $_GET['kelime'] : '';

$sonuclar = [];

if (!empty($arananKelime)) {
    // SQL Injection saldırılarını önlemek için prepare statement kullanma
    $stmt = $conn->prepare("SELECT name, description FROM dictionary WHERE name = ? OR name LIKE ? ORDER BY CASE WHEN name = ? THEN 0 ELSE 1 END, name ASC LIMIT 10");
    $searchTerm = $arananKelime . "%"; // Kelimenin başında veya sonunda olabilir
    $stmt->bind_param("sss",$arananKelime, $searchTerm, $arananKelime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sonuclar[] = $row;
        }
    }
    $stmt->close();
}
echo json_encode($sonuclar); // Sonuçları JSON formatında geri döndür

$conn->close();
?>