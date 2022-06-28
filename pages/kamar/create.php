<?php
require_once"../../config/koneksi.php";
/**
 *
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(400);
    $reply['error'] = 'POST method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data POST
 */
$id_kamar = $_POST['id_kamar'] ?? '';
$tipe_kamar = $_POST['tipe_kamar'] ?? '';
$nama_pelanggan = $_POST['nama_pelanggan'] ?? '';
$tanggal_masuk = $_POST['tanggal_masuk'] ?? date('Y-m-d');
$tanggal_keluar = $_POST['tanggal_keluar'] ?? date('Y-m-d');
$harga = $_POST['harga'] ?? '';
$id_pelanggan = $_POST['id_pelanggan'] ?? '';

/**
 * Validation int value
 */
$jumlahFilter = filter_var($id_pelanggan, FILTER_VALIDATE_INT);


/**
 * Validation empty fields
 */
$isValidated = true;
if($jumlahFilter === false) {
    $reply['error'] = "Jumlah harus format INT";
    $isValidated = false;
}
if(empty($id_kamar)){
    $reply['error'] = 'ID kamar harus diisi';
    $isValidated = false;
}
if(empty($tipe_kamar)){
    $reply['error'] = 'tipe_kamar harus diisi';
    $isValidated = false;
}
if(empty($nama_pelanggan)){
    $reply['error'] = 'NAMA pelanggan harus diisi';
    $isValidated = false;
}
if(empty($tanggal_masuk)){
    $reply['error'] = 'Data harus diisi';
    $isValidated = false;
}
if(empty($tanggal_keluar)){
    $reply['error'] = 'Data harus diisi';
    $isValidated = false;
}
if(empty($harga)){
    $reply['error'] = 'harga harus diisi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO id_kamar (id_kamar, tipe_kamar, nama_pelanggan, tanggal_masuk, tanggal_keluar, harga, id_pelanggan) VALUES (:id_kamar, :tipe_kamar,:nama_pelanggan, :tanggal_masuk, :tanggal_keluar, :harga, :id_pelanggan)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_kamar", $id_kamar);
    $statement->bindValue(":tipe_kamar", $tipe_kamar);
    $statement->bindValue(":nama_pelanggan", $nama_pelanggan);
    $statement->bindValue(":tanggal_masuk", $tanggal_masuk);
    $statement->bindValue(":tanggal_keluar", $tanggal_keluar);
    $statement->bindValue(":harga", $harga);
    $statement->bindValue(":id_pelanggan", $id_pelanggan);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/*
 * Get last data
 */
$lastId = $connection->lastInsertId();
$getResult = "SELECT * FROM kamar WHERE id_kamar = :id_kamar";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_kamar', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/*
 * Get pelanggan
 */
$stmpelanggan = $connection->prepare("SELECT * FROM kamar where id_pelanggan = :id_pelanggan");
$stmpelanggan->bindValue(':id_pelanggan', $result['id_pelanggan']);
$stmpelanggan->execute();
$resultpelanggan = $stmpelanggan->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat pegawai 'Tidak diketahui'
 */
$id_pelanggan = [
    'id_pelanggan' => $result['id_pelanggan'],
    'nama' => 'Tidak diketahui'
];
if ($resultpelanggan) {
    $kategori = [
        'id' => $resultpelanggan['id_pelanggan'],
        'nama' => $resultpelanggan['nama']
    ];
}
/*
 * Transform result
 */
$dataFinal = [
    'id_kamar' => $result['id_pelanggan'],
    'tipe_kamar' => $result['tipe_kamar'],
    'nama_pelanggan' => $result['nama_pelanggan'],
    'tanggal_masuk' => $result['tanggal_masuk'],
    'tanggal_keluar' => $result['tanggal_keluar'],
    'harga' => $result['harga'],
    'created_at' => $result['created_at'],
    'id_pelanggan' => $id_pelanggan,
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);