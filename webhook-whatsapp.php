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

    define('_VALID_MOS', 1);

    require_once('includes/configuration.php');
    require_once('includes/DBW.php');
    $db = new DB(HOST, USERNAME, PASSWD, DBNAME, PORT, SOCKET);

    // Capturar el JSON crudo
    $input = file_get_contents('php://input');

    // Log para depuración
    file_put_contents(date('Y-m-d').'general', "[" . date('Y-m-d H:i:s') . "] " . $input . PHP_EOL, FILE_APPEND);

    // Decodificar JSON
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo "Invalid JSON";
        exit;
    }

       // ✅ 1. Procesar estados de mensajes enviados
    if (isset($data['entry'][0]['changes'][0]['value']['statuses'][0])) {
        $status = $data['entry'][0]['changes'][0]['value']['statuses'][0];

        $message_id  = $status['id'] ?? '';
        $status_name = $status['status'] ?? '';
        $recipient   = $status['recipient_id'] ?? '';
        $timestamp   = isset($status['timestamp']) ? date('Y-m-d H:i:s', $status['timestamp']) : date('Y-m-d H:i:s');

        if (!empty($message_id) && !empty($status_name)) {
            $sql = "INSERT INTO waba_status (datelog, message_id, status_name, recipient_phone, raw_json)
                VALUES ('$timestamp', '$message_id', '$status_name', '$recipient', '" . addslashes($input) . "')";

            try {
                $db->sqlPure($sql, false);
                #file_put_contents('webhook_log_estatus_ok.txt', "STATUS OK: $message_id - $status_name" . PHP_EOL, FILE_APPEND);
            } catch (Exception $e) {
                file_put_contents('webhook_log_estatus_error.txt', "DB ERROR STATUS: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            }
        }
    }

    // Verificar si hay mensajes
    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];

        // Extraer datos básicos
        $from        = $message['from'] ?? '';
        $message_id  = $message['id'] ?? '';
        $type        = $message['type'] ?? '';
        $raw_json    = $input;

        // Determinar el contenido según tipo de mensaje
        $body = '';
        switch($type) {
            case 'text':
                $body = $message['text']['body'];
                break;

            case 'image':
                $body = '[IMAGE] ID: ' . ($message['image']['id'] ?? '');
                break;

            case 'audio':
                $body = '[AUDIO] ID: ' . ($message['audio']['id'] ?? '');
                break;

            case 'document':
                $body = '[DOCUMENT] ID: ' . ($message['document']['id'] ?? '');
                break;

            default:
                $body = '[UNSUPPORTED TYPE] ' . $type;
                break;
        }

        // Validar datos mínimos antes de insertar
        if (!empty($from) && !empty($message_id)) {

            // Escapar valores para evitar SQL injection y errores de caracteres
            $date = date("Y-m-d H:i:s");
            // Sentencia SQL directa
            $sql = "INSERT INTO waba_callbacks (datelog, sender_phone, message_id, message_text, raw_json,source) 
            VALUES ('$date', '$from', '$message_id', '$body', '$raw_json','callback_user')";
            try {
                // Insertar en base de datos
                $db->sqlPure($sql, false);

                // Log adicional para confirmar inserción
                // file_put_contents('webhook_logok.txt', "INSERT OK: $from - $body" . PHP_EOL, FILE_APPEND);
            } catch (Exception $e) {
                file_put_contents('webhook_message_error.txt', "DB ERROR: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents('webhook_message_else.txt', "Datos incompletos: " . print_r($message, true) . PHP_EOL, FILE_APPEND);
        }
    }

    http_response_code(200);
    echo "EVENT_RECEIVED";
    exit;
}

// Si no es GET ni POST
http_response_code(404);
echo "Not Found";
?>
