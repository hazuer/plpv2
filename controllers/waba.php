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
$token = "EAAYTcZCLS2AABPQAZAjzSVPZBZAUCSSgqZAZCXODZAyFySyAN10SIL6BluwAW2VvvFrmPJIixawChknpxW9P5bjXeDC4ZBsAvhpxQIqi4mCRZAPM3ir1E3vOecLZCFhhHN6tQCaiZBYKHoOWR6orZAGgN20n017ZCXvpkVAfW4pGg4HIu1AqpfVEO8tkW04pVYMpk2wHmco3fHHp3kkM1ljZCPyyADoVKn6dFfwhRsZCuiq";
$sender_phone='5217344093961';
switch ($_POST['option']) {
	case 'sendTemplate':
		$result   = [];
		$success  = 'false';
		$dataJson = [];
		$message  = 'Error al enviar los mensajes';

		$id_location   = $_POST['id_location'];
		$id_estatus=$_POST['mBEstatus'];
		$mbIdCatParcel=$_POST['mbIdCatParcel'];
		$field2=$_POST['field2'];
		$field3=$_POST['field3'];
		$field4=$_POST['field4'];
		$field5=$_POST['field5'];
		$field6=$_POST['field6'];

		$idParceIn   = ($mbIdCatParcel==99) ? '1,2,3': $mbIdCatParcel;
		$number = $_POST['number']; // Solo un número

		$n_user_id=$_SESSION["uId"];


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
			GROUP BY cc.phone";
			$rst = $db->select($sql);

			//var_dump($rst);
			
			if(count($rst)>0){
			$ids               = $rst[0] ? $rst[0]['ids'] : 0;
			$id_contact_type = $rst[0] ? $rst[0]['id_contact_type'] : 0;
			$folioGuias    = $rst[0] ? $rst[0]['folioGuias'] : 0;
			#$fullMessage   = "{message}";
			$sid = "";
			$totalRegistros=count($folioGuias);
			$tguias="Total:".$totalRegistros.", Folio y guías ".$folioGuias;
				
				/*let registros = folioGuias ? folioGuias.split('\n').filter(Boolean) : [];
				let totalRegistros = registros.length;
				fullMessage = `${message} \n*Total:${totalRegistros}*\n*(Folio)-Guía:*\n${folioGuias}`;
	
				let newStatusPackage = 1;
				let id_contact_type  = 3;
				let logWhats      = null;

				if(idContactType==2){ //WhatsApp
					const chatId = "521"+number+ "@c.us";
					let rst = await sendMessageWhats(client, chatId, fullMessage, iconBot);
					sid = `${rst.descMsj} ::Whatsapp Registrado`;
					logWhats = rst.details;
					newStatusPackage = rst.status ? (id_estatus == 5 ? 5 : 2) : 6;
					id_contact_type  = 2;
				}else{
					const number_details = await client.getNumberId(number); // get mobile number details
					if (number_details) {
						let rst = await sendMessageWhats(client, number_details._serialized, fullMessage,iconBot);
						sid =`${rst.descMsj}`;
						logWhats = rst.details;
						newStatusPackage = rst.status ? (id_estatus == 5 ? 5 : 2) : 6;
						// if(ids!=0){
							const lastMessage = moment().tz("America/Mexico_City").format("YYYY-MM-DD HH:mm:ss");
							const sqlUpdateTypeContact = `UPDATE cat_contact 
							SET id_contact_type=2, lastMessage='${lastMessage}'
							WHERE id_location=${id_location} AND phone='${number}' AND id_contact_type=1`
							await db.processDBQueryUsingPool(sqlUpdateTypeContact)
						// }
					} else {
						sid = `${number}, Número de móvil no registrado`
						logWhats = sid;
						newStatusPackage = 6
					}
					//if (i < numbers.length - 1) {
						//await sleep(1000); // tiempo de espera en segundos entre cada envío
					//}
				}*/

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
									["type" => "text", "text" => $number], //nombre
									["type" => "text", "text" => $field2], //ubicacion
									["type" => "text", "text" => $field3], // hora hoy
									["type" => "text", "text" => $field4], // hora mañana
									["type" => "text", "text" => $field5], //fecha dev
									["type" => "text", "text" => $field6], // hora dev
									["type" => "text", "text" => $tguias] //guias
								]
							]
						]
					]
				];

				$url = "https://graph.facebook.com/v23.0/683077594899877/messages";

				$url = "https://graph.facebook.com/v23.0/683077594899877/messages";
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					"Authorization: Bearer $token",
					"Content-Type: application/json"
				]);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

				// Ejecutar petición
				$response = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				// Decodificar respuesta
				$fullLog = json_encode(json_decode($response, true), JSON_UNESCAPED_UNICODE);


				$newStatusPackage=1;
				// Verificar éxito o error
				if ($httpCode >= 200 && $httpCode < 300) {
					$newStatusPackage =2;
				} else {
					$newStatusPackage=6;
				}


				$listIds = explode(",", $ids);
				$nDate = date('Y-m-d H:I:s'); 

				#let fullLog=`${sid}, ${logWhats}`;
				foreach ($listIds as $id_package) {
					#const id_package = listIds[i];
					$sqlSaveNotification = "INSERT INTO notification 
					(id_location,n_date,n_user_id,message,id_contact_type,sid,id_package) 
					VALUES 
					($id_location,'$nDate',$n_user_id,'$tguias',$id_contact_type,'$fullLog',$id_package)";
					 $db->sqlPure($sqlSaveNotification, false);

					$sqlLogger = "INSERT INTO logger 
					(datelog, id_package, id_user, new_id_status, old_id_status, desc_mov) 
					VALUES 
					('$nDate', $id_package, $n_user_id, $newStatusPackage, $id_estatus, 'Envío de Mensaje WABA')";
					$db->sqlPure($sqlLogger, false);

					$sqlUpdatePackage = "UPDATE package SET 
					n_date = '$nDate', n_user_id = '$n_user_id', id_status=$newStatusPackage 
					WHERE id_package IN ($id_package)";
					$db->sqlPure($sqlUpdatePackage, false);

					$dataLog = [
						'id_log'       => null,
						'datelog'      => date("Y-m-d H:i:s"),
						'sender_phone' => $sender_phone,
						'message_id'   => $id_package,
						'message_text' => $tguias,
						'raw_json'     => $id_package
					];
					$db->insert('waba_callbacks', $dataLog);
				}
			
			}
		//}
		    echo json_encode(['success' => true, 'message' => "Mensaje enviado a $number"]);

	break;

	case 'getAllMessagesToRead':
		$phone = $_POST['phone'] ?? '';
		//$waba_number = '7344093961';

		if (!$phone) {
			echo json_encode([]);
			exit;
		}

		$sql = "SELECT message_text, datelog, sender_phone
			FROM waba_callbacks
			WHERE sender_phone = '$phone'
			ORDER BY datelog ASC";
			$mensajes = $db->select($sql);

		// Marcar como leídos
		$sql = "UPDATE waba_callbacks 
				SET is_read = 1, 
					read_at = '" . date("Y-m-d H:i:s") . "', 
					read_by = " . intval($_SESSION['uId']) . " 
				WHERE sender_phone = '" .$phone . "' AND is_read = 0";
		#TODO$db->sqlPure($sql, false);

		echo json_encode($mensajes);
		break;
		
		case 'sendMessage':
			$tophone = $_POST['tophone'] ?? '';
    		$msj = $_POST['msj'] ?? '';

			if (empty($tophone) || empty($msj)) {
				echo json_encode(['success' => false, 'message' => 'Faltan datos.']);
				exit;
			}

			$url = "https://graph.facebook.com/v19.0/683077594899877/messages";

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
				"Authorization: Bearer $token",
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
			if (isset($decoded['messages'])) {
					$dataLog = [
					'id_log'       => null,
					'datelog'      => date("Y-m-d H:i:s"),
					'sender_phone' => $sender_phone,
					'message_id'   => 0,
					'message_text' => $msj,
					'raw_json'     => $decoded
				];
				$db->insert('waba_callbacks', $dataLog);
				echo json_encode(['success' => true, 'data' => $decoded]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Error al enviar mensaje', 'response' => $decoded]);
			}
	break;
}