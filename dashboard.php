<?php
session_start();
define( '_VALID_MOS', 1 );

require_once('includes/configuration.php');
require_once('includes/DB.php');
$db = new DB(HOST,USERNAME,PASSWD,DBNAME,PORT,SOCKET);
require_once('includes/session.php');

$id_location = $_SESSION['uLocation'];
$rParcel     = $_POST['rParcel'] ?? 99;
$andParcelIn = " AND p.id_cat_parcel IN (1,2,3)";
// Obtener el día actual del mes
$diaHoy       = date('j');
// Obtener el último día del mes actual con el día actual del mes
$ultimoDiaMes = date('t'); // Último día del mes actual
$ultimoD      = date('Y-m-' . min($diaHoy, $ultimoDiaMes)); // Ajusta al día actual del mes o al último día del mes si es mayor

// Convertir las fechas a objetos DateTime
#$diaInicial = new DateTime(date('Y-m-01')); // Primer día del mes actual
$diaInicial = new DateTime(date('Y-m-01')); // 'Y' es el año actual, 'm' es el mes actual y '01' es el primer día
$diaFinal   = new DateTime($ultimoD); // Último día del mes actual con el día actual del mes

$fini = $diaInicial->format('Y-m-d');
$f1   = $fini . ' 00:00:00';

$ffin = $diaFinal->format('Y-m-d');
$f2   = $ffin . ' 23:59:59';

$sql2="SELECT 
    p.id_status, 
    s.status_desc, 
    COUNT(*) AS count 
    FROM package p 
    JOIN cat_status s ON p.id_status = s.id_status 
    WHERE 
        p.c_date BETWEEN '$f1' AND '$f2' 
        AND p.id_location IN ($id_location) 
        $andParcelIn 
    GROUP BY p.id_status, s.status_desc 
    ORDER BY p.id_status";
$rst = $db->select($sql2);
$tpm = 0;

// Recorrer el arreglo y sumar los valores de 'count'
foreach ($rst as $item) {
    $tpm += intval($item['count']); // Convertir a entero antes de sumar
}
?>
<!DOCTYPE html>
<html lang="es-MX">
    <head>
        <?php include_once('head.php');?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                                        <div class="couter_icon">
                                            <div> 
                                                <i class="fa fa-cubes blue1_color"></i>
                                            </div>
                                        </div>
                                        <div class="counter_no">
                                            <div>
                                                <p class="total_no">123.50</p>
                                                <p class="head_couter">Paquetes en ruta</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="full counter_section margin_bottom_30" style="display: block !important;">
                                        <div class="couter_icon">
                                            <div> 
                                                <i class="fa fa-cubes yellow_color"></i>
                                            </div>
                                        </div>
                                        <div class="counter_no">
                                            <div>
                                                <p class="total_no">2500</p>
                                                <p class="head_couter">Paquetes sin rotular</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="full counter_section margin_bottom_30" style="display: block !important;">
                                        <div class="couter_icon">
                                            <div>
                                                <i class="fa fa-comments-o green_color"></i>
                                            </div>
                                        </div>
                                        <div class="counter_no">
                                            <div>
                                                <p class="total_no">54</p>
                                                <p class="head_couter">Mensajes nuevos</p>
                                            </div>
                                        </div>
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
                                            <h2><?php echo "Resumen del mes de ".date('F')." ".$desc_loc." Periodo del ".$fini." al ".$ffin;?></h2>
                                        </div>
                                    </div>
                                    <div class="full graph_revenue" style="display: block !important;">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="content">
                                                    <div class="area_chart" style="padding:15px; text-align:left;">
                                                    <!-- ---------- chart -->
                                                     <div class="row">
                                                        <div class='col-md-6'>
                                                            <?php
                                                            // Crear la tabla HTML
                                                            echo "<table class='table table-striped table-bordered nowrap table-hover' cellspacing='0' style='width:100%'>";
                                                            echo "<tr><th colspan='3' style='text-align:center;'>Total: ".$tpm." paquetes</th></tr>";
                                                            echo "<tr><th>Estatus</th><th>Total</th><th>Porcentaje</th></tr>";

                                                            // Recorrer el arreglo y generar las filas de la tabla
                                                            foreach ($rst as $row) {
                                                                $p= round(((100/$tpm)*$row["count"]),2);
                                                                echo "<tr>";
                                                                echo "<td>" . $row["status_desc"] . "</td>";
                                                                echo "<td>" . $row["count"] . "</td>";
                                                                echo "<td>" . $p . "%</td>";
                                                                echo "</tr>";
                                                            }
                                                            echo "</table>";

                                                            $labels = [];
                                                            $data = [];
                                                            ?>
                                                        </div>
                                                        <div class='col-md-6'>
                                                            <canvas id="myChart"></canvas>
                                                            <?php
                                                                foreach ($rst as $item) {
                                                                $labels[] = $item['status_desc']; // Nombre del estado
                                                                $data[]   = (int)$item['count'];    // Cantidad (convertida a número)
                                                                }

                                                                // Convertir arrays PHP a JSON para JavaScript
                                                                $labelsJson = json_encode($labels);
                                                                $dataJson   = json_encode($data);
                                                            ?>
                                                            <script>
                                                                // Convertir los datos de PHP a JavaScript
                                                                const labels     = <?php echo $labelsJson; ?>;
                                                                const dataValues = <?php echo $dataJson; ?>;

                                                                // Crear el gráfico con Chart.js
                                                                const ctx = document.getElementById('myChart').getContext('2d');
                                                                new Chart(ctx, {
                                                                type: 'pie', // Puedes cambiar a 'pie' o 'doughnut'
                                                                data: {
                                                                    labels: labels,
                                                                    datasets: [{
                                                                        label: 'Total',
                                                                        data: dataValues,
                                                                        backgroundColor: [
                                                                            'rgba(54, 162, 235, 0.6)',   // azul claro
                                                                            'rgba(75, 192, 192, 0.6)',   // verde agua
                                                                            'rgba(255, 99, 132, 0.6)',   // rojo rosado
                                                                            'rgba(255, 206, 86, 0.6)',   // amarillo
                                                                            'rgba(153, 102, 255, 0.6)',  // morado
                                                                            'rgba(255, 159, 64, 0.6)',   // naranja
                                                                            'rgba(60, 179, 113, 0.6)',   // verde medio
                                                                            'rgba(199, 199, 199, 0.6)'   // gris claro
                                                                        ],
                                                                        borderColor: [
                                                                            'rgba(54, 162, 235, 1)',   // azul claro
                                                                            'rgba(75, 192, 192, 1)',   // verde agua
                                                                            'rgba(255, 99, 132, 1)',   // rojo rosado
                                                                            'rgba(255, 206, 86, 1)',   // amarillo
                                                                            'rgba(153, 102, 255, 1)',  // morado
                                                                            'rgba(255, 159, 64, 1)',   // naranja
                                                                            'rgba(60, 179, 113, 1)',   // verde medio
                                                                            'rgba(199, 199, 199, 1)'   // gris claro
                                                                            ],
                                                                        borderWidth: 1
                                                                    }]
                                                                },
                                                                options: {
                                                                    responsive: true,
                                                                    plugins: {
                                                                        title: {
                                                                            display: true,          // Muestra el título
                                                                            text: 'Total: <?php echo $tpm; ?> paquetes',  // Título del gráfico
                                                                            font: {
                                                                                size: 18,           // Tamaño de la fuente
                                                                                family: 'Arial',     // Familia tipográfica
                                                                            },
                                                                            padding: 20             // Espaciado alrededor del título
                                                                        },
                                                                        legend: {
                                                                        position: 'top',      // Posición de la leyenda
                                                                        }
                                                                    },
                                                                    // Establecer la altura directamente
                                                                    maintainAspectRatio: false,
                                                                }
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                    <!-- ---------- chart -->
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