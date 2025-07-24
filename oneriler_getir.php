<?php
header('Content-Type: application/json; charset=utf8mb4');
mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
ini_set('display_errors',1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sozluk";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Veritabanı bağlantısı başarısız: " . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8mb4");
$kelimeParcasi = isset($_GET['kelime']) ? $_GET['kelime'] : '';
$kelimeParcasi = mb_strtoupper($kelimeParcasi, 'UTF-8'); // Öneriler için de büyük harfe çevir
$oneriler = [];

if (!empty($kelimeParcasi)) {
    // Sadece kelime başlangıcına göre arayabiliriz veya kelime içinde geçenleri de gösterebiliriz
    // 'name LIKE ?' -> kelimenin o parça ile başlaması için: $searchTerm = $kelimeParcasi . "%";
    // 'name LIKE ?' -> kelimenin o parça ile bitmesi için: $searchTerm = "%" . $kelimeParcasi;
    // 'name LIKE ?' -> kelimenin o parçayı içermesi için: $searchTerm = "%" . $kelimeParcasi . "%";

    // Öneriler için genellikle başlangıca göre arama daha performanslıdır ve mantıklıdır
    $stmt = $conn->prepare("SELECT name FROM dictionary WHERE name LIKE ? LIMIT 10"); // İlk 10 öneriyi getir
    $searchTerm = $kelimeParcasi . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $oneriler[] = $row['name']; 
        }
    }
    $stmt->close();
}

echo json_encode($oneriler);
$conn->close();
?>