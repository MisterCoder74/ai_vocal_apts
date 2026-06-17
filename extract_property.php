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
Estrai in JSON i seguenti campi dal testo fornito: indirizzo, città, prezzo, tipo, stato, superficie, descrizione.
Regole di estrazione:
  - "prezzo":     solo il numero intero in euro (es. 280000), senza simboli, punti migliaia o testo
  - "tipo":       uno tra [ appartamento · villa · negozio · ufficio · garage · terreno · altro ]
  - "stato":      uno tra [ disponibile · venduto · in trattativa · in affitto ]
  - "superficie": solo il numero intero in mq
  - Se un campo non è presente nel testo, metti null.
Esempio input  : "appartamento via Roma 15 Milano, disponibile, 280000 euro, 80 mq, bilocale con terrazzo"
Esempio output : {"indirizzo":"via Roma 15","città":"Milano","prezzo":280000,"tipo":"appartamento","stato":"disponibile","superficie":80,"descrizione":"bilocale con terrazzo"}

Rispondi solo con un JSON valido, senza blocchi di codice, senza markdown, senza spiegazioni.
Testo: "{$transcription}"
Risposta JSON:
EOD;

$post_fields = json_encode([
    "model"       => "gpt-4o-mini",
    "messages"    => [["role" => "user", "content" => $prompt]],
    "temperature" => 0
]);

$curl = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        "Content-Type: application/json",
        "Authorization: Bearer {$openai_api_key}"
    ],
    CURLOPT_POSTFIELDS     => $post_fields,
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
$jsonStr    = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $jsonStrRaw);
$data       = json_decode(trim($jsonStr), true);
if ($data === null) { $data = ["raw_response" => $jsonStrRaw]; }

$file = 'properties.json';
$records = [];
if (file_exists($file)) {
    $existing = json_decode(file_get_contents($file), true);
    if (is_array($existing)) $records = $existing;
}
$records[] = [
    'timestamp'      => date('Y-m-d H:i:s'),
    'transcription'  => $transcription,
    'extracted_data' => $data
];
file_put_contents($file, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true, 'extracted_data' => $data]);
