<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DB.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
//require_once('includes/session.php');

if(isset($_SESSION["uActive"])){
	header('Location: '.BASE_URL.'/dashboard.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="es-MX">
      <head>
         <?php include_once('head.php');?>
         <script src="<?php echo BASE_URL;?>/assets/js/login.js"></script>
      </head>
   <body class="inner_page login">
      <div class="full_container">
         <div class="container">
            <div class="center verticle_center full_height">
               <div class="login_section">
                  <div class="logo_login">
                     <div class="center">
                        <img width="210" src="images/logo/logo.png" alt="#" />
                     </div>
                  </div>
                  <div class="login_form">
                     <form onsubmit="return false;">
                        <fieldset>
                           <div class="field">
                              <label class="label_field">* Usuario</label>
                              <input type="text" autofocus name="email" placeholder="Usuario" autocomplete="off" name="username" id="username"/>
                           </div>
                           <div class="field">
                              <label class="label_field">* Contraseña</label>
                              <input type="password" name="password" placeholder="Contraseña" autocomplete="off" name="password" id="password"/>
                           </div>
                           <div class="field margin_0">
                              <label class="label_field hidden">Iniciar</label>
                              <button class="main_bt" name="btn-login" id="btn-login">Iniciar</button>
                           </div>
                        </fieldset>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>