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
                        <a href="#element"><i class="fa fa-bar-chart purple_color"></i> <span>Reportes</span></a>
                     </li>
                     <li>
                        <a href="#apps"><i class="fa fa-users red_color"></i> <span>Contactos</span></a>
                     </li>
                     <li>
                        <a href="#additional_page"><i class="fa fa-dashboard  yellow_color"></i> <span>Admin</span></a>
                     </li>
                     <li><a href="tables.html"><i class="fa fa-sign-out orange_color"></i> <span>Salir</span></a></li>
                  </ul>
               </div>
            </nav>