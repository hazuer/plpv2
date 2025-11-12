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
<!-- <div id="banner-festejo" class="alert alert-success text-center mb-0 py-3" style="font-size: 1.2rem; background: linear-gradient(90deg, #ffcc00, #ff6699); color: white; font-weight: bold;">
  🎂 ¡Feliz cumpleaños User! 🎉
</div> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script> -->
<script>
  // Lista de canciones
  /*const canciones = [
    "mananitas.mp3",
    "mariachi.mp3",
    "son.mp3"
  ];
  let reproductor = new Audio();
  let confetiIntervalo = null;
  function lanzarConfeti() {
    confetti({
      particleCount: 150,
      spread: 120,
      origin: { y: 0.6 }
    });
  }
  function lanzarConfetiYMusica() {
    // 🎶 Selección aleatoria de canción
    const aleatoria = canciones[Math.floor(Math.random() * canciones.length)];
    reproductor.src = aleatoria;
    reproductor.play();
    // 🚀 Inicia confeti en intervalos mientras la canción suena
    if (confetiIntervalo) clearInterval(confetiIntervalo);
    confetiIntervalo = setInterval(lanzarConfeti, 800);
    // Cuando termine la música, parar confeti
    reproductor.onended = () => {
      clearInterval(confetiIntervalo);
      confetiIntervalo = null;
    };
  }
  // Escuchar clic en el banner
  document.getElementById("banner-festejo").addEventListener("click", lanzarConfetiYMusica);
  // Lanzar automáticamente al cargar la página
  window.onload = lanzarConfeti;*/
</script>
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
            <?php 
            $colorActiveMenu = '';
            $nameOptionMenu      = '';
            if($pagina=='packages'){ $colorActiveMenu="#2196f3"; $nameOptionMenu='Paquetes'; }
            if($pagina=='whatsapp' || $pagina=='status_meta'){$colorActiveMenu="#009688"; $nameOptionMenu='WhatsApp'; }
            if($pagina=='reports' || $pagina=='reportspe'|| $pagina=='prereg' || $pagina=='audit'){ $colorActiveMenu="#673ab7"; $nameOptionMenu='Reportes'; }
            if($pagina=='contacts'){ $colorActiveMenu="#e91e63"; $nameOptionMenu='Contactos'; }
            if($pagina=='admin'){ $colorActiveMenu="#ff9800"; $nameOptionMenu='Admin'; }
            ?>
            <div class="left_topbar" style="margin-left:0px;">
                <div class="icon_info">
                    <ul class="user_profile_dd" style="display: <?php echo $display; ?>">
                        <li style="background-color: <?php echo $colorActiveMenu;?> !important;"> <!-- #15283c !important -->
                            <a class="dropdown-toggle" data-toggle="dropdown">
                                <span class="xname_user"><i class="fa fa-bars"></i> <span id="name_user"><?php echo $nameOptionMenu;?></span></span>
                            </a>
                            <div class="dropdown-menu">
                                <?php
                                if($pagina=='packages'){
                                ?>
                                    <a href="packages.php"><span>Paquetes</span></a>
                                	<a href="#" id="btn-add-package"><span>Nuevo paquete</span></a>
                                    <?php if($host==NAME_HOST_LOCAL){?>
                                    <a href="#" id="btn-template"><span>Plantilla de bot</span></a>
									<a href="#" id="btn-bot"><span>Crear bot</span></a>
									<a href="handler.php"><span>Envio manual</span></a>
                                    <?php }?>
                                    <a href="#" id="btn-folio"><span>Folio</span></a>
                                    <a href="#" id="btn-ocurre"><span>Crear código barras</span></a>
                                    <a href="#" id="btn-sync"><span>Guías no liberadas</span></a>
                                <?php
                                }
                                if($pagina=='whatsapp' || $pagina=='status_meta'){
                                ?>
                                 	<!--mesajes-->
									<a href="whatsapp.php"><span>Mensajes nuevos</span></a>
									<a href="#" id="waba-template"><span>Envío de mensajes meta</span></a>
                                    <a href="status_meta.php"><span>Estatus mensajes enviados</span></a>
                                <?php
                                }
                                 if($pagina=='reports' || $pagina=='reportspe'|| $pagina=='prereg' || $pagina=='audit'){
                                    ?>
                                    <a href="reports.php"><span>Reporte personalizado</span></a>
									<a href="reportspe.php"><span>Porcentaje entrega</span></a>
									<a href="prereg.php"><span>Sin rotular</span></a>
									<a href="audit.php"><span>Auditoria</span></a>
                                <?php
                                }
                                if($pagina=='contacts'){
                                ?>
                                 	<a href="#" id="btn-add-contact"><span>Nuevo contacto</span></a>
                                <?php 
								}
                                if($pagina=='admin'){
								?>
                                 <a href="admin.php"><span>Inventario</span></a>
                                 <!-- <a href="dashboard.html"><span>Cambiar estatus</span></a>
                                 <a href="dashboard.html"><span>Cambiar ubicación</span></a> -->
                                 <?php 
								}
								?>
                                <a class="dropdown-item" href="#" id="option-location-2" data-slocation="<?php echo $txtchgval; ?>" data-slocationd="<?php echo $txtchg;?>"><span>Cambiar a <?php echo $txtchg;?></span></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <input type="text" class="search_input" placeholder="Rotular/Verificar" id="vGuia">
            <!-- Menú de iconos y usuario a la derecha -->
            <div class="right_topbar">
                <div class="icon_info">
                    <ul>
                        <li><a href="#" id="btn-scan-qr"><i class="fa fa-qrcode blue1_color fa-lg"></i></a></li>
                        <?php 
                        if($totalMensajeSinLeer>0){
                        ?>
                        <li><a href="whatsapp.php"><i class="fa fa-whatsapp green_color fa-lg"></i><span class="badge online_animation"><?php
                         echo $totalMensajeSinLeer;
                        }
                        ?></span></a></li>
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