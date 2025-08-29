<?php
// Token seguro para validación del webhook
$verify_token = "4f9d8a7c2b1e6f0d3a9c5b8e7d2f1a0b";

// Verificación inicial (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';

    if ($mode === 'subscribe' && $token === $verify_token) {
        echo $challenge;
    } else {
        http_response_code(403);
        echo "Error: Token inválido.";
    }
    exit;
}

// Recepción del webhook (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar el JSON crudo
    $input = file_get_contents('php://input');

    // Log para depuración
    file_put_contents('webhook_log.txt', "[" . date('Y-m-d H:i:s') . "] " . $input . PHP_EOL, FILE_APPEND);

    // Decodificar JSON
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo "Invalid JSON";
        exit;
    }

    // Verificar si hay mensajes
    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];

        // Extraer datos del mensaje
        $from        = $message['from'] ?? '';
        $message_id  = $message['id'] ?? '';
        $body        = $message['text']['body'] ?? '';
        $raw_json    = $input;

        // Validar datos mínimos antes de insertar
        if (!empty($from) && !empty($message_id)) {
            define( '_VALID_MOS', 1 );

            date_default_timezone_set('America/Mexico_City');
            
            require_once('includes/configuration.php');
            require_once('includes/DB.php');
            $db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
            // Construcción del array
            $dataLog = [
                'id_log'       => null,
                'datelog'      => date("Y-m-d H:i:s"),
                'sender_phone' => $from,
                'message_id'   => $message_id,
                'message_text' => $body,
                'raw_json'     => $raw_json
            ];

            try {
                // Insertar en base de datos
                $db->insert('waba_callbacks', $dataLog);

                // Log adicional para confirmar inserción
                file_put_contents('webhook_log.txt', "INSERT OK: $from - $body" . PHP_EOL, FILE_APPEND);
            } catch (Exception $e) {
                file_put_contents('webhook_log.txt', "DB ERROR: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents('webhook_log.txt', "Datos incompletos: " . print_r($message, true) . PHP_EOL, FILE_APPEND);
        }
    }

    http_response_code(200);
    echo "EVENT_RECEIVED";
    exit;
}

// Si no es GET ni POST
http_response_code(404);
echo "Not Found";
