<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 02/06/2022
 * Time: 20:07
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'PATCH'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'PATCH method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$isbn = $formData['isbn'] ?? '';
$judul = $formData['judul'] ?? '';
$pengarang = $formData['pengarang'] ?? '';
$jumlah = $formData['jumlah'] ?? 0;
$abstrak = $formData['abstrak'] ?? '';
$tanggal = $formData['tanggal'] ?? date('Y-m-d');
$idKategori = $formData['pegawai'] ?? 0;

/**
 * Validation int value
 */
$jumlahFilter = filter_var($jumlah, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($jumlahFilter === false){
    $reply['error'] = "Jumlah harus format INT";
    $isValidated = false;
}
if(empty($isbn)){
    $reply['error'] = 'ISBN harus diisi';
    $isValidated = false;
}
if(empty($judul)){
    $reply['error'] = 'JUDUL harus diisi';
    $isValidated = false;
}
if(empty($pengarang)){
    $reply['error'] = 'PENGARANG harus diisi';
    $isValidated = false;
}
if(empty($abstrak)){
    $reply['error'] = 'ABSTRAK harus diisi';
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM buku where isbn = :isbn";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':isbn', $isbn);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ISBN '.$isbn;
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
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE buku SET judul = :judul, pengarang = :pengarang, jumlah = :jumlah, tanggal = :tanggal, abstrak = :abstrak, pegawai = :pegawai 
WHERE isbn = :isbn";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":isbn", $isbn);
    $statement->bindValue(":judul", $judul);
    $statement->bindValue(":pengarang", $pengarang);
    $statement->bindValue(":jumlah", $jumlah, PDO::PARAM_INT);
    $statement->bindValue(":tanggal", $tanggal);
    $statement->bindValue(":abstrak", $abstrak);
    $statement->bindValue(":pegawai", $idKategori, PDO::PARAM_INT);
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
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM buku where isbn = :isbn");
$stmSelect->bindValue(':isbn', $isbn);
$stmSelect->execute();
$dataBuku = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data pegawai berdasarkan kolom pegawai
 */
$dataFinal = [];
if($dataBuku) {
    $stmKategori = $connection->prepare("select * from pegawai where id = :id");
    $stmKategori->bindValue(':id', $dataBuku['pegawai']);
    $stmKategori->execute();
    $resultKategori = $stmKategori->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat pegawai 'Tidak diketahui'
     */
    $kategori = [
        'id' => $dataBuku['pegawai'],
        'nama' => 'Tidak diketahui'
    ];
    if ($resultKategori) {
        $kategori = [
            'id' => $resultKategori['id'],
            'nama' => $resultKategori['nama']
        ];
    }

    /*
     * Transoform hasil query dari table buku dan pegawai
     * Gabungkan data berdasarkan kolom id pegawai
     * Jika id pegawai tidak ditemukan, default "tidak diketahui'
     */
    $dataFinal = [
        'isbn' => $dataBuku['isbn'],
        'judul' => $dataBuku['judul'],
        'pengarang' => $dataBuku['pengarang'],
        'tanggal' => $dataBuku['tanggal'],
        'jumlah' => $dataBuku['jumlah'],
        'created_at' => $dataBuku['created_at'],
        'pegawai' => $kategori,
        'abstrak' => $dataBuku['abstrak'],
    ];
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);