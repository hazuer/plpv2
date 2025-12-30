
$FLOW = [
    // paso inicial (se envía desde procesarRespuesta)
    'inicio' => [
        'message_variant' => [
            'first' => "Hola 👋\n¿En qué te podemos ayudar hoy?",
            'again' => "¿En qué más te podemos ayudar hoy?"
        ],
        'buttons' => [
            ['id' => 'btn_horario', 'title'   => '🕔 Horario'],
            ['id' => 'btn_ubicacion', 'title' => '📍Ubicación'],
            ['id' => 'btn_no', 'title'        => 'Todo bien, gracias']
        ]
    ],

    // segunda capa
    'horario' => [
        'message' => "Con gusto lo atendemos de 9:00 AM a 5:00 PM. Puede pasar por su paquete cuando guste dentro de ese horario.\n¿Necesitas ayuda con algo más?",
        'buttons' => [
            ['id' => 'btn_ubicacion', 'title' => '📍Ubicación 📍'],
            ['id' => 'btn_no',   'title' => 'Nada más, gracias'],
        ]
    ],

    'ubicacion' => [
        'message' => "Estamos ubicados en Tlaquiltenango, Morelos.\n ¿Quieres revisar algo más?",
        'buttons' => [
            ['id' => 'btn_horario', 'title'   => '🕔 Horario 🕔'],
            ['id' => 'btn_no',   'title' => 'Nada más, gracias'],
        ]
    ],
    // cierre
    'cerrar' => [
        'message' => "Perfecto 😊. Me alegra haber ayudado. Si necesitas algo más, aquí estaré.",
        'buttons' => []
    ]
];

function procesarRespuesta($from,$token,$wabaPhone,$phoneNumberId,$infoLocation){
    global $db;
    global $FLOW;
    dd("------------- P.  R E S P U E S T A -------------");
    dd("infoLocation: ".json_encode($infoLocation));
    date_default_timezone_set('America/Mexico_City');
    //Determinar si hay ventana de 24 hrs desde la primer plantilla de contacto
    $sql = "SELECT datelog,source 
    FROM waba_callbacks 
    WHERE 1 
    AND (raw_json LIKE '%".$from."%') 
    AND (source = 'template') 
    ORDER BY id_log DESC 
    LIMIT 1";
    dd("SQL last template: ".$sql);

    $result = $db->select($sql);
    $t      = count($result);
    if($t>0){
        $dateNow   = date("Y-m-d H:i:s");
        $datelog   = $result[0]['datelog'];

        $aun_no_24hrs = (strtotime($dateNow) - strtotime($datelog)) < 86400;
        
        dd("dateNow: ".$dateNow);
        dd("datelog: ".$datelog);
        dd("valor aun_no_24hrs: ".$aun_no_24hrs);
        #TODO::if ($aun_no_24hrs) {
            dd("Puedes responder no han pasado 24 horas");
               // SESSION HANDLING
                $session = getSession($from);

                if (!$session) {
                    // Aún no creamos sesión aquí
                    dd("No hay sesión previa");
                } else {
                    $session = resetSessionIfNewDay($session);
                }

                dd("sessionx: ".json_encode($session));

                // decidir mensaje inicial (first vs again)
               $today = date('Y-m-d');
                $stepDef = $FLOW['inicio'];

                if ($session && $session['first_message_sent'] === $today) {
                    // YA SALUDAMOS HOY
                    $mensajeInicio = $stepDef['message_variant']['again'];
                    dd("again");
                } else {
                    // PRIMER MENSAJE DEL DÍA
                    $mensajeInicio = $stepDef['message_variant']['first'];
                    dd("first");

                    if (!$session) {
                        createSession($from, 'inicio');
                    }

                    // Asignar hoy
                    $sqlUpdate = "UPDATE waba_session_state 
                                SET first_message_sent = '$today'
                                WHERE phone = '".addslashes($from)."'";
                    $db->sqlPure($sqlUpdate, false);
                    $session = getSession($from);
                }

                // enviar interactive usando la nueva función
                sentInteractiveButtons($from, $mensajeInicio, $stepDef['buttons'], $token, $wabaPhone, $phoneNumberId);

                // actualizar session last_step = 'inicio'
                updateSession($from, 'inicio');
        #TODO::}else{
        #TODO::    dd("Ya pasaron 24hrs desde el envio de plantilla, bot no activo");
        #TODO::}
    }
}

//-----------------------------------------
//-----------------------------------------
// Devuelve sesión o false
function getSession($phone) {
    global $db;
    $sql = "SELECT * FROM waba_session_state WHERE phone = '".addslashes($phone)."' LIMIT 1";
    $res = $db->select($sql);
    return (count($res) > 0) ? $res[0] : false;
}

function createSession($phone, $first_step = 'inicio') {
    global $db;
    $now   = date('Y-m-d H:i:s');
    $sql = "INSERT INTO waba_session_state 
            (phone, last_step, last_interaction, first_message_sent, created_at, updated_at) 
            VALUES ('".addslashes($phone)."','".addslashes($first_step)."','$now',null,'$now','$now')";
            dd("create session:".$sql);
    $db->sqlPure($sql, false);
}

function updateSession($phone, $step) {
    global $db;
    $now = date('Y-m-d H:i:s');
    $sql = "UPDATE waba_session_state SET last_step = '".addslashes($step)."', last_interaction = '$now', updated_at = '$now'
            WHERE phone = '".addslashes($phone)."'";
    $db->sqlPure($sql, false);
}

function resetSessionIfNewDay($session) {
    global $db;
    if (!$session) return false;

    $today = date('Y-m-d');
    $last = substr($session['last_interaction'], 0, 10);

    // Solo resetea si es OTRO día
    if ($last !== $today) {

        $sql = "UPDATE waba_session_state 
                SET last_step = 'inicio', 
                    last_interaction = '".date('Y-m-d H:i:s')."', 
                    first_message_sent = null,
                    updated_at = '".date('Y-m-d H:i:s')."'
                WHERE phone = '".addslashes($session['phone'])."'";

        $db->sqlPure($sql, false);

        return getSession($session['phone']);
    }

    return $session;
}
//-----------------------------------------
//-----------------------------------------

function handleButtonReply($from, $buttonId, $token, $wabaPhone, $phoneNumberId) {
    global $db, $FLOW;

    // obtener session
    $session = getSession($from);

    // si no existe session, crear y tratar como first
    if (!$session) {
        createSession($from, 'inicio');
        $session = getSession($from);
    } else {
        $session = resetSessionIfNewDay($session);
    }

    $current = $session['last_step'] ?? 'inicio';

    // lógica de transición basada en current y buttonId
    // mapa simple:
    $transitions = [
        'inicio' => [
            'btn_horario'   => 'horario',
            'btn_ubicacion' => 'ubicacion',
            'btn_no'       => 'cerrar'
        ],
        'horario' => [
            'btn_menu' => 'inicio',
            'btn_no' => 'cerrar',
        ],
        'ubicacion' => [
            'btn_menu' => 'inicio',
            'btn_no' => 'cerrar',
        ]
    ];

    $next = null;
    if (isset($transitions[$current][$buttonId])) {
        $next = $transitions[$current][$buttonId];
    } else {
        // fallback: si no hay transición definida, volver al menú principal
        $next = 'inicio';
    }

    // actualizar session al nuevo paso
    updateSession($from, $next);

    // enviar la respuesta según $next
    if ($next === 'cerrar') {
        // obtener mensaje de cierre
        $msg = $FLOW['cerrar']['message'];
        // enviar texto simple
        enviarMensajeTexto($from, $msg, $token);
        // opcional: marcar session cerrada o eliminarla
        $sql = "DELETE FROM waba_session_state WHERE phone = '".addslashes($from)."'";
        $db->sqlPure($sql,false);
    } else {
        // decidir qué mensaje enviar: si tiene 'message_variant' tratamos first/again
        $stepDef = $FLOW[$next];
        if (isset($stepDef['message'])) {
            $msgToSend = $stepDef['message'];
        } else if (isset($stepDef['message_variant'])) {
            // checar si ya se envió el first_message_sent hoy
            $session = getSession($from);
            $today = date('Y-m-d');
            if ($session['first_message_sent'] === $today && $next === 'inicio') {
                $msgToSend = $stepDef['message_variant']['again'];
            } else {
                $msgToSend = $stepDef['message_variant']['first'];
                // setear first_message_sent si estaba vacío o diferente
                $sql = "UPDATE waba_session_state SET first_message_sent = '$today' WHERE phone = '".addslashes($from)."'";
                $db->sqlPure($sql,false);
            }
        } else {
            $msgToSend = "Ok";
        }

        // finalmente enviar botones del paso
        $buttons = $stepDef['buttons'] ?? [];

        // si hay más de 3 botones y step requiere más, puedes enviar list type (no cubierto aquí).
        sentInteractiveButtons($from, $msgToSend, $buttons, $token, $wabaPhone, $phoneNumberId);
    }
}
////////////////////////



function sentInteractiveButtons($phone, $mensaje, $buttons, $token, $wabaPhone, $phoneNumberId) {
    global $db;
    dd("*********** I.  B U T T O N S***********");
    dd('phone:'.$phone);
    dd('mensaje:'.$mensaje);
    dd('buttons:'.json_encode($buttons, JSON_UNESCAPED_UNICODE));
    dd('token:'.$token);
    dd('wabaPhone:'.$wabaPhone);
    dd('phoneNumberId:'.$phoneNumberId);

    usleep(300000); //TODO

    $url = "https://graph.facebook.com/v23.0/".$phoneNumberId."/messages";
    // construir botones para payload (máx 3 para buttons; si son listas, distinto formato)
    $payloadButtons = [];
    foreach ($buttons as $b) {
        $payloadButtons[] = [
            "type"  => "reply",
            "reply" => [
                "id"    => $b['id'],
                "title" => $b['title']
            ]
        ];
    }

    $payload = [
        "messaging_product" => "whatsapp",
        "to"   => $phone,
        "type" => "interactive",
        "interactive" => [
            "type"   => "button",
            "body"   => ["text"    => $mensaje],
            "action" => ["buttons" => $payloadButtons]
        ]
    ];

    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    dd(":::response sent :::buttons::::".$response);

    // extraer titles e insertar en waba_callbacks como ya haces
    $button_titles = array_map(function($b){ return $b['title']; }, $buttons);
    $concatenado   = $mensaje . (count($button_titles) ? " | " . implode(" | ", $button_titles) : "");

    $decoded = json_decode($response, true);
    if (isset($decoded['messages'][0]['id'])) {
        $message_id = $decoded['messages'][0]['id'];
        $date = date("Y-m-d H:i:s");
        $read_by = 21; 
        $sent_by = 21;
        $sql = "INSERT INTO waba_callbacks (datelog, sender_phone, message_id, message_text, raw_json, is_read, read_at, read_by, source, sent_by)
            VALUES ('$date', '$wabaPhone', '$message_id', '".addslashes($concatenado)."', '".addslashes($response)."', 1, '$date', $read_by, 'bot', $sent_by)";
        $db->sqlPure($sql, false);
    }
}
//////

function enviarMensajeTexto($phone, $msg, $token) {
    $idPhone="683077594899877"; #TODO
    $wabaPhone="5217344093961"; #TODO
    global $db;
    $url = "https://graph.facebook.com/v23.0/".$idPhone."/messages";

    $payload = [
        "messaging_product" => "whatsapp",
        "to" => $phone,
        "type" => "text",
        "text" => [
            "body" => $msg
        ]
    ];

    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    file_put_contents("msg_debug.txt", $response.PHP_EOL, FILE_APPEND);
    $decoded = json_decode($response, true);
    if (isset($decoded['messages'][0]['id'])) {
        $message_id = $decoded['messages'][0]['id']; // ID que regresa la API
        $date    = date("Y-m-d H:i:s");
        $read_by = 21;
        $sent_by = 21;
        $sql = "INSERT INTO waba_callbacks (datelog, sender_phone, message_id, message_text, raw_json,is_read,read_at,read_by,source,sent_by) 
        VALUES ('$date', '$wabaPhone', '$message_id', '$msg', '$response',1,'$date', $read_by,'bot',$sent_by)";
        $inserted = $db->sqlPure($sql, false);
    }
}


handleButtonReply($from, $buttonId, $token, $wabaPhone, $phoneNumberId);



SELECT distinct message_text,source FROM waba_callbacks
WHERE message_text LIKE '%gracias%'
and source in('callback_user')
order by message_text ASC;