<?php
// Sesuaikan dengan setting MySQL
// Pengaturan Koneksi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgwe-acara8";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  mengambil semua data dari tabel jml_pddk
$sql = "SELECT * FROM jml_pddk";
$result = $conn->query($sql);

// Array untuk menyimpan data marker
$markers = [];

// Jika ada data yang ditemukan, program membuat tabel HTML untuk menampilkan kolom
if ($result->num_rows > 0) {
    echo "<table border='1px'><tr>
    <th>Id</th>    
    <th>Kecamatan</th>
    <th>Longitude</th>
    <th>Latitude</th>
    <th>Luas</th>
    <th>Jumlah Penduduk</th>
    <th>Delete</th>
    <th>Edit</th>";

    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
        <td>" . $row["id"] . "</td>
        <td>" . $row["kecamatan"] . "</td>
        <td>" . $row["longitide"] . "</td>
        <td>" . $row["latitude"] . "</td>
        <td>" . $row["luas"] . "</td>
        <td align='center'>" . $row["jumlah_penduduk"] . "</td>
        <td>
            <a href='delete.php?id=" . $row["id"] . "' onclick=\"return confirm
            ('Apakah Anda yakin ingin menghapus data ini?');\">Delete</a>
            </td>
        <td>
            <a href='edit.php?id=" . $row["id"] . "' onclick=\"return confirm
            ('Apakah Anda yakin ingin mengedit data ini?');\">Edit</a>
            </td>
            </tr>";

        // Menyimpan data marker untuk Peta
        $markers[] = [
            "kecamatan" => $row["kecamatan"],
            "longitide" => (float)$row["longitide"],
            "latitude" => (float)$row["latitude"],
            "luas" => (float)$row["luas"],
            "jumlah_penduduk" => (int)$row["jumlah_penduduk"],
        ];
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>

<!-- Menampilkan Peta dengan Leaflet JS -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peta Jumlah Penduduk</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        /* digunakan untuk mengatur ukuran dan tinggi peta yang akan ditampilkan */
        /* # digunakan untuk memanggil id yg ada di line 21 */
        #map {
            width: 100%;
            height: 400px;
            margin-bottom: 10px;
        }

        /* Gaya utama halaman */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #9B7EBD;
            margin: 20px;
        }

        /* Gaya untuk tabel agar berada di tengah */
        .center-table {
            display: none;
            width: 0%;
            max-width: 800px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-height: 400px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4A148C;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Judul Halaman -->
    <h1>Peta Jumlah Penduduk</h1>

    <!-- Peta -->
    <div id="map"></div>

    <!-- Library Leaflet JS diimpor untuk mengaktifkan fitur pemetaan interaktif -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        /* Gaya utama halaman */
        body {
            font-family: 'Times New Roman', sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            color: #4A148C;
            margin: 0px;
            font-size: 2.3em;
            text-shadow: 9px 7px 9px rgba(0, 0, 0, 0.2);
        }

        /* Peta */
        #map {
            width: 100%;
            height: 350px;
            margin-bottom: 10px;
            border: 2px solid #9B7EBD;
            border-radius: 0px;
        }

        /* Tabel */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0
        }
    </style>
    <script>
        // Inisialisasi peta
        var map = L.map('map');

        // Tile layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Data marker dikonversi dari PHP ke JavaScript menggunakan json_encode
        var markers = <?php echo json_encode($markers); ?>;

        // Membuat bounds peta, Peta disesuaikan untuk menampilkan semua marker
        var bounds = L.latLngBounds();

        // Menambahkan marker dan memperluas bounds peta
        markers.forEach(function(marker) {
            var latLng = [marker.latitude, marker.longitide];
            L.marker(latLng)
                .addTo(map)
                .bindPopup("<b>" + marker.kecamatan + "</b>");
            bounds.extend(latLng);
        });

        // Menyesuaikan peta agar sesuai dengan semua marker
        map.fitBounds(bounds);
    </script>

</body>

</html>