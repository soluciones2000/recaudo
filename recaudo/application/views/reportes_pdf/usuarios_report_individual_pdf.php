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
         font-family: Calibri, Verdana, Sans-serif;
         font-size: 14px;
         color: #000;
         font-weight: bold;
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
            font-size: 13px;
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
   	<!-- fin header -->
	<!-- inicio cuerpo -->
    <table style="width: 100%">
        <thead>
			<tr>
                <th>Campos</th>
				<th>Datos</th>
			</tr>
        </thead>
        <tbody>
            <tr>
                <td>Nombres</td>
                <td><?php echo utf8_encode($datos->nombres) ?></td>
            </tr>
            <tr>
                <td>Apellidos</td>
                <td><?php echo utf8_encode($datos->apellidos) ?></td>
            </tr>
            <tr>
                <td>Correo</td>
                <td><?php echo utf8_encode($datos->correo) ?></td>
            </tr>
            <tr>
                <td>Telefono</td>
                <td><?php echo $datos->telefono ?></td>
            </tr>
            <tr>
                <td>Ultimo ingreso</td>
                <td><?php echo $datos->ultimo_inicio ?></td>
            </tr>
            <tr>
                <td>Modulos</td>
                <td><?php echo $datos->modulos ?></td>
            </tr>
            <tr>
                <td>Estado</td>
                <?php if ($datos->activo == 1){ ?>
                <td>Activado</td>
                <?php }else{ ?>
                <td>desactivado</td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</body>
</html>
