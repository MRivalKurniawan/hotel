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
$id_pegawai = $_POST['id_pegawai'] ?? '';
$nama_pegawai = $_POST['nama_pegawai'] ?? '';
$jenis_kelamain = $_POST['jenis_kelamin'] ?? '';
$umur = $_POST['umur'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nohp = $_POST['nohp'] ?? '';

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($id_pegawai)){
    $reply['error'] = 'ID pegawai harus diisi';
    $isValidated = false;
}
if(empty($nama_pegawai)){
    $reply['error'] = 'NAMA pegawai harus diisi';
    $isValidated = false;
}
if(empty($jenis_kelamain)){
    $reply['error'] = 'Data harus diisi';
    $isValidated = false;
}
if(empty($umur)){
    $reply['error'] = 'Umur harus diisi';
    $isValidated = false;
}
if(empty($alamat)){
    $reply['error'] = 'alamat harus diisi';
    $isValidated = false;
}
if(empty($nohp)){
    $reply['error'] = 'NO HP harus diisi';
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
    $query = "INSERT INTO pegawai (id_pegawai, nama_pegawai, jenis_kelamin, umur, alamat, nohp) VALUES (:id_pegawai,:nama_pegawai, :jenis_kelamin, :umur, :alamat, :nohp)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_pegawai", $id_pegawai);
    $statement->bindValue(":nama_pegawai", $nama_pegawai);
    $statement->bindValue(":jenis_kelamin", $jenis_kelamain);
    $statement->bindValue(":umur", $umur);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nohp", $nohp);
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
$getResult = "SELECT * FROM pegawai WHERE id_pegawai = :id_pegawai";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_pegawai', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);


/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);