<?php
// Konfigurasi koneksi MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgwe-acara8";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah ada `id` yang diterima
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data berdasarkan id
    $sql = "DELETE FROM jml_pddk WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Data berhasil dihapus.";
    } else {
        echo "Gagal menghapus data: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "ID tidak ditemukan.";
}

// Tutup koneksi
$conn->close();

// Kembali ke halaman utama
// header("Location: index.html");
// exit;
?>
