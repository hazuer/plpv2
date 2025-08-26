<style>
.full {
    display: flex;
    align-items: center;
}
.logo_section {
    margin-right: 20px;
}
.left_topbar {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-right: auto; /* Empuja lo demás a la derecha */
}
.search_input {
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-radius: 20px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
}
.search_input:focus {
    border-color: #03a9f4;
    box-shadow: 0 0 5px rgba(3,169,244,0.4);
}
</style>
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

<div class="topbar">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="full">
            <button type="button" id="sidebarCollapse" class="sidebar_toggle">
                <i class="fa fa-chevron-right"></i>
            </button>

            <!-- Logo -->
            <!--<div class="logo_section">
                <a href="index.html"><img class="img-responsive" src="images/logo/logo.png" alt="#" /></a>
            </div>
            -->
         
            <div class="left_topbar" style="margin-left:0px;">
         
                <div class="icon_info">
                    <ul class="user_profile_dd" style="display: <?php echo $display; ?>">
                        <li style="background-color: #03a9f4 !important;"> <!-- #15283c !important -->
                            <a class="dropdown-toggle" data-toggle="dropdown">
                                <span class="name_user"><i class="fa fa-bars"></i> Menu</span>
                            </a>
                            <div class="dropdown-menu">
                                 <?php 
                                 if($pagina=='packages'){
                                 ?>
                                <a href="#" id="btn-add-package"><span>Nuevo paquete</span></a>
                                <?php 
                                    }
                                 if($pagina=='whatsapp'){
                                 ?>
                                 <!--mesajes-->
                                 <a href="calendar.html"><span>Mensajes nuevos</span></a>
                                 <a href="dashboard_2.html"><span>Plant. de whatsApp</span></a>
                                 <a href="dashboard.html"><span>Enviar whatsApp</span></a>
                                 <a href="dashboard_2.html"><span>Plantilla de bot</span></a>
                                 <a href="dashboard_2.html"><span>Crear bot</span></a>
                                 <a href="dashboard.html"><span>Envio manual</span></a>
                                 <?php 
                                    }
                                 ?>
                                 <!--reportes-->
                                 <!--<a href="media_gallery.html"><span>Porcentaje entrega</span></a>

                                 <a href="email.html"><span>Nuevo contacto</span></a>

                                 <a href="dashboard.html"><span>Folio</span></a>
                                 <a href="dashboard.html"><span>Sin rotular</span></a>
                                 <a href="dashboard.html"><span>Crear código barras</span></a>
                                 <a href="dashboard.html"><span>Guías no liberadas</span></a>
                                 <a href="dashboard.html"><span>Inventario</span></a>
                                 <a href="dashboard.html"><span>Cambiar estatus</span></a>
                                 <a href="dashboard.html"><span>Cambiar ubicación</span></a>-->
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
            <input type="text" class="search_input" placeholder="Rotular/Verificar">
            <!-- Menú de iconos y usuario a la derecha -->
            <div class="right_topbar">
                <div class="icon_info">
                    <ul>
                        <li><a href="#"><i class="fa fa-qrcode"></i></a></li>
                        <li><a href="#"><i class="fa fa-comments-o"></i><span class="badge online_animation">3</span></a></li>
                    </ul>
                    <ul class="user_profile_dd">
                        <li>
                            <a class="dropdown-toggle" data-toggle="dropdown">
                                <span class="name_user"><?php echo $desc_loc;?></span></a>
                              <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" id="option-location-1" data-slocation="<?php echo $txtchgval; ?>" data-slocationd="<?php echo $txtchg;?>"><span><?php echo $txtchg;?></span></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>
<select name="option-location" id="option-location" style="display: none;" >
    <option value="1" <?php echo ($_SESSION['uLocation']==1) ? 'selected': ''; ?> >TLQ</option>
    <option value="2" <?php echo ($_SESSION['uLocation']==2) ? 'selected': ''; ?> >ZAC</option>
</select>