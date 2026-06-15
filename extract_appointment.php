<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// I tuoi header e codice...
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// POST JSON input, contenente { transcription: "testo da elaborare" }
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['transcription']) || !is_string($input['transcription'])) {
http_response_code(400);
echo json_encode(['error' => 'Manca o non valido campo transcription']);
exit;
}

$transcription = trim($input['transcription']);
if ($transcription === '') {
http_response_code(400);
echo json_encode(['error' => 'Transcription vuoto']);
exit;
}

// Recupera API key da environment o da file di configurazione per sicurezza
// qui supponiamo venga passata come variabile d'ambiente OPENAI_API_KEY
$openai_api_key = isset($input['openai_api_key']) ? trim($input['openai_api_key']) : null;
if (!$openai_api_key) {
http_response_code(400);
echo json_encode(['error' => 'API Key OpenAI mancante nel corpo della richiesta']);
exit;
}


// Prepara prompt per estrarre i dati richiesti in JSON
$prompt = <<<EOD
Estrai in JSON i seguenti campi dal testo fornito: cliente, telefono, città, indirizzo, data (formato DD-MM-YYYY), orario (formato HH:MM).
Rispondi solo con un JSON valido e non inserire blocchi di codice o markdown o spiegazioni.
Testo: "{$transcription}"
Risposta JSON:
EOD;

// Prepara payload OpenAI
$post_fields = json_encode([
"model" => "gpt-4o-mini",
"messages" => [
["role" => "user", "content" => $prompt]
],
"temperature" => 0
]);

// Effettua chiamata cURL
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

// Decodifica risposta OpenAI
$gptResult = json_decode($response, true);
if (!isset($gptResult['choices'][0]['message']['content'])) {
http_response_code(500);
echo json_encode(['error' => 'Risposta GPT non valida']);
exit;
}

$jsonStrRaw = trim($gptResult['choices'][0]['message']['content']);

// Rimuove blocchi markdown ... o ...
$jsonStr = preg_replace('/^\s*|$/mi', '', $jsonStrRaw);
// In alternativa rimuove qualsiasi triple backtick (non solo json)
$jsonStr = preg_replace('/^|$/m', '', $jsonStr);
$jsonStr = trim($jsonStr);

// A questo punto prova a decodificare il JSON pulito
$data = json_decode($jsonStr, true);

if ($data === null) {
// Fallback alla risposta grezza se json non valido
$data = ["raw_response" => $jsonStrRaw];
}

// Opzionale: salva i dati estratti in un file JSON (append)
$file_name = 'transcriptions.json';
$records = [];

if (file_exists($file_name)) {
$content = file_get_contents($file_name);
$records = json_decode($content, true);
if (!is_array($records)) $records = [];
}

$records[] = [
'timestamp' => date('c'),
'transcription' => $transcription,
'extracted_data' => $data
];

// Salva su file, formato leggibile con unicode non escape, indentato
if (file_put_contents($file_name, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
// Non bloccare la risposta se fallisce il salvataggio
error_log("Impossibile salvare file {$file_name}");
}

// Risposta JSON al client
echo json_encode([
'transcription' => $transcription,
'extracted_data' => $data
]);
