<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DB.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
require_once('includes/session.php');

$id_location = $_SESSION['uLocation'];

$smGuia   = $_POST['smGuia'] ?? '';
$smPhone  = $_POST['smPhone'] ?? '';

$labelSub = "En Ruta";
$sqlRutaFiltro ="SELECT DISTINCT 
        n.message_id 
    FROM package p
    INNER JOIN notification n 
        ON n.id_package = p.id_package 
    INNER JOIN ( 
        SELECT 
            n2.id_package, 
            MAX(n2.id_notification) AS last_notification 
        FROM notification n2 
        INNER JOIN package p2 
            ON n2.id_package = p2.id_package 
        WHERE 1 
        AND p2.id_location IN ($id_location) 
        AND p2.id_status IN (1, 2, 6) 
        AND n2.message_id LIKE 'wamid%' 
        GROUP BY n2.id_package 
    ) ult 
        ON n.id_package = ult.id_package 
    AND n.id_notification = ult.last_notification 
    ORDER BY n.id_notification DESC";


if(!empty($smGuia) || !empty($smPhone)){
    $labelSub = "Filtro";

    $andGuia ='';
    if(!empty($smGuia)){
        $andGuia = " AND p2.tracking IN('$smGuia')";
    }

    $andPhone ='';
    if(!empty($smPhone)){
        $andPhone = " AND p2.id_contact = (
			SELECT
				cc.id_contact
			FROM
				cat_contact cc
			WHERE
				cc.phone IN ('".$smPhone."') AND cc.id_contact = p2.id_contact
			LIMIT 1)";
    }

    $sqlRutaFiltro ="SELECT DISTINCT 
        n.message_id 
    FROM package p 
    INNER JOIN notification n 
        ON n.id_package = p.id_package 
    INNER JOIN (
        SELECT 
            n2.id_package,
            MAX(n2.id_notification) AS last_notification
        FROM notification n2
        INNER JOIN package p2 
            ON n2.id_package = p2.id_package
        WHERE 1
        AND p2.id_location IN ($id_location) 
        $andGuia 
        $andPhone 
        AND n2.message_id LIKE 'wamid%' 
        GROUP BY n2.id_package 
    ) ult 
        ON n.id_package = ult.id_package
    AND n.id_notification = ult.last_notification
    ORDER BY n.id_notification DESC";
}
$phonesWabaUnicos = $db->select($sqlRutaFiltro);

#Template
$sqlLocationInfo ="SELECT * FROM cat_location WHERE id_location IN($id_location)";
$infoLocation = $db->select($sqlLocationInfo);
$fechaDev = date("d/m/Y", strtotime("+2 days"));
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
		<script src="<?php echo BASE_URL;?>/assets/js/libraries/buttons.html5.min.js"></script>
		<link type="text/css" href="<?php echo BASE_URL;?>/assets/css/libraries/dataTables.checkboxes.css" rel="stylesheet"/>
		<script type="text/javascript" src="<?php echo BASE_URL;?>/assets/js/libraries/dataTables.checkboxes.min.js"></script>
<script src="<?php echo BASE_URL;?>/assets/js/whatsapp.js?version=<?php echo time(); ?>"></script>
         <script src="<?php echo BASE_URL;?>/assets/js/status_meta.js?version=<?php echo time(); ?>"></script>
      </head>
	<body class="dashboard dashboard_1"><body class="dashboard dashboard_1">
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
                              <h2>Estatus Mensajes Enviados Meta - <?php echo $labelSub; ?></h2>
                           </div>
                        </div>
                     </div>
                     <!-- row -->
                     <div class="row">
                        <!-- table section -->
                        <div class="col-md-12">
                           <div class="white_shd full margin_bottom_30">

                              <div class="table_section padding_infor_info">
                                 <div class="table-responsive-sm">

                                    <form id="frm-est-meta" action="<?php echo BASE_URL;?>/status_meta.php" method="POST">
										<div class="row">
											<div class="col-md-2">
												<div class="form-group">
													<label for="smGuia"><b></b></label>
													<input type="text" placeholder="Búscar Guía" class="form-control" name="smGuia" id="smGuia" value="<?php echo $smGuia; ?>" autocomplete="off">
												</div>
											</div>
                                            <div class="col-md-2">
												<div class="form-group">
													<label for="smPhone"><b></b></label>
													<input type="text" placeholder="Búscar Télefono" class="form-control" name="smPhone" id="smPhone" value="<?php echo $smPhone; ?>" autocomplete="off">
												</div>
											</div>
                                             <div class="col-md-1"><br>
												<div class="form-group">
													<button id="btn-filter-msj" type="submit" class="btn btn-success" data-dismiss="modal">Filtrar</button>
												</div>
                                            </div>
                                            <div class="col-md-1"><br>
                                                <button id="btn-borrar" type="button" class="btn btn-default">Borrar</button>
                                            </div>
                                        </div>

                                    </form>
                                 <table id="tbl-reports"class="table table-striped table-hover" cellspacing="0" style="width:100%">
										<thead class="thead-dark">
											 <tr>
                                                <th>Guía</th>
                                                <th>Phone</th>
                                                <th>F. Notificación</th>
                                                <th>Contacto</th>
                                                <th>Último Estatus</th>
                                                <th>Fecha</th>
                                                <th>Message ID</th>
                                            </tr>
										</thead>
										 <tbody>
                                        <?php
                                            foreach ($phonesWabaUnicos as $item) {
                                                $sqlGetWamid="SELECT 
                                                    n.n_date,
                                                    n.message_id,
                                                    n.id_package,
                                                    cc.phone,
                                                    cc.contact_name 
                                                FROM 
                                                    package p 
                                                INNER JOIN notification n ON n.id_package = p.id_package 
                                                INNER JOIN cat_contact cc ON cc.id_contact = p.id_contact 
                                                WHERE 1 
                                                    AND n.message_id LIKE 'wamid%' 
                                                    AND n.message_id IN ('".$item['message_id']."') 
                                                ORDER BY 
                                                    n.n_date DESC LIMIT 1";
                                                $dtsWamid = $db->select($sqlGetWamid);

                                                $sqlGuias="SELECT 
                                                p.tracking 
                                                FROM 
                                                    package p 
                                                INNER JOIN notification n ON n.id_package = p.id_package 
                                                WHERE 1 
                                                    AND n.message_id LIKE 'wamid%' 
                                                    AND n.message_id IN ('".$item['message_id']."') 
                                                ORDER BY p.tracking ASC";
                                                $dtsGuias = $db->select($sqlGuias);
                                                $trackings = array_column($dtsGuias, "tracking");
                                                $guias = implode(", ", $trackings);

                                                // Buscar último estatus de este message_id
                                                $sqlWabaStatus = "SELECT status_name, datelog, raw_json
                                                    FROM waba_status 
                                                    WHERE message_id = '".$dtsWamid[0]['message_id']."' 
                                                    ORDER BY FIELD(status_name, 'read', 'delivered', 'sent'), id_status DESC 
                                                    LIMIT 1";
                                                $statusRow = $db->select($sqlWabaStatus);
                                                $statusName = $statusRow ? $statusRow[0]['status_name'] : 'Pendiente..';
                                                $errorMessage = '';
                                                if ($statusName == 'failed') {
                                                    $rawJson = $statusRow[0]['raw_json'];
                                                    $errorData = json_decode($rawJson, true);
                                                    if ($errorData && isset($errorData['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['message'])) {
                                                        $errorMessage = "\n".$errorData['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['message'];
                                                    }
                                                }

                                                // if (!isset($statusCounts[$statusName])) $statusCounts[$statusName] = 0;
                                                // $statusCounts[$statusName]++;

                                                $statusDate = $statusRow ? $statusRow[0]['datelog'] : '-';
                                                echo "<tr>";
                                                echo "<td>{$guias}</td>";
                                                echo "<td>{$dtsWamid[0]['phone']}</td>";
                                                echo "<td>{$dtsWamid[0]['n_date']}</td>";
                                                echo "<td>{$dtsWamid[0]['contact_name']}</td>";
                                                echo "<td title=\"".$errorMessage."\">$statusName</td>";
                                                echo "<td>$statusDate</td>";
                                                echo "<td>{$dtsWamid[0]['message_id']}</td>";
                                                echo "</tr>";
                                            }
                                        ?>
                                        <tbody>
									</table>

                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
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