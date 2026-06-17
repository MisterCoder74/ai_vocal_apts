<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$action = $_GET['action'] ?? '';
$file_name = 'transcriptions.json';

function readRecords(string $file_name): array {
    if (!file_exists($file_name)) return [];
    $content = file_get_contents($file_name);
    $records = json_decode($content, true);
    return is_array($records) ? $records : [];
}

function saveRecords(string $file_name, array $records): bool {
    return file_put_contents(
        $file_name,
        json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

switch ($action) {

    // ── LIST ──────────────────────────────────────────────
    case 'list':
        echo json_encode(readRecords($file_name));
        break;

    // ── EDIT ──────────────────────────────────────────────
    case 'edit':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['index'], $input['updatedRecord'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Parametri mancanti (index, updatedRecord)']);
            exit;
        }
        $index = (int) $input['index'];
        $records = readRecords($file_name);
        if (!array_key_exists($index, $records)) {
            http_response_code(404);
            echo json_encode(['error' => "Appuntamento all'indice {$index} non trovato"]);
            exit;
        }
        $records[$index] = $input['updatedRecord'];
        if (!saveRecords($file_name, $records)) {
            http_response_code(500);
            echo json_encode(['error' => 'Errore salvataggio file']);
            exit;
        }
        echo json_encode(['success' => true]);
        break;

    // ── DELETE ────────────────────────────────────────────
    case 'delete':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['index'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Parametro index mancante']);
            exit;
        }
        $index = (int) $input['index'];
        $records = readRecords($file_name);
        if (!array_key_exists($index, $records)) {
            http_response_code(404);
            echo json_encode(['error' => "Appuntamento all'indice {$index} non trovato"]);
            exit;
        }
        array_splice($records, $index, 1);
        if (!saveRecords($file_name, $records)) {
            http_response_code(500);
            echo json_encode(['error' => 'Errore salvataggio file']);
            exit;
        }
        echo json_encode(['success' => true]);
        break;

    // ── FALLBACK ──────────────────────────────────────────
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Azione non valida. Usa ?action=list|edit|delete']);
}
