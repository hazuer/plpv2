<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Cambia el límite de ejecución a 600 segundos (10 minutos)
ini_set('max_execution_time', 800);
set_time_limit(800);
ini_set('memory_limit', '512M');

define( '_VALID_MOS', 1 );
date_default_timezone_set('America/Mexico_City');

require_once('../includes/configuration.php');
require_once('../includes/DBW.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);

header('Content-Type: application/json; charset=utf-8');

$wabaPhone='5217344093961';
switch ($_POST['option']) {
	case 'sendTemplate':
		$result   = [];
		$success  = 'false';
		$dataJson = [];
		$message  = 'Error al enviar el mensaje';

		$id_location   = $_POST['id_location'];
		$id_estatus    = $_POST['mBEstatus'];
		$mbIdCatParcel = $_POST['mbIdCatParcel'];
		$field2        = $_POST['field2'];
		$field3        = $_POST['field3'];
		$field4        = $_POST['field4'];
		$field5        = $_POST['field5'];
		$field6        = $_POST['field6'];
		$txtTemplate   = $_POST['txtTemplate'];
		$tokenWaba     = $_POST['tokenWaba'];

		$idParceIn = ($mbIdCatParcel==99) ? '1,2,3': $mbIdCatParcel;
		$number    = $_POST['number']; // Solo un número
		$n_user_id = $_SESSION["uId"];

		$sql ="SELECT 
			cc.phone,
			cc.id_contact_type,
			GROUP_CONCAT(p.id_package) AS ids,
			GROUP_CONCAT('(',p.folio,')-',p.tracking,'' SEPARATOR '\n') AS folioGuias 
			FROM package p 
			INNER JOIN cat_contact cc ON cc.id_contact=p.id_contact 
			WHERE 
			p.id_location IN (".$id_location.") 
			AND p.id_status IN (".$id_estatus.") 
			AND cc.phone IN(".$number.") 
			AND p.id_cat_parcel IN(".$idParceIn.") 
			GROUP BY cc.phone
		";
		$rst = $db->select($sql);
		//var_dump($rst);

		if(count($rst)>0){
			$ids             = $rst[0] ? $rst[0]['ids'] : 0;
			$id_contact_type = $rst[0] ? $rst[0]['id_contact_type'] : 0;
			$folioGuias      = $rst[0] ? $rst[0]['folioGuias'] : 0;

			$sqlGetName="SELECT c.contact_name 
			FROM cat_contact c 
				WHERE 
				c.id_location IN ($id_location) 
				AND c.phone IN('$number') 
				AND c.id_contact_status IN(1)
				ORDER BY c.c_date DESC LIMIT 2";
			$contacts       = $db->select($sqlGetName);
			$customNameUser = $number;
			if(count($contacts)==1){
				$customNameUser = $contacts[0]['contact_name'];
			}

			$idsArray       = explode(',', $ids);
			$totalRegistros = count($idsArray);
			$tguias         = "Total:".$totalRegistros." (Folio)-Guía: ".$folioGuias;

			$fullTemplate = str_replace("usuario_db,", $customNameUser, $txtTemplate);
			$fullTemplate = str_replace("folios_db", $tguias, $fullTemplate);
			//var_dump($fullTemplate);

			$data = [
				"messaging_product" => "whatsapp",
				"to" => "521".$number,
				"type" => "template",
				"template" => [
					"name" => "order_pick_up_jt_im",
					"language" => ["code" => "es"],
					"components" => [
						[
							"type" => "body",
							"parameters" => [
								["type" => "text", "text" => $customNameUser], //nombre
								["type" => "text", "text" => $field2],         //ubicacion fisica y maps
								["type" => "text", "text" => $field3],         // hora hoy
								["type" => "text", "text" => $field4],         // hora mañana
								["type" => "text", "text" => $field5],         //fecha dev
								["type" => "text", "text" => $field6],         // hora dev
								["type" => "text", "text" => $tguias]          //guias
							]
						]
					]
				]
			];

			$url = "https://graph.facebook.com/v23.0/683077594899877/messages";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Bearer $tokenWaba",
				"Content-Type: application/json"
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

			// Ejecutar petición
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			// JSON de respuesta
			$fullLog    = json_encode(json_decode($response, true), JSON_UNESCAPED_UNICODE);
			$decoded    = json_decode($response, true);
			$message_id = $decoded['messages'][0]['id'] ?? 0; // devuelve null si no existe

			$newStatusPackage = 1;
			if ($httpCode >= 200 && $httpCode < 300) {
				//var_dump('Okas');
				$success  = 'true';
				$message  = "Mensaje enviado a $number";
				$newStatusPackage = ($id_estatus == 5 ? 5 : 2);
			} else {
				//var_dump('false');
				$success  = 'false';
				$message  = 'Error al enviar el mensaje';
				$newStatusPackage = 6;
			}

			$listIds = explode(",", $ids);
			$nDate   = date('Y-m-d H:I:s');
			foreach ($listIds as $id_package) {
			//var_dump('id_package',$id_package);
				$sqlSaveNotification = "INSERT INTO notification 
				(id_location,n_date,n_user_id,message,id_contact_type,sid,id_package,message_id) 
				VALUES 
				($id_location,'$nDate',$n_user_id,'$fullTemplate',$id_contact_type,'$fullLog',$id_package,'$message_id')";
				$db->sqlPure($sqlSaveNotification, false);

				$sqlLogger = "INSERT INTO logger 
				(datelog, id_package, id_user, new_id_status, old_id_status, desc_mov) 
				VALUES 
				('$nDate', $id_package, $n_user_id, $newStatusPackage, $id_estatus, 'Envío de Mensaje WABA, '".$message_id.")";
				$db->sqlPure($sqlLogger, false);

				$sqlUpdatePackage = "UPDATE package SET 
				n_date = '$nDate', n_user_id = '$n_user_id', id_status=$newStatusPackage 
				WHERE id_package IN ($id_package)";
				$db->sqlPure($sqlUpdatePackage, false);

				$date    = date("Y-m-d H:i:s");
				$read_by = intval($_SESSION['uId']);
				$sql = "INSERT INTO waba_callbacks (datelog, sender_phone, message_id, message_text, raw_json,is_read,read_at,read_by,source) 
				VALUES ('$date', '$wabaPhone', '$message_id', '$fullTemplate', '$response',1,'$date', $read_by, 'template')";
				$inserted = $db->sqlPure($sql, false);
			}
		}else{
			$success  = 'false';
			$message  = "Número $number sin guías para procesar, mensaje no enviado";
		}
		echo json_encode(['success' => $success, 'message' => $message]);
	break;

	case 'getAllMessagesToRead':
		$phone = $_POST['phone'] ?? '';

		if (!$phone) {
			echo json_encode([]);
			exit;
		}

		$sql = "SELECT 
			id_log,
			datelog,
			sender_phone,
			message_text,
			CASE 
				WHEN sender_phone = '".$wabaPhone."' THEN 'outgoing' 
				ELSE 'incoming' 
			END AS message_type 
			FROM waba_callbacks 
			WHERE 
			(sender_phone = '".$wabaPhone."' AND raw_json LIKE '%".$phone."%') 
			OR 
			(sender_phone = '".$phone."') 
			ORDER BY datelog ASC
		";
		$mensajes = $db->select($sql);

		echo json_encode($mensajes);
		break;

	case 'markAsRead':
		$tophone    = $_POST['tophone'] ?? '';

		if (!$tophone) {
			echo json_encode([]);
			exit;
		}
		// Marcar como leídos
		$sql = "UPDATE waba_callbacks 
		SET is_read = 1, 
			read_at = '" . date("Y-m-d H:i:s") . "', 
			read_by = " . intval($_SESSION['uId']) . " 
		WHERE sender_phone = '" .$tophone . "' AND is_read = 0";
		$rst = $db->sqlPure($sql, false);
		echo json_encode(['success' => true, 'data' => $rst]);
	break;
		
	case 'sendMessage':
		$tophone    = $_POST['tophone'] ?? '';
		$msj        = $_POST['msj'] ?? '';
		$tokenWaba  = $_POST['tokenWaba'];

		if (empty($tophone) || empty($msj)) {
			echo json_encode(['success' => false, 'message' => 'Faltan datos.']);
			exit;
		}

		$url = "https://graph.facebook.com/v23.0/683077594899877/messages";

		$payload = [
			"messaging_product" => "whatsapp",
			"to" => $tophone,
			"type" => "text",
			"text" => [
				"body" => $msj
			]
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $tokenWaba",
			"Content-Type: application/json"
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($error) {
			echo json_encode(['success' => false, 'message' => "cURL Error: $error"]);
			exit;
		}

		$decoded = json_decode($response, true);

		if (isset($decoded['messages'][0]['id'])) {
			$message_id = $decoded['messages'][0]['id']; // ID que regresa la API
			$date    = date("Y-m-d H:i:s");
			$read_by = intval($_SESSION['uId']);
			$sql = "INSERT INTO waba_callbacks (datelog, sender_phone, message_id, message_text, raw_json,is_read,read_at,read_by,source) 
            VALUES ('$date', '$wabaPhone', '$message_id', '$msj', '$response',1,'$date', $read_by,'waba_response_to_user')";
			$inserted = $db->sqlPure($sql, false);

			if ($inserted) {
				echo json_encode(['success' => true, 'data' => $decoded]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Error al guardar en DB']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Error al enviar mensaje', 'response' => $decoded]);
		}

	break;
}