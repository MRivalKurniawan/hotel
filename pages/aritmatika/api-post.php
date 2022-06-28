<?php
/**
 * Data POST
 */
$var1 = $_POST['var1'] ?? 0;
$var2 = $_POST['var2'] ?? 0;
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