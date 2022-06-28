<?php
//ambil data dari value array
$var1 = $_GET['var1'] ?? 0;
$var2 = $_GET['var2'] ?? 0;
// Diproses
$hasil = $var1 * $var2;

$data = [
    'perkalian' => [
        'var1' => $var1,
        'var2' => $var2,
        'hasil' => $hasil
    ]
];
$response['data'] = $data;
echo json_encode($data);