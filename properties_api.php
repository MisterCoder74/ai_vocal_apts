<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$file = 'properties.json';

function readProperties($file) {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}
function writeProperties($file, $items) {
    file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'list':
        echo json_encode(readProperties($file));
        break;

    case 'edit':
        $body          = json_decode(file_get_contents('php://input'), true);
        $index         = $body['index'] ?? null;
        $updatedRecord = $body['updatedRecord'] ?? null;
        if ($index === null || $updatedRecord === null) {
            http_response_code(400);
            echo json_encode(['error' => 'index o updatedRecord mancante']);
            break;
        }
        $items = readProperties($file);
        if (!isset($items[$index])) {
            http_response_code(404);
            echo json_encode(['error' => 'Proprietà non trovata']);
            break;
        }
        $items[$index] = $updatedRecord;
        writeProperties($file, $items);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $body  = json_decode(file_get_contents('php://input'), true);
        $index = $body['index'] ?? null;
        if ($index === null) {
            http_response_code(400);
            echo json_encode(['error' => 'index mancante']);
            break;
        }
        $items = readProperties($file);
        if (!isset($items[$index])) {
            http_response_code(404);
            echo json_encode(['error' => 'Proprietà non trovata']);
            break;
        }
        array_splice($items, $index, 1);
        writeProperties($file, $items);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Azione non riconosciuta: ' . htmlspecialchars($action)]);
}
?>
