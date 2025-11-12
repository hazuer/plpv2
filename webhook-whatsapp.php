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

    $sqlLocationInfo ="SELECT * FROM cat_location WHERE id_location IN(1)";
    $infoLocation = $db->select($sqlLocationInfo);
    $token = $infoLocation[0]['token'];

    // Capturar el JSON crudo
    $input = file_get_contents('php://input');

    // Log para depuración
    #file_put_contents(date('Y-m-d').'general', "[" . date('Y-m-d H:i:s') . "] " . $input . PHP_EOL, FILE_APPEND);

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

            // Cambio de estatus
            $n_user_id = 1;
            $nDate     = date('Y-m-d H:i:s'); // corregido 'i' minúscula
            $newStatusPackage = 6;
            if ($status_name == 'failed') {
                $sqlPackages="SELECT 
                    p.id_package 
                FROM 
                    package p 
                INNER JOIN notification n ON n.id_package = p.id_package 
                WHERE 1 
                    AND n.message_id LIKE 'wamid%' 
                    AND n.message_id IN ('".$message_id."') 
                ORDER BY p.id_package ASC";
                #file_put_contents('sql.txt', $sqlPackages . PHP_EOL, FILE_APPEND);

                $dtsPks = $db->select($sqlPackages);
                $idsPks = array_column($dtsPks, "id_package");

                if (!empty($idsPks)) {

                    foreach ($dtsPks as $row) {
                        $id_package = $row['id_package'];

                        $sqlGetCurrentStatus="SELECT id_status old_id_status FROM package WHERE id_package IN ($id_package)";
                        $records           = $db->select($sqlGetCurrentStatus);
                        $id_status_current = $records[0]['old_id_status'];

                        $sqlLogger = "INSERT INTO logger 
                        (datelog, id_package, id_user, new_id_status, old_id_status, desc_mov) 
                        VALUES 
                        ('$nDate', $id_package, $n_user_id, $newStatusPackage, $id_status_current, 'Error al enviar mensaje Meta Waba, ".$message_id."')";
                        $db->sqlPure($sqlLogger, false);
				    }

                    $listIdsP = implode(", ", $idsPks);
                    $sqlUpdatePackage = "UPDATE package SET 
                        n_date = '$nDate', n_user_id = '$n_user_id', id_status=$newStatusPackage 
                        WHERE id_package IN ($listIdsP)";
                    $db->sqlPure($sqlUpdatePackage, false);

                    // Guardar en log
                    #$logMsg = "[$nDate] Status: $status_name | User: $n_user_id | MsgID: $message_id | Packages: $listIdsP\n";
                    #file_put_contents('webhook_log_estatus_error.txt', $logMsg . PHP_EOL, FILE_APPEND);
                } else {
                    // Si no hay paquetes también logueamos
                    #$logMsg = "[$nDate] Status: $status_name | User: $n_user_id | MsgID: $message_id | No se encontraron paquetes\n";
                    #file_put_contents('webhook_log_estatus_error.txt', $logMsg . PHP_EOL, FILE_APPEND);
                }
            }
            //update type contact
            if ($status_name == 'read' || $status_name =='delivered') {
                $phone = substr($recipient, 3);
                $sqlUpdateTypeContact="UPDATE cat_contact 
                    SET id_contact_type=2, lastMessage='".$nDate."'
                    WHERE  phone='".$phone."' AND id_contact_type=1";
                $db->sqlPure($sqlUpdateTypeContact, false);
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
                $media_id = $message['image']['id'];
                $mediaInfo = getMediaUrl($media_id, $token);
                if (isset($mediaInfo['url'])) {
                    $savePath = "meta/image/".$media_id.".jpg";
                    downloadMedia($mediaInfo['url'], $token, $savePath);
                    $body = "[IMAGE SAVED] $savePath";
                } else {
                    $body = "[IMAGE] ID: ".$media_id;
                }
                break;

            case 'audio':
                $media_id = $message['audio']['id'];
                $mediaInfo = getMediaUrl($media_id, $token);
                if (isset($mediaInfo['url'])) {
                    $savePath = "meta/audio/".$media_id.".ogg";
                    downloadMedia($mediaInfo['url'], $token, $savePath);
                    $body = "[AUDIO SAVED] $savePath";
                }
                break;

            case 'document':
                $media_id = $message['document']['id'];
                $filename = $message['document']['filename'] ?? ($media_id.".pdf");
                $mediaInfo = getMediaUrl($media_id, $token);
                if (isset($mediaInfo['url'])) {
                    $savePath = "meta/document/".$filename;
                    downloadMedia($mediaInfo['url'], $ACCESS_TOKEN, $savePath);
                    $body = "[DOCUMENT SAVED] $savePath";
                }
                break;

                case 'reaction':
                $reaction = $message['reaction'] ?? [];
                $emoji = $reaction['emoji'] ?? '';
                $msgReactedId = $reaction['message_id'] ?? '';

                if (!empty($emoji) && !empty($msgReactedId)) {
                    $body = "[REACTION] $emoji en mensaje $msgReactedId";
                } else {
                    $body = "[REACTION] sin datos";
                }
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

function getMediaUrl($media_id, $access_token) {
    $url = "https://graph.facebook.com/v21.0/$media_id";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function downloadMedia($file_url, $access_token, $save_path) {
    $ch = curl_init($file_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);

    file_put_contents($save_path, $data);
}


// Si no es GET ni POST
http_response_code(404);
echo "Not Found";
?>
