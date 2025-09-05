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
                                            <h2>Estatus WABA</h2>
                                        </div>
                                    </div>
                                    <div class="full graph_revenue" style="display: block !important;">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="content">
                                                    <div class="table_section padding_infor_info">
                                 <div class="table-responsive-sm">
                                                   <?php 
                                                   $sql="SELECT DISTINCT 
                                                    n.id_package,
                                                    n.n_date,
                                                    cc.phone,
                                                    cc.contact_name,
                                                    n.message_id
                                                    FROM package p 
                                                    INNER join notification n on n.id_package=p.id_package 
                                                    INNER JOIN cat_contact cc ON cc.id_contact = p.id_contact
                                                    WHERE 1 
                                                    AND p.id_location IN ($id_location)
                                                    AND p.id_status IN(1,2,5,6,7,8)
                                                    AND n.message_id like 'wamid%'
                                                    ORDER BY cc.contact_name;
                                                    ";
                                                    $phonesWabaUnicos = $db->select($sql);

                                                   $uniquePhones = [];
                                                    $seenMessages = [];

                                                    foreach ($phonesWabaUnicos as $row) {
                                                        if (!in_array($row['message_id'], $seenMessages)) {
                                                            $uniquePhones[] = $row;
                                                            $seenMessages[] = $row['message_id'];
                                                        }
                                                    }

                                                   
                                                    
                                                    ?>
  
                                                    <table id="tbl-reports" class="table table-striped table-hover" cellspacing="0" style="width:100%">
										<thead class="thead-dark"><tr>
                                                            <th>ID Package</th>
                                                            <th>Phone</th>
                                                            <th>F. Notificación</th>
                                                            <th>Contact</th>
                                                            <th>Message ID</th>
                                                            <th>Último Estatus</th>
                                                            <th>Fecha</th>
                                                        </tr>
                                        </thead>
                                        <tbody>
<?php


                                                    foreach ($uniquePhones as $item) {
                                                        // Buscar último estatus de este message_id
                                                        $msgId = $item['message_id'];
                                                        $sqlWabaStatus = "
                                                            SELECT status_name, datelog 
                                                            FROM waba_status 
                                                            WHERE message_id = '".$msgId."' 
                                                            ORDER BY id_status DESC 
                                                            LIMIT 1
                                                        ";
                                                        $statusRow = $db->select($sqlWabaStatus);

                                                        $statusName = $statusRow ? $statusRow[0]['status_name'] : 'SIN ESTATUS';
                                                        $statusDate = $statusRow ? $statusRow[0]['datelog'] : '-';

                                                       
                                                        echo "<tr>";
                                                        echo "<td>{$item['id_package']}</td>";
                                                        echo "<td>{$item['phone']}</td>";
                                                        echo "<td>{$item['n_date']}</td>";
                                                        echo "<td>{$item['contact_name']}</td>";
                                                        echo "<td>{$item['message_id']}</td>";
                                                        echo "<td>$statusName</td>";
                                                        echo "<td>$statusDate</td>";
                                                        echo "</tr>";
                                                    }
                                                    echo "<tbody></table>";
                                                   ?>
                                                   
                                                    </div>
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