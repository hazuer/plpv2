<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DB.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
require_once('includes/session.php');

			$id_location = $_SESSION['uLocation'];
			$sql = "SELECT 
				p.tracking,
				p.folio,
				cc.contact_name receiver,
				UPPER(SUBSTRING(TRIM(REPLACE(
					REPLACE(
						REPLACE(
							REPLACE(
								REPLACE(
									REPLACE(
										REPLACE(
											REPLACE(cc.contact_name, 'á', 'a'),
										'é', 'e'),
									'í', 'i'),
								'ó', 'o'),
							'ú', 'u'),
						'Á', 'A'),
					'Ñ', 'N'),
				'É', 'E')), 1, 1)) AS initial,
				p.marker 
				FROM package p 
				INNER JOIN cat_contact cc ON cc.id_contact = p.id_contact 
				WHERE p.id_location IN ($id_location) 
				AND p.id_status IN (1, 2, 5, 6, 7, 8) 
				ORDER BY initial, p.folio
			";

			$result = $db->select($sql);
			$groupedPackages = [];
			// Contadores
			$countJMX1   = 0;
			$countCN1    = 0;
			$countImile1 = 0;

			foreach($result as $row){
				$initial = $row['initial'];  // La primera letra del nombre
				$folio   = $row['folio'];      // El folio del paquete

				// Recorrer el array y contar los que comienzan con "JMX"
				if (strpos($row['tracking'], 'JMX') === 0) {
					$countJMX1++;
				}else if(strpos($row['tracking'], 'CNMEX') === 0) {
					$countCN1++;
				} else {
					$countImile1++;
				}
				// Agrupar los paquetes por inicial
				if (!isset($groupedPackages[$initial])) {
					$groupedPackages[$initial] = [];
				}

				// Dentro de cada inicial, agrupar por folio
				$groupedPackages[$initial][] = [
					'tracking' => $row['tracking'],
					'folio'    => $folio,
					'receiver' => $row['receiver'],
					'marker'   => $row['marker']
				];
			}
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
         <script>
         let templateMsj    = `<?php echo $templateMsj;?>`;
         let uMarker        = `<?php echo $_SESSION["uMarker"];?>`;
         let uIdCatParcel   = `<?php echo $_SESSION["uIdCatParcel"];?>`;
         let largo          = `<?php echo LARGO;?>`;
         let alto           = `<?php echo ALTO;?>`;
         let rVoice         = `<?php echo $_SESSION["uVoice"]; ?>`
         </script>
         <script src="<?php echo BASE_URL;?>/assets/js/reports.js?version=<?php echo time(); ?>"></script>
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
                              <h2>Inventario | <?php echo "Total: ".count($result)." Paquetes |";
			if($countJMX1>0){echo "J&T:".$countJMX1." | "; }
			if($countCN1>0){echo "CNMEX:".$countCN1." | "; }
			if($countImile1>0){echo " IMILE:".$countImile1; }?>
			</h2>
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
                                    		
								 <div class="row column1">
								 <?php foreach ($groupedPackages as $initial => $packages): ?>
				<div class="col-4 col-md-1"></div>
				<div class="col-6 col-md-3" style="border: 1px solid black; min-height: 150px; background-color: lightblue; margin-bottom: 15px;">
					<div class="row">
						<div class="col-8" style="text-align:right;">
							<span style="font-size:28px;"><?php echo $initial."-".count($packages); ?></span>
						</div>
						<div class="col-4" style="text-align:right;">
							<?php
							// Contadores
							$countJMX   = 0;
							$countCN    = 0;
							$countImile = 0;

							// Recorrer el array y contar los que comienzan con "JMX"
							foreach ($packages as $item) {
								if (strpos($item['tracking'], 'JMX') === 0) {
									$countJMX++;
								} else if (strpos($item['tracking'], 'CNMEX') === 0) {
									$countCN++;
								} else {
									$countImile++;
								}
							}
							if($countJMX>0){echo "JT:".$countJMX."<br>"; }
							if($countCN>0){echo "CN:".$countCN."<br>"; }
							if($countImile>0){echo " IM:".$countImile; }
							?>
						</div>
					</div>
					<div class="row">
						<?php foreach ($packages as $package): ?>
							<div class="col-4 col-sm-3 col-md-3 mx-auto mb-1" data-toggle="tooltip" data-placement="top" title="<?php echo $package['tracking'];?>-<?php echo $package['receiver'];?>">
								<span style="color:<?php echo $package['marker'];?>">
									<input type="checkbox" autocomplete="off"><b><?php echo $package['folio']; ?></b>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
</div>
                                 

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
      require_once('footer.php');
      ?>
   </body>
</html>