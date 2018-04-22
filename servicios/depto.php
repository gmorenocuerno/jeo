<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "ctg_depto";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$desc = utf8_decode(isset($_GET['desc']) ? $_GET['desc'] : '');
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(!empty($id)) $id="A.id='$id'";
	else $id="1=1";
	if(!empty($desc)) $desc="AND A.descripcion LIKE '%$desc%'";
	else $desc="";
	if(!empty($estado)) $estado="AND A.estado='$estado'";
	else $estado="";
	
	$sql = "
	SELECT A.id, A.descripcion, A.estado
	FROM $bd.$tabla A
	WHERE $id $desc $estado ";

	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array("id" => $row["id"], 'descripcion' => utf8_decode($row["descripcion"]), 'estado'=>$row["estado"]);
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
	
		$sql = "INSERT INTO $bd.$tabla(ID, DESCRIPCION, ESTADO, USUARIO_CREACION, FECHA_CREACION) VALUE($id,'$desc', 'A', '$user', '$date')";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
	
		$desc = "descripcion='".strtoupper($desc)."'";
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $desc $estado $user $date WHERE id = $id";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro actualizado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "UPDATE $bd.$tabla set estado='I' $user $date WHERE id = $id";
		
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
