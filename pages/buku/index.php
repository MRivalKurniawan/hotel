<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:22
 * @var $connection PDO
 */
try{
    /**
     * Prepare query buku limit 50 rows
     */
    $statement = $connection->prepare("select * from buku order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsBuku = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data pegawai
     */
    $stmKategori = $connection->prepare("select * from pegawai");
    $isOk = $stmKategori->execute();
    $resultKategori = $stmKategori->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table buku dan pegawai
     * Gabungkan data berdasarkan kolom id pegawai
     * Jika id pegawai tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsKetegori = array_column($resultKategori, 'id');
    foreach ($resultsBuku as $buku){
        /*
         * Default pegawai 'Tidak diketahui'
         */
        $kategori = [
            'id' => $buku['pegawai'],
            'nama' => 'Tidak diketahui'
        ];
        /*
         * Cari pegawai berd id
         */
        $findByIdKategori = array_search($buku['pegawai'], $idsKetegori);

        /*
         * Jika id ditemukan
         */
        if($findByIdKategori !== false){
            $findDataKategori = $resultKategori[$findByIdKategori];
            $kategori = [
                'id' => $findDataKategori['id'],
                'nama' => $findDataKategori['nama']
            ];
        }

        $finalResults[] = [
            'isbn' => $buku['isbn'],
            'judul' => $buku['judul'],
            'pengarang' => $buku['pengarang'],
            'tanggal' => $buku['tanggal'],
            'jumlah' => $buku['jumlah'],
            'created_at' => $buku['created_at'],
            'pegawai' => $kategori,
            'abstrak' => $buku['abstrak']
        ];
    }

    $reply['data'] = $finalResults;
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/*
 * Query OK
 * set status == true
 * Output JSON
 */
$reply['status'] = true;
echo json_encode($reply);