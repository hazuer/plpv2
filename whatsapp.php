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
    last_date DESC;";
$chats = $db->select($sql);
#var_dump($chats);

#Template
$sqlLocationInfo ="SELECT * FROM cat_location WHERE id_location IN($id_location)";
$infoLocation = $db->select($sqlLocationInfo);
date_default_timezone_set('America/Mexico_City');
$fechaDev = date("d/m/Y", strtotime("+2 days"));
?>
<!DOCTYPE html>
<html lang="es-MX">
      <head>
        <?php include_once('head.php');?>
         <script src="<?php echo BASE_URL;?>/assets/js/whatsapp.js?version=<?php echo time(); ?>"></script>
         <link rel="stylesheet" href="<?php echo BASE_URL;?>/assets/css/waba.css?version=<?php echo time(); ?>"/>
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
                              <h2>Mensajes nuevos <?php echo $id_location.'-'.$desc_loc;?></h2>
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
                                               $sqlGetContac="SELECT 
                                                    c.id_location,
                                                    c.contact_name 
                                                    FROM cat_contact c 
                                                    WHERE 
                                                    c.id_location IN ($id_location) 
                                                    AND c.phone IN('$numero')
                                                    AND c.id_contact_status IN (1)
                                                    ORDER BY c.c_date DESC LIMIT 1";
                                                    $rstCheck    = $db->select($sqlGetContac);
    			                                       $contact_name = $rstCheck[0]['contact_name'] ?? 0;
                                                    $locId       = $rstCheck[0]['id_location'] ?? 0;
                                                    //$ubicacion = ($locId==1)? 'Tlaquiltenango':' Zacatepec';
                                                    if($locId==$id_location){
                                                   $numero = substr($chat['sender_phone'], 3); ?>
                                                   <li class="chat-item" data-phone="<?php echo $chat['sender_phone']?>">
                                                      <span>
                                                         <span class="name_user"><?php echo $numero.'<br>'.$contact_name; ?></span>
                                                         <span class="msg_user"><?php echo htmlspecialchars($chat['last_message']); ?></span>
                                                         <span class="time_ago"><?php $formatter = new IntlDateFormatter(
                                                                                 'es_ES',
                                                                                 IntlDateFormatter::FULL,
                                                                                 IntlDateFormatter::SHORT,
                                                                                 'America/Mexico_City',
                                                                                 IntlDateFormatter::GREGORIAN,
                                                                                 'EEE, dd MMM, HH:mm'
                                                                              );
                                                                              echo $formatter->format(strtotime($chat['last_date'])); ?></span>
                                                      </span>
                                                   </li>
                                             <?php 
                                                   }
                                          endforeach; ?>
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
                              <h2><input type="text" id="tophone" value="" readonly>
                           <input type="text" id="tokenWaba" value="<?php echo $infoLocation[0]['token']?>" readonly></h2>
                           </div>
                        </div>

                        <!-- Contenedor conversación -->
                        <div class="full progress_bar_inner chat-container" id="chat-container">
                           <p style="text-align:center;color:#777;">Selecciona un chat para ver los mensajes.</p>
                        </div>
                        <!-- Barra para escribir mensaje -->
                        <div class="chat-input-area">
                           <input type="text" class="chat-input" id="chat-input" placeholder="Escribe un mensaje...">
                           <button class="btn-send" id="btn-send" data-toggle="tooltip" data-placement="top" title="" data-original-title="Enviar mensaje"><i class="fa fa-paper-plane"></i></button>
                           <button class="btn-read" id="btn-read" data-toggle="tooltip" data-placement="top" title="" data-original-title="Marcar como leido"><i class="fa fa-check-circle"></i></button>
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