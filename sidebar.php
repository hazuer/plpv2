<?php
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$url .= "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$pagina = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
$display='initial';
if($pagina=='dashboard'){
   $display='none';
}
$txtchg    = ($_SESSION['uLocation']==2) ? "Tlaquiltenango":"Zacatepec";
$txtchgval = ($_SESSION['uLocation']==2) ? 1:2;
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
// Host
$host = $_SERVER['HTTP_HOST'];
?>            
            <nav id="sidebar" class="active">
               <div class="sidebar_blog_1">
                  <div class="sidebar-header">
                     <div class="logo_section">
                        <a href="dashboard.php"><img class="logo_icon img-responsive" src="images/logo/logo_icon.png" alt="#" /></a>
                     </div>
                  </div>
                  <div class="sidebar_user_info">
                     <div class="icon_setting"></div>
                     <div class="user_profle_side">
                        <div class="user_img"><img class="img-responsive" src="images/layout_img/user_img.jpg" alt="#" /></div>
                        <div class="user_info">
                           <h6><?php echo $_SESSION["uName"];?></h6>
                           <p><span class="online_animation"></span> Online</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="sidebar_blog_2">
                  <h4>Panel</h4>
                  <ul class="list-unstyled components">
                     <li>
                        <a href="dashboard.php"><i class="fa fa-home blue2_color"></i> <span>Dashboard</span></a>
                     </li>
                     <li>
                        <a href="packages.php"><i class="fa fa-cubes blue1_color"></i> <span>En ruta</span></a>
                     </li>
                     <li>
                        <a href="whatsapp.php"><i class="fa fa-paper-plane green_color"></i> <span>WhatsApp</span></a>
                     </li>                     <li>
                        <a href="reports.php"><i class="fa fa-bar-chart purple_color"></i> <span>Reportes</span></a>
                     </li>
                     <li>
                        <a href="contacts.php"><i class="fa fa-users red_color"></i> <span>Contactos</span></a>
                     </li>
                     <li>
                        <a href="admin.php"><i class="fa fa-dashboard  yellow_color"></i> <span>Admin</span></a>
                     </li>
                     <li><a href="#" id="logoff"><i class="fa fa-sign-out orange_color"></i> <span>Salir</span></a></li>
                  </ul>
               </div>
            </nav>