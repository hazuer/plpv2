<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DBW.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
require_once('includes/session.php');
$id_location = $_SESSION['uLocation'];
$sql ="SELECT 
    sender_phone,
    MAX(datelog) AS last_date,
    SUBSTRING_INDEX(
        GROUP_CONCAT(message_text ORDER BY datelog DESC SEPARATOR '|'),
        '|',
        1
    ) AS last_message 
FROM 
    waba_callbacks 
WHERE 
    is_read = 0 
GROUP BY 
    sender_phone 
ORDER BY 
    last_date DESC LIMIT 5;";
$chats = $db->select($sql);
#var_dump($chats);
?>
<!DOCTYPE html>
<html lang="es-MX">
      <head>
        <?php include_once('head.php');?>

      <script src="<?php echo BASE_URL;?>/assets/js/whatsapp.js?version=<?php echo time(); ?>"></script>
		 <style>
		.chat-container {
			background: #ece5dd;
			padding: 15px;
			height: 400px;
			overflow-y: auto;
			display: flex;
			flex-direction: column;
		}

		.chat-bubble {
			max-width: 70%;
			padding: 10px 15px;
			border-radius: 15px;
			margin: 5px 0;
			position: relative;
			font-size: 14px;
		}

		.chat-bubble.sent {
			background: #dcf8c6;
			align-self: flex-end;
			border-bottom-right-radius: 0;
		}

		.chat-bubble.received {
			background: #fff;
			align-self: flex-start;
			border-bottom-left-radius: 0;
		}

		.chat-bubble .time {
			display: block;
			font-size: 11px;
			color: #888;
			margin-top: 5px;
			text-align: right;
		}

		

		/* Que las columnas tengan mismo alto */
.row.column4.graph {
    display: flex;
    flex-wrap: nowrap;
}

/* Que la sección de chat se comporte como columna */
.chat-wrapper {
    display: flex;
    flex-direction: column;
}

/* Contenedor del chat */
.chat-container {
    background: #ece5dd;
    padding: 15px;
    height: 400px; /* Ajustable */
    overflow-y: auto;
    flex-grow: 1; /* Ocupar todo el espacio disponible */
    display: flex;
    flex-direction: column;
}

/* Barra inferior para enviar mensaje */
.chat-input-area {
    display: flex;
    align-items: center;
    border-top: 1px solid #ccc;
    background: #fff;
    padding: 10px;
}

.chat-input {
    flex-grow: 1;
    border: none;
    padding: 10px;
    font-size: 14px;
    outline: none;
}

.btn-send {
    background: #25d366; /* Verde estilo WhatsApp */
    border: none;
    padding: 10px 15px;
    margin-left: 10px;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.btn-send i {
    font-size: 18px;
}

.chat-item {
    cursor: pointer;          /* Manita */
    transition: background 0.2s; /* Animación suave */
}

.chat-item:hover {
    background-color: #cce5ff; /* Color de fondo al pasar el mouse */
}

.msg_list_main {
    max-height: 433px; /* Ajusta según el diseño */
    overflow-y: auto;
    overflow-x: hidden; /* evita scroll horizontal */
    padding-right: 5px; /* para que no se corte el contenido */
}

/* Opcional: estilo para la barra de scroll */
.msg_list_main::-webkit-scrollbar {
    width: 6px;
}

.msg_list_main::-webkit-scrollbar-thumb {
    background-color: #bbb;
    border-radius: 4px;
}

.chat-bubble {
    max-width: 70%;
    padding: 10px;
    margin: 5px;
    border-radius: 8px;
    display: inline-block;
    position: relative;
}
.sent {
    background-color: #dcf8c6;
    text-align: right;
    margin-left: auto;
}
.received {
    background-color: #fff;
    text-align: left;
    margin-right: auto;
    border: 1px solid #ccc;
}
.time {
    font-size: 10px;
    color: #888;
    display: block;
    text-align: right;
}

		</style>
      </head>
	<body class="dashboard dashboard_1">
      <div class="full_container">
         <div class="inner_container">
            <!-- Sidebar  -->
                <?php include_once('sidebar.php');?>
            <!-- end sidebar -->
            <!-- right content -->
            <div id="content">
               <!-- topbar -->
               <?php include_once('topbar.php');?>
               <!-- end topbar -->
               <!-- dashboard inner -->
               <div class="midde_cont">
                  <div class="container-fluid">
                     <div class="row column_title">
                        <div class="col-md-12">
                           <div class="page_title">
                              <h2>Mensajes nuevos <?php echo $id_location;?></h2>
                           </div>
                        </div>
                     </div>
                  </div>
				  <div class="row column4 graph">
                        <div class="col-md-4">
                           <div class="white_shd full margin_bottom_30" style="display: block !important;">
                              <div class="full graph_head">
                                 <div class="heading1 margin_0">
                                    <h2>Todos</h2>
                                 </div>
                              </div>
                              <div class="full progress_bar_inner">
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="msg_section">
                                          <div class="msg_list_main">
                                          <ul class="msg_list" id="chat-list">
                                             <?php foreach ($chats as $chat): 
                                             $numero = substr($chat['sender_phone'], 3);
                                               /*$sqlGetContac="SELECT 
                                                    c.id_location
                                                    FROM cat_contact c 
                                                    WHERE 
                                                    c.id_location IN ($id_location) 
                                                    AND c.phone IN('$numero')
                                                    AND c.id_contact_status IN (1)
                                                    ORDER BY c.c_date DESC LIMIT 1";
                                                    $rstCheck = $db->select($sqlGetContac);
    			                                       $contact_name = $rstCheck[0]['contact_name'] ?? 0;
                                                    $locId = $rstCheck[0]['id_location'] ?? 0;
                                                    // $ubicacion = ($locId==1)? 'Tlaquiltenango':' Zacatepec';*/
                                                   $numero = substr($chat['sender_phone'], 3); ?>
                                                   <li class="chat-item" data-phone="<?php echo $chat['sender_phone']; ?>">
                                                      <span>
                                                         <span class="name_user"><?php echo $numero; ?></span>
                                                         <span class="msg_user"><?php echo htmlspecialchars($chat['last_message']); ?></span>
                                                         <span class="time_ago"><?php echo date("H:i", strtotime($chat['last_date'])); ?></span>
                                                      </span>
                                                   </li>
                                             <?php endforeach; ?>
                                          </ul>
                                       </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                  
						<!-- --------------- -->
                  <div class="col-md-8 chat-wrapper">
                     <div class="white_shd full margin_bottom_30" style="display: block !important;">
                        <div class="full graph_head">
                           <div class="heading1 margin_0">
                              <h2><input type="text" id="tophone" value="" readonly></h2>
                           </div>
                        </div>

                        <!-- Contenedor conversación -->
                        <div class="full progress_bar_inner chat-container" id="chat-container">
                           <p style="text-align:center;color:#777;">Selecciona un chat para ver los mensajes.</p>
                        </div>
                        <!-- Barra para escribir mensaje -->
                        <div class="chat-input-area">
                           <input type="text" class="chat-input" id="chat-input" placeholder="Escribe un mensaje...">
                           <button class="btn-send" id="btn-send"><i class="fa fa-paper-plane"></i></button>
                        </div>
                     </div>
                  </div>
						<!-- --------------- -->
						
                     </div>
               </div>
               <!-- end dashboard inner -->
            </div>
         </div>
      </div>
      <?php
         require_once('modal/waba-template.php');
      	require_once('footer.php');
      ?>
   </body>
</html>