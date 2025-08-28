<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DB.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
require_once('includes/session.php');
$id_location = $_SESSION['uLocation'];

?>
<!DOCTYPE html>
<html lang="es-MX">
      <head>
        <?php include_once('head.php');?>

		<link href="<?php echo BASE_URL;?>/assets/css/libraries/jquery.dataTables.min.css" rel="stylesheet">
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/jquery.dataTables.min.js"></script>

		<link href="<?php echo BASE_URL;?>/assets/css/libraries/buttons.dataTables.min.css" rel="stylesheet">
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/dataTables.buttons.min.js"></script>
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/jszip.min.js"></script>
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/pdfmake.min.js"></script>
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/vfs_fonts.js"></script>
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/buttons.html5.min.js"></script>
		<link type="text/css" href="<?php echo BASE_URL;?>/assets/css/libraries/dataTables.checkboxes.css" rel="stylesheet"/>
		<script type="text/javascript" src="<?php echo BASE_URL;?>/assets/js/libraries/dataTables.checkboxes.min.js"></script>

         <script src="<?php echo BASE_URL;?>/assets/js/contacts.js?version=<?php echo time(); ?>"></script>
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
                              <h2>Mensajes nuevos</h2>
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
                                             <ul class="msg_list">
                                                <li>
                                                   <span><img src="images/layout_img/msg2.png" class="img-responsive" alt="#"></span>
                                                   <span>
                                                   <span class="name_user">Isidoro 7772314822</span>
                                                   <span class="msg_user">Sed ut perspiciatis unde omnis.</span>
                                                   <span class="time_ago">12 min ago</span>
                                                   </span>
                                                </li>
                                                <li>
                                                   <span><img src="images/layout_img/msg3.png" class="img-responsive" alt="#"></span>
                                                   <span>
                                                   <span class="name_user">Isidoro 7772314822</span>
                                                   <span class="msg_user">On the other hand, we denounce.</span>
                                                   <span class="time_ago">12 min ago</span>
                                                   </span>
                                                </li>
                                                <li>
                                                   <span><img src="images/layout_img/msg2.png" class="img-responsive" alt="#"></span>
                                                   <span>
                                                   <span class="name_user">Isidoro 7772314822</span>
                                                   <span class="msg_user">Sed ut perspiciatis unde omnis.</span>
                                                   <span class="time_ago">12 min ago</span>
                                                   </span>
                                                </li>
                                                <li>
                                                   <span><img src="images/layout_img/msg3.png" class="img-responsive" alt="#"></span>
                                                   <span>
                                                   <span class="name_user">Isidoro 7772314822</span>
                                                   <span class="msg_user">On the other hand, we denounce.</span>
                                                   <span class="time_ago">12 min ago</span>
                                                   </span>
                                                </li>
												 <li>
                                                   <span><img src="images/layout_img/msg3.png" class="img-responsive" alt="#"></span>
                                                   <span>
                                                   <span class="name_user">Isidoro 7772314822</span>
                                                   <span class="msg_user">On the other hand, we denounce.</span>
                                                   <span class="time_ago">12 min ago</span>
                                                   </span>
                                                </li>
                                             </ul>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

						<!-- --------------- -->
                        <<div class="col-md-8 chat-wrapper">
   <div class="white_shd full margin_bottom_30" style="display: block !important;">
      <div class="full graph_head">
         <div class="heading1 margin_0">
            <h2>Isidoro 7772314822</h2>
         </div>
      </div>
      
      <!-- Contenedor conversación -->
      <div class="full progress_bar_inner chat-container">
         <?php
         $mensajes = [
            ['texto' => 'Hola, ¿cómo estás?', 'tipo' => 'recibido', 'hora' => '10:30 AM'],
            ['texto' => 'Muy bien, ¿y tú?', 'tipo' => 'enviado', 'hora' => '10:31 AM'],
            ['texto' => 'También bien, gracias.', 'tipo' => 'recibido', 'hora' => '10:32 AM'],
            ['texto' => '¿Listo para la reunión?', 'tipo' => 'recibido', 'hora' => '10:33 AM'],
            ['texto' => 'Sí, ya estoy conectado.', 'tipo' => 'enviado', 'hora' => '10:34 AM'],
         ];

         foreach ($mensajes as $msg) {
            if ($msg['tipo'] == 'enviado') {
               echo '<div class="chat-bubble sent">'.$msg['texto'].'<span class="time">'.$msg['hora'].'</span></div>';
            } else {
               echo '<div class="chat-bubble received">'.$msg['texto'].'<span class="time">'.$msg['hora'].'</span></div>';
            }
         }
         ?>
      </div>

      <!-- Barra para escribir mensaje -->
      <div class="chat-input-area">
         <input type="text" class="chat-input" placeholder="Escribe un mensaje...">
         <button class="btn-send"><i class="fa fa-paper-plane"></i></button>
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
      	require_once('footer.php');
      ?>
   </body>
</html>