<?php
require_once"../../config/koneksi.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

/**
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$id_pegawai = $res['id_pegawai'] ?? '';
/**
 * Validation int value
 */
$idFilter = filter_var($id_pegawai, FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($idFilter === false){
    $reply['error'] = "id_pegawai harus format INT";
    $isValidated = false;
}
if(empty($id_pegawai)){

    $reply['error'] = 'id_pegawai harus diisi';
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
 *
 * Cek apakah ISBN tersedia
 */
try{
    $queryCheck = "SELECT * FROM pegawai where id_pegawai = :id_pegawai";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_pegawai', $id_pegawai);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID '.$id_pegawai;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM pegawai where id_pegawai = :id_pegawai";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_pegawai', $id_pegawai);
    if(!$statement->execute()){
        $reply['error'] = $statement->errorInfo();
        echo json_encode($reply);
        http_response_code(400);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
$reply['status'] = true;
echo json_encode($reply);