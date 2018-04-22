<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "sec_usuario";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$usr = isset($_GET['usr']) ? $_GET['usr'] : '';
$clave = utf8_decode(isset($_GET['clave']) ? $_GET['clave'] : '');

$nombre = utf8_decode(isset($_GET['nombre']) ? $_GET['nombre'] : '');
$apellido = utf8_decode(isset($_GET['apellido']) ? $_GET['apellido'] : '');
$email = utf8_decode(isset($_GET['email']) ? $_GET['email'] : '');

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(!empty($usr)) $usr="A.usr='$usr'";
	else $usr="1=1";
	if(!empty($nombre)) $nombre="AND A.nombre LIKE '%$nombre%'";
	else $nombre="";
	if(!empty($apellido)) $apellido="AND A.apellido LIKE '%$apellido%'";
	else $apellido="";
	if(!empty($estado)) $estado="AND A.estado='$estado'";
	else $estado="";
	
	$sql = "
	SELECT A.usr, A.clave, A.nombre, A.apellido, A.email, A.estado
	FROM $bd.$tabla A
	WHERE $usr $clave $estado ";

	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"usr" => $row["usr"]
				, 'clave' => utf8_decode($row["clave"])
				, 'nombre' => utf8_decode($row["nombre"])
				, 'apellido' => utf8_decode($row["apellido"])
				, 'email' => utf8_decode($row["email"])
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
		
		$date = date('Y-m-d');
	
		$sql = "INSERT INTO $bd.$tabla(usr, clave, nombre, apellido, email, ESTADO, USUARIO_CREACION, FECHA_CREACION) 
		VALUE('$usr','$clave', '$nombre', '$apellido', '$email', 'A', '$user', '$date')";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
	
		if(!empty($clave)) $clave = "clave='".strtoupper($clave)."'";
		else $clave = "clave=clave";
		if(!empty($nombre)) $nombre = ", nombre='".strtoupper($nombre)."'";
		else $nombre = ", nombre=nombre";
		if(!empty($apellido)) $apellido = ", apellido='".strtoupper($apellido)."'";
		else $apellido = ", apellido=apellido";
		if(!empty($email)) $email = ", email='".strtoupper($email)."'";
		else $email = ", email=email";
		
				
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $clave $nombre $apellido $email $estado $user $date WHERE usr = '$usr'";
		
		echo $sql;
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro actualizado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		$sql = "UPDATE $bd.$tabla set estado='I' $user $date WHERE usr = $usr";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro eliminado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='LU'){ //VERIFICACION SI LA ACCION ES CONSULTA DEL LOGIN
			if(!empty($usr)) $usr="A.usr='$usr'";
			else $usr = "A.usr = null";
			if(!empty($estado)) $estado="AND A.estado='A'";
			else $estado="AND A.estado=null";
			
			$sql = "
			SELECT A.usr
			FROM $bd.$tabla A
			WHERE $usr $estado ";

			$result = $conn->query($sql);
			
			if (!empty($result))
				if($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$results[] = array(
						"usr" => $row["usr"]
						);
						$json = array("status"=>1, "info"=>$results);
					}
				} else {
					$json = array("status"=>0, "info"=>"No existe información con ese criterio.");
				}
			else $json = array("status"=>0, "info"=>"No existe información.");
		}
	else if(strtoupper($accion) =='LP'){ //VERIFICACION SI LA ACCION ES CONSULTA DEL PASSWORD
			if(!empty($usr)) $usr="A.usr='$usr'";
			else $usr = "A.usr = null";
			if(!empty($clave)) $clave="A.clave='$clave'";
			else $clave = "AND A.clave = null";
			if(!empty($estado)) $estado="AND A.estado='A'";
			else $estado="AND A.estado=null";
			
			$sql = "
			SELECT A.usr
			FROM $bd.$tabla A
			WHERE $usr $clave $estado ";

			$result = $conn->query($sql);
			
			if (!empty($result))
				if($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$results[] = array(
						"usr" => $row["usr"]
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
