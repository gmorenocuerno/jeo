<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "prc_curso";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$idEstudiante = isset($_GET['idEstudiante']) ? $_GET['idEstudiante'] : '';
$nombre = utf8_decode(isset($_GET['nombre']) ? $_GET['nombre'] : '');
$desc = utf8_decode(isset($_GET['desc']) ? $_GET['desc'] : '');
$institucion = utf8_decode(isset($_GET['institucion']) ? $_GET['institucion'] : '');
$emailContacto = utf8_decode(isset($_GET['emailContacto']) ? $_GET['emailContacto'] : '');
$direccion = utf8_decode(isset($_GET['direccion']) ? $_GET['direccion'] : '');
$fechaInicio = utf8_decode(isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '');
$fechaFin = utf8_decode(isset($_GET['fechaFin']) ? $_GET['fechaFin'] : '');

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(empty($id)) $id="A.id";
	else $id="'$id'";
	if(isset($idEstudiante)) $idEstudiante="AND A.id_estudiante LIKE '%$idEstudiante%'";
	if(isset($nombre)) $nombre="AND A.nombre LIKE '%$nombre%'";
	if(isset($institucion)) $institucion="AND A.institucion LIKE '%$institucion%'";
	
	$sql = "
	SELECT A.id, A.id_estudiante, A.nombre, A.descripcion, A.email_contacto
	, A.institucion, A.direccion_institucion, A.fecha_inicio, A.fecha_fin, A.estado
	FROM $bd.$tabla A
	WHERE A.id = $id $idEstudiante $nombre $institucion AND A.estado='A'";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id" => $row["id"]
				, 'id_estudiante' => $row["id_estudiante"]
				, 'nombre' => $row["nombre"]
				, 'descripcion' => utf8_decode($row["descripcion"])
				, 'email_contacto' => utf8_decode($row["email_contacto"])
				, 'institucion' => utf8_decode($row["institucion"])
				, 'direccion_institucion' => utf8_decode($row["direccion_institucion"])
				, 'fecha_inicio' => $row["fecha_inicio"]
				, 'fecha_fin' => $row["fecha_fin"]
				, 'estado'=>$row["estado"]);
				$json = array("status"=>1, "info"=>$results);
			}
		} else {
			$json = array("status"=>0, "info"=>"No existe información con ese criterio.");
		}
	else $json = array("status"=>0, "info"=>"No existe información.");
}
else{
	if(strtoupper($accion) =='I'){// VERIFICACION SI LA ACCION ES INSERCION
		$sql = "
		SELECT MAX(a.id) + 1 as id
		FROM $bd.$tabla a";
		
		$result = $conn->query($sql);
		
		if (!empty($result) || !is_null($result)){
			if ($result->num_rows > 0) {
				
				while($row = $result->fetch_assoc()) {
					if(!is_null($row["id"])) $id=$row["id"];
					else $id=1;
				}
			} else {
				$id=1;
			}
		}
		else $id=1;
		$date = date('Y-m-d');
		
		$sql = "INSERT INTO $bd.$tabla(ID, id_estudiante, nombre, descripcion, email_contacto
		, institucion, direccion_institucion, fecha_inicio, fecha_fin
		, ESTADO, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id, $idEstudiante, '$nombre', '$desc', '$emailContacto'
		, '$institucion', '$direccion', '$fechaInicio', '$fechaFin'
		, 'A', '$user', '$date')";
		
		//echo $sql."<br>";
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
		
		$nombre = " nombre='".$nombre."'";
		$desc = ", descripcion='".$desc."'";
		$institucion = ", institucion='".$institucion."'";
		$emailContacto = ", email_contacto='".$emailContacto."'";
		$direccion = ", direccion_institucion='".$direccion."'";
		$fechaInicio = ", fecha_inicio='".$fechaInicio."'";
		$fechaFin = ", fecha_fin='".$fechaFin."'";
		
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $nombre $desc $institucion 
		$emailContacto $direccion $fechaInicio $fechaFin 
		$estado $user $date WHERE id = $id and id_estudiante=$idEstudiante";
		
		//echo $sql;
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro actualizado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "UPDATE $bd.$tabla set estado='I' $user $date WHERE id = $id and id_estudiante=$idEstudiante";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro eliminado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}	
}
$conn->close();

/* Output header */
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($json);
//*/
 ?>
