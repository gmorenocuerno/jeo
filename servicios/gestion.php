<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "prc_gestion";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$idAplicante = isset($_GET['idAplicante']) ? $_GET['idAplicante'] : '';
$observacion = utf8_decode(isset($_GET['observacion']) ? $_GET['observacion'] : '');
$fechaCita = isset($_GET['fechaCita']) ? $_GET['fechaCita'] : '';
$horaCita = isset($_GET['horaCita']) ? $_GET['horaCita'] : '';

$estado = utf8_decode(isset($_GET['estado']) ? $_GET['estado'] : '');
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(empty($id)) $id="A.id";
	else $id="'$id'";
	if(empty($idAplicante)) $idAplicante=" AND A.id_aplicante=A.id_aplicante";
	else $idAplicante=" AND A.id_aplicante=$idAplicante";
	if(empty($fechaCita)) $fechaCita=" AND A.fecha_cita=A.fecha_cita";
	else $fechaCita=" AND A.fecha_cita=$fechaCita";
	
	$sql = "
	SELECT A.id, A.id_aplicante, A.observacion, A.fecha_cita, A.hora_cita, A.estado
	FROM $bd.$tabla A
	WHERE A.id = $id $idAplicante $fechaCita";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id" => $row["id"]
				, 'id_aplicante' => $row["id_aplicante"]
				, 'observacion' => $row["observacion"]
				, 'fecha_cita' => $row["fecha_cita"]
				, 'hora_cita' => $row["hora_cita"]
				, 'estado' => $row["estado"]
				);
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
	
		$sql = "INSERT INTO $bd.$tabla(id, id_aplicante, observacion, fecha_cita, hora_cita, estado, usuario_creacion, fecha_creacion) 
		VALUE($id, $idAplicante, '$observacion', '$fechaCita', '$horaCita', 'A', '$user', '$date')";

		//echo $sql;
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
	
		
		$observacion = "observacion='".utf8_decode($observacion)."'";
		$fechaCita = ", fecha_cita='".$fechaCita."'";
		$horaCita = ", hora_cita='".$horaCita."'";
		$estado = ", estado='".$estado."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "UPDATE $bd.$tabla SET $observacion $fechaCita $horaCita $estado $user $date WHERE id = $id and id_aplicante = $idAplicante";
		
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
		
		$sql = "UPDATE $bd.$tabla SET estado = 'I' $user $date WHERE id = $id and id_aplicante = $idAplicante";
		
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
