<?php
header('Content-Type: application/json; charset=utf-8');
// header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['index'])) {
http_response_code(400);
echo json_encode(['error' => 'Parametro index mancante']);
exit;
}

$index = (int)$input['index'];
$file_name = 'transcriptions.json';

if (!file_exists($file_name)) {
http_response_code(404);
echo json_encode(['error' => 'File appuntamenti non trovato']);
exit;
}

$content = file_get_contents($file_name);
$records = json_decode($content, true);
if (!is_array($records)) $records = [];

if (!isset($records[$index])) {
http_response_code(404);
echo json_encode(['error' => 'Appuntamento indicizzato non trovato']);
exit;
}

// Rimuove elemento array con indice $index mantenendo indice compatto
array_splice($records, $index, 1);

if (file_put_contents($file_name, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
http_response_code(500);
echo json_encode(['error' => 'Errore salvataggio file']);
exit;
}

echo json_encode(['success' => true]);