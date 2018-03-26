<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style type="text/css">
        body {
         background-color: #fff;
         margin: 20px;
         font-family: Lucida Grande, Verdana, Sans-serif;
         font-size: 14px;
         color: #000;
        }

        h1 {
         color: #000;
         background-color: transparent;
         border-bottom: 1px solid #D0D0D0;
         font-size: 16px;
         font-weight: bold;
         margin: 24px 0 2px 0;
         padding: 5px 0 6px 0;
        }

        h2 {
         color: #000;
         background-color: transparent;
         font-size: 16px;
         font-weight: bold;
         margin: 10px 0px 10px 0px; /* top right botton left */
         padding: 0px 0px 0px -5px;
         text-align: left;
        }
		
		hr {border: 0; height: 2; box-shadow: 0 1px 5px 1px purple;}
		
        /* estilos para la tabla */
        table{
            text-align: left;
			border-collapse: collapse;
			margin: 0px;
        }
		th, td
		{
			padding-top: 1px;
			padding-right: 1px;
			padding-bottom: 1px;
			padding-left: 5px;
   			border: solid 1px #000;
   			vertical-align: middle;
   			text-align: left;
		}
        thead
        {
			border-top: solid 1px rgba(15,36,62,1.00);
   			border: solid 1px rgba(15,36,62,1.00);
            background-color: rgba(15,36,62,1.00);
			color: #fff;
        }
		.hidden
		{
			border: hidden;
			border-bottom: hidden;
		}

    </style>
</head>
<body>
    <table style="width: 100%;">
		<tbody>
			<tr>
                <td rowspan="2" style="text-align: center;"><img src="<?php echo $logo; ?>"></td>
                <td style="text-align: center;"><?php echo $header; ?></td>
                <td style="text-align: center;"><?php echo $rif; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;"><?php echo $title; ?></td>
                <td style="text-align: center;"><?php echo $contador; ?></td>
            </tr>
		</tbody>
	</table>
	<table style="width: 100%;">
		<tr>
			<td class="hidden">
				<!--<h2>ESPECIALIDAD: <?php echo $carrera; ?></h2>
				<h2>PROMOCION: <?php echo $promocion; ?></h2>-->
			</td>
			<td class="hidden">
				<h2 style="text-align: right">FECHA: <?php echo date("d-m-Y"); ?></h2>
			</td>
		</tr>
	</table>
   	<!-- fin header -->
	<!-- inicio cuerpo -->
    <table style="width: 100%">
        <thead>
			<tr>
                <th><?php echo utf8_encode("Nº") ?></th>
                <th>Razon social</th>
                <th>Nombre</th>
                <th><?php echo utf8_encode("Nº") ?> de reportes realizados</th>
                <th>Predeterminado</th>
			</tr>
        </thead>
        <tbody>
            <?php $a = 1; ?>
            <?php  foreach($datos as $row) { ?>
            <tr>
                <td style="font-size: 10px;"><?php echo $a++ ?></td>
                <td style="font-size: 10px;"><?php echo utf8_encode($row->razon_social) ?></td>
                <td style="font-size: 10px;"><?php echo utf8_encode($row->nombre_empresa) ?></td>
                <td style="font-size: 10px;"><?php echo $row->contador ?></td>
                <?php if ($row->predeterminado == 1){ ?>
                    <td style="font-size: 10px;">Si</td>
                <?php }else{ ?>
                    <td style="font-size: 10px;">No</td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
