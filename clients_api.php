<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$file = 'clients.json';

function readClients($file) {
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}
function writeClients($file, $clients) {
    file_put_contents($file, json_encode($clients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'list':
        echo json_encode(readClients($file));
        break;

    case 'edit':
        $body = json_decode(file_get_contents('php://input'), true);
        $index = $body['index'] ?? null;
        $updatedRecord = $body['updatedRecord'] ?? null;
        if ($index === null || $updatedRecord === null) {
            http_response_code(400);
            echo json_encode(['error' => 'index o updatedRecord mancante']);
            break;
        }
        $clients = readClients($file);
        if (!isset($clients[$index])) {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente non trovato']);
            break;
        }
        $clients[$index] = $updatedRecord;
        writeClients($file, $clients);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $body = json_decode(file_get_contents('php://input'), true);
        $index = $body['index'] ?? null;
        if ($index === null) {
            http_response_code(400);
            echo json_encode(['error' => 'index mancante']);
            break;
        }
        $clients = readClients($file);
        if (!isset($clients[$index])) {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente non trovato']);
            break;
        }
        array_splice($clients, $index, 1);
        writeClients($file, $clients);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Azione non riconosciuta: ' . htmlspecialchars($action)]);
}
?>