<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DB.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
require_once('includes/session.php');

$id_location = $_SESSION['uLocation'];


# total de paquetes
$sql = "SELECT 
p.id_package 
FROM package p 
WHERE 1 
AND p.id_location IN ($id_location)
AND p.id_status IN(1,2,5,6,7,8)";
$tpackages = $db->select($sql);

$sqlpre = "SELECT 
p.id_package 
FROM package_tmp p 
WHERE 1 
AND p.id_location IN ($id_location)";
$tpre = $db->select($sqlpre);
?>
<!DOCTYPE html>
<html lang="es-MX">
    <head>
        <?php include_once('head.php');?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
.counter_no:hover,
.counter_no:hover .couter_icon {
    transform: scale(1.05);
}

.counter_no:hover .couter_icon {
    color: #007bff; /* ejemplo: cambia color del ícono */
    transition: all 0.3s ease;
}


        </style>
    </head>
    <body class="dashboard dashboard_1">
        <div class="full_container">
            <div class="inner_container">
                <?php include_once('sidebar.php');?>
                <div id="content">
                    <?php include_once('topbar.php');?>
                    <div class="midde_cont">
                        <div class="container-fluid">
                            <div class="row column_title">
                                <div class="col-md-12">
                                    <div class="page_title">
                                        <h2>Dashboard</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="row column1">
                                <div class="col-md-6 col-lg-4">
                                    <div class="full counter_section margin_bottom_30" style="display: block !important;">
                                    <a href="packages.php" class="counter_no">
                                    <div class="couter_icon">
                                            <div> 
                                                <i class="fa fa-cubes blue1_color"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="counter_no">
                                            <div>
                                                <p class="total_no"><?php echo count($tpackages); ?></p>
                                                <p class="head_couter">Paquetes en ruta</p>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="full counter_section margin_bottom_30" style="display: block !important;">
                                    <a href="prereg.php" class="counter_no">
                                    <div class="couter_icon">
                                            <div> 
                                                <i class="fa fa-cubes yellow_color"></i>
                                            </div>
                                        </div>
                                        <div class="counter_no">
                                            <div>
                                                <p class="total_no"><?php echo count($tpre); ?></p>
                                                <p class="head_couter">Paquetes sin rotular</p>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="full counter_section margin_bottom_30" style="display: block !important;">
                                        <a href="whatsapp.php" class="counter_no">
                                    <div class="couter_icon">
                                            <div>
                                                <i class="fa fa-comments-o green_color"></i>
                                            </div>
                                        </div>
                                        <div class="counter_no">
                                            <div>
                                                <p class="total_no"><?php echo $totalMensajeSinLeer; ?></p>
                                                <p class="head_couter">Mensajes nuevos</p>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ---------- -->
                        <div class="row graph margin_bottom_30">
                            <div class="col-md-12 col-lg-12">
                                <div class="white_shd full" style="display: block !important;">
                                    <div class="full graph_head">
                                        <div class="heading1 margin_0">
                                            <h2>Mensajes WABA</h2>
                                        </div>
                                    </div>
                                    <div class="full graph_revenue" style="display: block !important;">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="content">
                                                    <div class="area_chart" style="padding:15px; text-align:left;">
                                                   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ---------- -->
                    </div>
                </div>
            </div>
        </div>
        <?php
            require_once('footer.php');
        ?>
    </body>
</html>