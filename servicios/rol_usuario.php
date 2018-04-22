<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "sec_rol_usuario";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$usr = isset($_GET['usr']) ? $_GET['usr'] : '';
$rol = utf8_decode(isset($_GET['rol']) ? $_GET['rol'] : '');

$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(!empty($usr)) $usr="A.usr='$usr'";
	else $usr="1=1";
	if(!empty($rol)) $rol="AND A.rol = '$rol'";
	else $rol="";
		
	$sql = "
	SELECT A.usr, A.rol
	FROM $bd.$tabla A
	WHERE $usr $rol ";
	
	//echo $sql;
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"usr" => $row["usr"]
				, 'rol' => utf8_decode($row["rol"])
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
	
		$sql = "INSERT INTO $bd.$tabla(usr, rol, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($usr,$rol, '$user', '$date')";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "DELETE FROM $bd.$tabla WHERE usr = $usr AND rol = $rol";
		
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
