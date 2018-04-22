<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "sec_opc_rol";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id_opc_ppal = isset($_GET['id_opc_ppal']) ? $_GET['id_opc_ppal'] : '';
$id_opc = utf8_decode(isset($_GET['id_opc']) ? $_GET['id_opc'] : '');
$id_rol = utf8_decode(isset($_GET['id_rol']) ? $_GET['id_rol'] : '');

$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(!empty($id_opc)) $id_opc="A.id_opc='$id_opc'";
	else $id_opc="1=1";
	if(!empty($id_opc_ppal)) $id_opc_ppal="AND A.id_opc_ppal='$id_opc_ppal'";
	else $id_opc_ppal="AND 1=1";
	if(!empty($id_rol)) $id_rol="AND A.id_rol = '$id_rol'";
	else $id_rol="AND 1=1";
		
	$sql = "
	SELECT A.id_opc_ppal, A.id_opc, A.id_rol
	FROM $bd.$tabla A
	WHERE $id_opc $id_opc_ppal $id_rol ";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id_opc_ppal" => $row["id_opc_ppal"]
				, 'id_opc' => utf8_decode($row["id_opc"])
				, 'id_rol' => utf8_decode($row["id_rol"])
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
		
		$date = date('Y-m-d');
	
		$sql = "INSERT INTO $bd.$tabla(id_opc_ppal, id_opc, id_rol, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id_opc_ppal, $id_opc, $id_rol, '$user', '$date')";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES ACTUALIZACION
		$id_opc_ppal = "id_opc_ppal=".$id_opc_ppal;
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "UPDATE $bd.$tabla SET $id_opc_ppal $user $date WHERE id_opc = $id_opc AND id_rol = $id_rol";
		echo $sql;
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro eliminado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "DELETE FROM $bd.$tabla WHERE id_opc = $id_opc AND id_rol = $id_rol";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro eliminado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='OP'){// VERIFICACION SI LA ACCION DE LAS OPCIONES PRINCIPALES

		$sql = "SELECT DISTINCT opcppal.*  
		FROM jeo.sec_opc_principal opcppal
		INNER JOIN jeo.sec_opc_rol opcr ON opcr.id_opc_ppal = opcppal.id
		INNER JOIN jeo.sec_rol_usuario rusr ON rusr.usr = '$user' AND rusr.rol = opcr.id_rol
		WHERE opcppal.estado = 'A'";
		
		$result = $conn->query($sql);
		
		if (!empty($result))
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$results[] = array(
					"id" => $row["id"]
					, 'descripcion' => utf8_decode($row["descripcion"])
					, 'menu_icon' => utf8_decode($row["menu_icon"])
					);
					$json = array("status"=>1, "info"=>$results);
				}
			} else {
				$json = array("status"=>0, "info"=>"No existe información con ese criterio.");
			}
		else $json = array("status"=>0, "info"=>"No existe información.");
	}
	else if(strtoupper($accion) =='OU'){// VERIFICACION SI LA ACCION DE LAS OPCIONES DE LOS USUARIOS
	
		$sql = "SELECT DISTINCT opc.*  
		FROM jeo.sec_rol_usuario rusr
		INNER JOIN jeo.sec_opc_rol opcr ON opcr.id_rol = rusr.rol
		INNER JOIN jeo.sec_opcion opc ON opc.id = opcr.id_opc AND COALESCE(opc.id_opc_principal, -1) = COALESCE(opcr.id_opc_ppal, -1)
		AND opc.estado = 'A'
		INNER JOIN jeo.sec_usuario us ON us.usr = rusr.usr AND us.estado = 'A' AND us.usr = '$user'";
		
		$result = $conn->query($sql);
		
		if (!empty($result))
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$results[] = array(
					"id" => $row["id"]
					, 'id_opc_principal' => utf8_decode($row["id_opc_principal"])
					, 'id_opc_padre' => utf8_decode($row["id_opc_padre"])
					, 'padre' => utf8_decode($row["padre"])
					, 'descripcion' => utf8_decode($row["descripcion"])
					, 'url' => utf8_decode($row["url"])
					);
					$json = array("status"=>1, "info"=>$results);
				}
			} else {
				$json = array("status"=>0, "info"=>"No existe información con ese criterio.");
			}
		else $json = array("status"=>0, "info"=>"No existe información.");
	}
}
$conn->close();

/* Output header */
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($json);
//*/
 ?>
