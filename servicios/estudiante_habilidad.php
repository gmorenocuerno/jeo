<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "prc_estudiante_habilidad";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$idEstudiante = utf8_decode(isset($_GET['idEstudiante']) ? $_GET['idEstudiante'] : '');
$desc = isset($_GET['desc']) ? $_GET['desc'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(empty($id)) $id="A.id";
	else $id="'$id'";
	if(empty($idEstudiante)) $idEstudiante=" AND A.id_estudiante=A.id_estudiante";
	else $idEstudiante=" AND A.id_estudiante=$idEstudiante";
	if(isset($desc)) $desc=" AND A.descripcion LIKE '%$desc%'";
	
	$sql = "
	SELECT A.id, A.id_estudiante, A.descripcion
	FROM $bd.$tabla A
	WHERE A.id = $id $idEstudiante $desc";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array("id" => $row["id"]
				, 'id_estudiante' => $row["id_estudiante"]
				, 'descripcion' => $row["descripcion"]
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
	
		$sql = "INSERT INTO $bd.$tabla(id, id_estudiante, descripcion, usuario_creacion, fecha_creacion) 
		VALUE($id, $idEstudiante, '$desc', '$user', '$date')";
		
		//echo $sql;
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
	
		$desc = "descripcion='".$desc."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $desc $user $date WHERE id = $id and id_estudiante = $idEstudiante";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro actualizado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "DELETE FROM $bd.$tabla WHERE id = $id and id_estudiante = $idEstudiante";
		
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
