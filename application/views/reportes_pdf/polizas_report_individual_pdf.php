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
    </style>
</head>
<body>
    <!-- inicio del header -->
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
    <!-- inicio de los campos del contratante -->
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%">Datos de identificacion del contratante/Tomador</th>
            </tr>
            <tr>
                <th style="width: 100%">Persona natural/Juridica</th>
            </tr>
        </thead>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 25%">Nombres y Apellidos/Razon social</td>
                <td style="width: 25%"><?php echo utf8_encode($datos["contratante"]->nombre_razonsocial); ?></td>
                <td style="width: 15%">C.I. / Pasaporte/ Rif</td>
                <td style="width: 15%"><?php echo utf8_encode($datos["contratante"]->ci_rif); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width: 12.5%">Tipo de persona</td>
                <td style="width: 12.5%"><?php echo utf8_encode($datos["contratante"]->tipo_persona); ?></td>
                <td style="width: 12.5%">Nacionalidad</td>
                <td style="width: 12.5%"><?php echo utf8_encode($datos["contratante"]->nacionalidad); ?></td>
                <td style="width: 12.5%">Sexo</td>
                <td style="width: 12.5%"><?php echo utf8_encode($datos["contratante"]->sexo); ?></td>
                <td style="width: 12.5%">Estado civil</td>
                <td style="width: 12.5%"><?php echo utf8_encode($datos["contratante"]->estado_civil); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Fecha de nacimiento/Constitucion</td>
                <td><?php echo date("d-m-Y", strtotime(utf8_encode($datos["contratante"]->fecha_nacimiento_constitucion))); ?></td>
                <td>Lugar de nacimiento</td>
                <td><?php echo utf8_encode($datos["contratante"]->lugar_nacimiento_constitucion); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Nombre del registro mercantil</td>
                <td><?php echo utf8_encode($datos["contratante"]->nombre_registro_mercantil ); ?></td>
                <td>Numero de registro</td>
                <td><?php echo utf8_encode($datos["contratante"]->numero_registro); ?></td>
                <td>Numero de tomo</td>
                <td><?php echo utf8_encode($datos["contratante"]->numero_tomo); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Profesion o actividad economica</td>
                <td><?php echo utf8_encode($datos["contratante"]->profesion_actividad_economica); ?></td>
                <td>Ingreso promedio anual</td>
                <td><?php echo utf8_encode($datos["contratante"]->ingreso_prome_anual); ?></td>
            </tr>
            <tr>
                <td>Representante legal:Nombres y apellidos</td>
                <td><?php echo utf8_encode($datos["contratante"]->representante_legal); ?></td>
                <td>C.I.</td>
                <td><?php echo utf8_encode($datos["contratante"]->ci_representante_legal); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Pais</td>
                <td><?php echo utf8_encode($datos["contratante"]->pais); ?></td>
                <td>Estado</td>
                <td><?php echo utf8_encode($datos["contratante"]->estado); ?></td>
                <td>Ciudad</td>
                <td><?php echo utf8_encode($datos["contratante"]->ciudad); ?></td>
            </tr>
            <tr>
                <td>Municipio</td>
                <td><?php echo utf8_encode($datos["contratante"]->municipio); ?></td>
                <td>Parroquia</td>
                <td><?php echo utf8_encode($datos["contratante"]->parroquia); ?></td>
                <td>Urbanizacion</td>
                <td><?php echo utf8_encode($datos["contratante"]->urbanizacion); ?></td>
            </tr>
            <tr>
                <td>Calle</td>
                <td><?php echo utf8_encode($datos["contratante"]->calle); ?></td>
                <td>Local / Casa/ Apto</td>
                <td><?php echo utf8_encode($datos["contratante"]->centrocomercial_casa); ?></td>
                <td>Piso</td>
                <td><?php echo utf8_encode($datos["contratante"]->piso); ?></td>
            </tr>
            <tr>
                <td>Nº Apto</td>
                <td><?php echo utf8_encode($datos["contratante"]->num_apto); ?></td>
                <td>Telefono 1</td>
                <td><?php echo utf8_encode($datos["contratante"]->telf1); ?></td>
                <td>Telefono 2</td>
                <td><?php echo utf8_encode($datos["contratante"]->telf2); ?></td>
            </tr>
            <tr>
                <td>Telefono 3</td>
                <td><?php echo utf8_encode($datos["contratante"]->telf3); ?></td>
                <td>Telefono celular</td>
                <td><?php echo utf8_encode($datos["contratante"]->telf_cel); ?></td>
                <td>Fax</td>
                <td><?php echo utf8_encode($datos["contratante"]->fax); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Zona postal</td>
                <td><?php echo utf8_encode($datos["contratante"]->zona_postal); ?></td>
                <td>Correo</td>
                <td><?php echo utf8_encode($datos["contratante"]->correo); ?></td>
            </tr>
        </tbody>
    </table>
    <!-- fin de los campos del contratante -->
    <!-- inicio de los campos del asegurado -->
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%">Datos de identificacion del asegurado titular</th>
            </tr>
        </thead>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Tipo doc de indentidad</td>
                <td><?php echo utf8_encode($datos["asegurado"]->tipo_documento_asegurado); ?></td>
                <td>Doc de indentidad</td>
                <td><?php echo utf8_encode($datos["asegurado"]->ci_pasaporte_asegurado); ?></td>
                <td>Nacionalidad</td>
                <td><?php echo utf8_encode($datos["asegurado"]->nacionalidad_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Sexo</td>
                <td><?php echo utf8_encode($datos["asegurado"]->sexo_asegurado); ?></td>
                <td>Nombres</td>
                <td><?php echo utf8_encode($datos["asegurado"]->nombres_asegurado); ?></td>
                <td>Apellidos</td>
                <td><?php echo utf8_encode($datos["asegurado"]->apellidos_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Fecha de nacimiento</td>
                <td><?php echo date("d-m-Y", strtotime(utf8_encode($datos["asegurado"]->fecha_nacimiento_asegurado))); ?></td>
                <td>Lugar de nacimiento</td>
                <td><?php echo utf8_encode($datos["asegurado"]->lugar_nacimiento_asegurado); ?></td>
                <td>Edad</td>
                <td><?php echo utf8_encode($datos["asegurado"]->edad_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Estado civil</td>
                <td><?php echo utf8_encode($datos["asegurado"]->estado_civil_asegurado); ?></td>
                <td>Profesion</td>
                <td><?php echo utf8_encode($datos["asegurado"]->profesion_asegurado); ?></td>
                <td>Ocupacion</td>
                <td><?php echo utf8_encode($datos["asegurado"]->ocupacion_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Ingreso anual promedio</td>
                <td><?php echo utf8_encode($datos["asegurado"]->ingreso_anual_asegurado); ?></td>
                <td>Fecha de ingreso a la empresa</td>
                <td><?php echo date("d-m-Y", strtotime(utf8_encode($datos["asegurado"]->fecha_ingreso_empresa_asegurado))); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Fecha de ingreso al seguro</td>
                <td><?php echo date("d-m-Y", strtotime(utf8_encode($datos["asegurado"]->fecha_ingreso_poliza_asegurado))); ?></td>
                <td>Estatura</td>
                <td><?php echo utf8_encode($datos["asegurado"]->estatura_asegurado); ?></td>
                <td>Peso</td>
                <td><?php echo utf8_encode($datos["asegurado"]->peso_asegurado); ?></td>
                <td>Zurdo</td>
                <td><?php echo utf8_encode($datos["asegurado"]->zurdo_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Deportes / Pasatiempos</td>
                <td><?php echo utf8_encode($datos["asegurado"]->deportes_pasatiempo); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Pais</td>
                <td><?php echo utf8_encode($datos["asegurado"]->pais_asegurado); ?></td>
                <td>Estado</td>
                <td><?php echo utf8_encode($datos["asegurado"]->estado_asegurado); ?></td>
                <td>Ciudad</td>
                <td><?php echo utf8_encode($datos["asegurado"]->ciudad_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Municipio</td>
                <td><?php echo utf8_encode($datos["asegurado"]->municipio_asegurado); ?></td>
                <td>Parroquia</td>
                <td><?php echo utf8_encode($datos["asegurado"]->parroquia_asegurado); ?></td>
                <td>urbanizacion</td>
                <td><?php echo utf8_encode($datos["asegurado"]->urbanizacion_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Calle</td>
                <td><?php echo utf8_encode($datos["asegurado"]->calle_asegurado); ?></td>
                <td>Local / casa / Apto</td>
                <td><?php echo utf8_encode($datos["asegurado"]->centrocomercial_casa_asegurado); ?></td>
                <td>Piso</td>
                <td><?php echo utf8_encode($datos["asegurado"]->piso_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Nº Apto</td>
                <td><?php echo utf8_encode($datos["asegurado"]->num_apto_asegurado); ?></td>
                <td>Telefono 1</td>
                <td><?php echo utf8_encode($datos["asegurado"]->telf1_asegurado); ?></td>
                <td>Telefono 2</td>
                <td><?php echo utf8_encode($datos["asegurado"]->telf2_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Telefono 3</td>
                <td><?php echo utf8_encode($datos["asegurado"]->telf3_asegurado); ?></td>
                <td>Telefono celular</td>
                <td><?php echo utf8_encode($datos["asegurado"]->telf_cel_asegurado); ?></td>
                <td>Fax</td>
                <td><?php echo utf8_encode($datos["asegurado"]->fax_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td>Zona postal</td>
                <td><?php echo utf8_encode($datos["asegurado"]->zona_postal_asegurado); ?></td>
                <td>Correo</td>
                <td><?php echo utf8_encode($datos["asegurado"]->correo_asegurado); ?></td>
            </tr>
        </tbody>
    </table>
    <!-- fin de los campos del asegurado -->             
    <!-- inicio de los campos de otros beneficiarios --> 
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%"><?php echo utf8_encode("Grupo a asegurar"); ?></th>
            </tr>
        </thead>
    </table>
	<?php foreach ($datos["grupo"] as $grupo){ ?>
	    <table style="width: 100%">
	        <tbody>
	            <tr>
	                <td>C.I. / Pasaporte</td>
	                <td><?php echo utf8_encode($grupo->ci_pasaporte_grupo); ?></td>
	                <td>Nombres y apellidos</td>
	                <td><?php echo utf8_encode($grupo->nombres_apellidos_grupo); ?></td>
                    <td>Parentesco</td>
                    <td><?php echo utf8_encode($grupo->parentesco_grupo); ?></td>
	            </tr>
	        </tbody>
	    </table>
	    <table style="width: 100%">
	        <tbody>
	            <tr>
	                <td>Fecha de nacimiento</td>
	                <td><?php echo utf8_encode($grupo->fecha_nacimiento_grupo); ?></td>
	                <td>Edad</td>
	                <td><?php echo utf8_encode($grupo->edad_grupo); ?></td>
                    <td>Sexo</td>
                    <td><?php echo utf8_encode($grupo->sexo_grupo); ?></td>
                    <td>Estado civil</td>
                    <td><?php echo utf8_encode($grupo->estado_civil_grupo); ?></td>
	            </tr>
	        </tbody>
	    </table>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td>Peso</td>
                    <td><?php echo utf8_encode($grupo->peso_grupo); ?></td>
                    <td>Estatura</td>
                    <td><?php echo utf8_encode($grupo->estatura_grupo); ?></td>
                    <td>Zurdo</td>
                    <td><?php echo utf8_encode($grupo->zurdo_grupo); ?></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td><?php echo utf8_encode("Profesión"); ?></td>
                    <td><?php echo utf8_encode($grupo->profesion_grupo); ?></td>
                    <td><?php echo utf8_encode("Ocupación"); ?></td>
                    <td><?php echo utf8_encode($grupo->ocupacion_grupo); ?></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td>Pasatiempos</td>
                    <td><?php echo utf8_encode($grupo->pasatiempos_grupo); ?></td>
                    <td>Deportes</td>
                    <td><?php echo utf8_encode($grupo->deportes_grupo); ?></td>
                </tr>
            </tbody>
        </table>
	<?php } ?>
    <!-- fin de los campos de otros beneficiarios --> 
    <!-- inicio de los campos de la cobertura -->
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%">Coberturas solicitadas</th>
            </tr>
        </thead>
    </table>
    <table style="width: 100%">
	    <tbody>
	        <tr>          
	           	<td>Plan Basico Suma asegurada</td>
                <td><?php echo utf8_encode($datos["cobertura"]->suma_asegurada_basico); ?></td>
                <td>Deducible</td>
                <td><?php echo utf8_encode($datos["cobertura"]->deducible_basico); ?></td>
            </tr> 
            <tr>
                <td>Plan maternidad Suma asegurada</td>
                <td><?php echo utf8_encode($datos["cobertura"]->suma_asegurada_maternidad); ?></td>
                <td>Deducible</td>
                <td><?php echo utf8_encode($datos["cobertura"]->deducible_maternidad); ?></td>
            </tr>
            <tr>
                <td>Plan execeso Suma asegurada</td>
                <td><?php echo utf8_encode($datos["cobertura"]->suma_asegurada_exceso); ?></td>
                <td>Deducible</td>
                <td><?php echo utf8_encode($datos["cobertura"]->deducible_exceso); ?></td>
            </tr>
            <tr>
                <td>Fecha de corte</td>
                <td><?php echo date("d-m-Y", strtotime(utf8_encode($datos["cobertura"]->fecha_corte))); ?></td>
                <td>Gastos funerarios</td>
                <td><?php echo utf8_encode($datos["cobertura"]->gastos_funerarios); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%">Accidentes personales(Solo titular asegurado)</th>
            </tr>
        </thead>
    </table>
    <table style="width: 100%">
	    <tbody>
	        <tr>
		        <td>Muerte accidental</td>          
	            <td><?php echo utf8_encode($datos["cobertura"]->muerte_accidental); ?></td>
	            <td>Invalides permanente</td>
	            <td><?php echo utf8_encode($datos["cobertura"]->invalides_permanente); ?></td>
            </tr>
            <tr>
                <td>Incapacidad temporal</td>
                <td><?php echo utf8_encode($datos["cobertura"]->incapacidad_temporal); ?></td>
                <td>Gastos medicos</td>
                <td><?php echo utf8_encode($datos["cobertura"]->gastos_medicos); ?></td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%">Vida(Solo titular asegurado)</th>
            </tr>
        </thead>
    </table>
    <table style="width: 100%">
        <tbody>
            <tr>
            	<td>Muerte</td>          
	            <td><?php echo utf8_encode($datos["cobertura"]->muerte); ?></td>
	            <td>Muerte accidental o Incapacidad total y permanente</td>
	            <td><?php echo utf8_encode($datos["cobertura"]->ma_it_permanente); ?></td>
            </tr>
            <tr>
                <td>Pago del capital por Incapacidad total y permanente</td>
                <td><?php echo utf8_encode($datos["cobertura"]->pc_it_permanente); ?></td>
                <td>Pago por fallecimiento de familiares</td>
                <td><?php echo utf8_encode($datos["cobertura"]->pago_muerte_familiar); ?></td>
            </tr>
        </tbody>
    </table>
    <!-- fin de los campos de la cobertura -->                
    <!-- inicio de los campos de otros beneficiarios --> 
    <table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%"><?php echo utf8_encode("Beneficiarios en caso de fallescimiento del asegurado titular(aplica para todos los ramos)"); ?></th>
            </tr>
        </thead>
    </table>
	<?php foreach ($datos["beneficiarios"] as $beneficiarios){ ?>
	    <table style="width: 100%">
	        <tbody>
	            <tr>
	                <td>C.I. / Pasaporte</td>
	                <td><?php echo utf8_encode($beneficiarios->ci_pasaporte_beneficiarios); ?></td>
	                <td>Nombres y apellidos</td>
	                <td><?php echo utf8_encode($beneficiarios->nombres_apellidos_beneficiarios); ?></td>
	            </tr>
	        </tbody>
	    </table>
	    <table style="width: 100%">
	        <tbody>
	            <tr>
	                <td>Parentesco</td>
	                <td><?php echo utf8_encode($beneficiarios->parentesco_beneficiarios); ?></td>
	                <td>Distribucion</td>
	                <td><?php echo utf8_encode($beneficiarios->distribucion_beneficiarios); ?></td>
	            </tr>
	        </tbody>
	    </table>
	<?php } ?>
    <!-- fin de los campos de otros beneficiarios --> 
    <!-- inicio de los campos de otros seguros de salud --> 
    <?php if (($datos["otros_seguros_asegurado"] != NULL) && isset($datos["otros_seguros_asegurado"])) { ?>
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th style="width: 100%"><?php echo utf8_encode("Otros seguros de salud en esta u otra compañia"); ?></th>
                    </tr>
                </thead>
            </table>
    <?php } ?>
	<?php foreach ($datos["otros_seguros_asegurado"] as $otros_seguros_asegurado){ ?>
	    <table style="width: 100%">
	        <tbody>
	            <tr>
	                <td>Nombre de la empresa</td>
	                <td><?php echo utf8_encode($otros_seguros_asegurado->nombre_empresa); ?></td>
	                <td>Numero de poliza</td>
	                <td><?php echo utf8_encode($otros_seguros_asegurado->numero_poliza); ?></td>
	            </tr>
	        </tbody>
	    </table>
	    <table style="width: 100%">
	        <tbody>
	            <tr>
	                <td>Monto</td>
	                <td><?php echo utf8_encode($otros_seguros_asegurado->monto); ?></td>
	                <td>Estatus de la poliza</td>
	                <td><?php echo utf8_encode($otros_seguros_asegurado->estado_poliza); ?></td>
	            </tr>
	        </tbody>
	    </table>
	<?php } ?>
    <!-- fin de los campos de otros seguros de salud --> 
    <!-- inicio de los campos de personas que padecieron alguna de las preguntas anteriores --> 
    <?php if(($datos["padecimiento"] != NULL) && isset($datos["padecimiento"])){ ?>
	<table style="width: 100%">
        <thead>
            <tr>
                <th style="width: 100%"><?php echo utf8_encode("Familiar del asegurado o persona del grupo a asegurar que padecio alguna enfermedad mental, del corazon, cancer, diabetes, riñores, tuberculosis, paralisis, apoplejia, hemiplejia, medua, reumatismo o a cometido suicidio"); ?></th>
            </tr>
        </thead>
    </table>
    <?php foreach ($datos["padecimiento"] as $padecimiento){ ?>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td>Nombres y apellidos</td>
                    <td><?php echo utf8_encode($padecimiento->nombres_apellidos_padecimiento); ?></td>
                    <td>Parentesco</td>
                    <td><?php echo utf8_encode($padecimiento->parentesco_padecimiento); ?></td>
                    <td>Edad</td>
                    <td><?php echo utf8_encode($padecimiento->edad_padecimiento); ?></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td>Fallecio</td>
                    <td><?php echo utf8_encode($padecimiento->fallecido_padecimiento); ?></td>
                    <td>Causa</td>
                    <td><?php echo utf8_encode($padecimiento->causa_padecimiento); ?></td>
                </tr>
            </tbody>
        </table>
    <?php } ?>
	<?php } ?>
    <!-- fin de los campos de personas que padecieron alguna de las preguntas anteriores --> 
</body>
</html>