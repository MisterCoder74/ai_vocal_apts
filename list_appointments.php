<?php
header('Content-Type: application/json; charset=utf-8');
// Abilita CORS se frontend su dominio differente:
// header('Access-Control-Allow-Origin: *');

$file_name = 'transcriptions.json';
if (!file_exists($file_name)) {
echo json_encode([]);
exit;
}

$content = file_get_contents($file_name);
$records = json_decode($content, true);
if (!is_array($records)) $records = [];

echo json_encode($records);