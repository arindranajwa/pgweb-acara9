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

    // Ambil data berdasarkan id
    $sql = "SELECT * FROM jml_pddk WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $marker = $result->fetch_assoc();

    if (!$marker) {
        echo "Data tidak ditemukan.";
        exit;
    }
} else {
    echo "ID tidak ditemukan.";
    exit;
}

// Proses pengeditan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kecamatan = $_POST['kecamatan'];
    $longitude = $_POST['longitide'];
    $latitude = $_POST['latitude'];
    $luas = $_POST['luas'];
    $jumlah_penduduk = $_POST['jumlah_penduduk'];

    // Query untuk mengupdate data
    $sql = "UPDATE jml_pddk SET kecamatan = ?, longitide = ?, latitude = ?, luas = ?, jumlah_penduduk = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdii", $kecamatan, $longitude, $latitude, $luas, $jumlah_penduduk, $id);

    if ($stmt->execute()) {
        echo "Data berhasil diperbarui.";
        header("Location: latihan8a.php"); // Ganti dengan nama file yang sesuai
        exit;
    } else {
        echo "Gagal memperbarui data: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!-- Form untuk mengedit data -->
<form method="post">
    <label for="kecamatan">Kecamatan:</label>
    <input type="text" name="kecamatan" value="<?php echo htmlspecialchars($marker['kecamatan']); ?>" required><br>

    <label for="longitide">Longitude:</label>
    <input type="text" name="longitide" value="<?php echo htmlspecialchars($marker['longitide']); ?>" required><br>

    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" value="<?php echo htmlspecialchars($marker['latitude']); ?>" required><br>

    <label for="luas">Luas:</label>
    <input type="number" name="luas" value="<?php echo htmlspecialchars($marker['luas']); ?>" required><br>

    <label for="jumlah_penduduk">Jumlah Penduduk:</label>
    <input type="number" name="jumlah_penduduk" value="<?php echo htmlspecialchars($marker['jumlah_penduduk']); ?>" required><br>

    <input type="submit" value="Update">
</form>