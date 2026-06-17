<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['transcription']) || !is_string($input['transcription'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Campo transcription mancante o non valido']);
    exit;
}
$transcription = trim($input['transcription']);
if ($transcription === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Transcription vuota']);
    exit;
}
$openai_api_key = isset($input['openai_api_key']) ? trim($input['openai_api_key']) : null;
if (!$openai_api_key) {
    http_response_code(400);
    echo json_encode(['error' => 'API Key OpenAI mancante']);
    exit;
}

$prompt = <<<EOD
Estrai in JSON i seguenti campi dal testo fornito: nome, telefono, email, città, note.
Se un campo non è presente nel testo, metti null.
Rispondi solo con un JSON valido, senza blocchi di codice, senza markdown, senza spiegazioni.
Testo: "{$transcription}"
Risposta JSON:
EOD;

$post_fields = json_encode([
    "model" => "gpt-4o-mini",
    "messages" => [["role" => "user", "content" => $prompt]],
    "temperature" => 0
]);

$curl = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer {$openai_api_key}"
    ],
    CURLOPT_POSTFIELDS => $post_fields,
]);
$response = curl_exec($curl);
if (curl_errno($curl)) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore cURL: ' . curl_error($curl)]);
    exit;
}
curl_close($curl);

$gptResult = json_decode($response, true);
if (!isset($gptResult['choices'][0]['message']['content'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Risposta GPT non valida', 'raw' => $response]);
    exit;
}

$jsonStrRaw = trim($gptResult['choices'][0]['message']['content']);
$jsonStr = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $jsonStrRaw);
$jsonStr = trim($jsonStr);
$data = json_decode($jsonStr, true);
if ($data === null) {
    $data = ["raw_response" => $jsonStrRaw];
}

$file_name = 'clients.json';
$records = [];
if (file_exists($file_name)) {
    $existing = json_decode(file_get_contents($file_name), true);
    if (is_array($existing)) $records = $existing;
}
$records[] = [
    'timestamp'      => date('Y-m-d H:i:s'),
    'transcription'  => $transcription,
    'extracted_data' => $data
];
file_put_contents($file_name, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true, 'extracted_data' => $data]);