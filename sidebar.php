            <nav id="sidebar">
               <div class="sidebar_blog_1">
                  <div class="sidebar-header">
                     <div class="logo_section">
                        <a href="index.html"><img class="logo_icon img-responsive" src="images/logo/logo_icon.png" alt="#" /></a>
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
                  <h4>Menú</h4>
                  <ul class="list-unstyled components">
                     <li class="active">
                        <a href="#dashboard" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-cubes blue1_color"></i> <span>En ruta</span></a>
                        <ul class="collapse list-unstyled" id="dashboard">
                           <li>
                              <a href="dashboard.html">> <span>Nuevo</span></a>
                           </li>
                           <!--<li>
                              <a href="dashboard_2.html">> <span>Dashboard style 2</span></a>
                           </li> -->
                        </ul>
                     </li>
                     <li>
                        <a href="#config" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-paper-plane green_color"></i> <span>WhatsApp</span></a>
                        <ul class="collapse list-unstyled" id="config">
                            <li><a href="calendar.html">> <span>Mensajes nuevos</span></a></li>
                            <li>
                              <a href="dashboard_2.html">> <span>Plantilla de whatsApp</span></a>
                           </li>
                            <li>
                              <a href="dashboard.html">> <span>Enviar whatsApp</span></a>
                           </li>
                           <li>
                              <a href="dashboard_2.html">> <span>Plantilla de bot</span></a>
                           </li>
                           <li>
                              <a href="dashboard_2.html">> <span>Crear bot</span></a>
                           </li>
                            <li>
                              <a href="dashboard.html">> <span>Envio manual</span></a>
                           </li>
                        </ul>
                     </li>
                     <!--<li><a href="widgets.html"><i class="fa fa-clock-o orange_color"></i> <span>Widgets</span></a></li>-->
                     <li>
                        <a href="#element" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-bar-chart purple_color"></i> <span>Reportes</span></a>
                        <ul class="collapse list-unstyled" id="element">
                           <li><a href="media_gallery.html">> <span>Porcentaje entrega</span></a></li>
                        </ul>
                     </li>
                     <!--<li><a href="tables.html"><i class="fa fa-table purple_color2"></i> <span>Tables</span></a></li> -->
                     <li>
                        <a href="#apps" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-users red_color"></i> <span>Contactos</span></a>
                        <ul class="collapse list-unstyled" id="apps">
                           <li><a href="email.html">> <span>Nuevo</span></a></li>
                        </ul>
                     </li>
                     <!--<li><a href="price.html"><i class="fa fa-briefcase blue1_color"></i> <span>Pricing Tables</span></a></li>
                     <li>
                        <a href="contact.html">
                        <i class="fa fa-paper-plane red_color"></i> <span>Contact</span></a>
                     </li>-->
                     <li class="active">
                        <a href="#additional_page" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-clone yellow_color"></i> <span>Admin</span></a>
                        <ul class="collapse list-unstyled" id="additional_page">
                            <li>
                              <a href="dashboard.html">> <span>Folio</span></a>
                           </li>
                            <li>
                              <a href="dashboard.html">> <span>Sin rotular</span></a>
                           </li>
                           <li>
                              <a href="dashboard.html">> <span>Crear código barras</span></a>
                           </li>
                           <li>
                              <a href="dashboard.html">> <span>Guías no liberadas</span></a>
                           </li>
                            <li>
                              <a href="dashboard.html">> <span>Inventario</span></a>
                           </li>
                            <li>
                              <a href="dashboard.html">> <span>Cambiar estatus</span></a>
                           </li>
                            <li>
                              <a href="dashboard.html">> <span>Cambiar ubicación</span></a>
                           </li>
                        </ul>
                     </li>
                     <!--<li><a href="map.html"><i class="fa fa-map purple_color2"></i> <span>Map</span></a></li>
                     <li><a href="charts.html"><i class="fa fa-bar-chart-o green_color"></i> <span>Charts</span></a></li>-->
                  </ul>
               </div>
            </nav>