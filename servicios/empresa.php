<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "ctg_empresa";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$idSector = isset($_GET['idSector']) ? $_GET['idSector'] : '';
$razonSocial = utf8_decode(isset($_GET['razonSocial']) ? $_GET['razonSocial'] : '');
$nombreContacto = utf8_decode(isset($_GET['nombreContacto']) ? $_GET['nombreContacto'] : '');
$apellidoContacto = utf8_decode(isset($_GET['apellidoContacto']) ? $_GET['apellidoContacto'] : '');
$emailContacto = utf8_decode(isset($_GET['emailContacto']) ? $_GET['emailContacto'] : '');
$direccion = utf8_decode(isset($_GET['direccion']) ? $_GET['direccion'] : '');
$telFijo = utf8_decode(isset($_GET['telFijo']) ? $_GET['telFijo'] : '');
$telCelular = utf8_decode(isset($_GET['telCelular']) ? $_GET['telCelular'] : '');


$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(empty($id)) $id="A.id";
	else $id="'$id'";
	if(isset($razonSocial)) $razonSocial="AND A.razon_social LIKE '%$razonSocial%'";
	
	$sql = "
	SELECT A.id, A.id_sector, A.razon_social, A.nombre_contacto, A.apellido_contacto, A.email, A.direccion, A.tel_fijo, A.tel_celular, A.estado
	FROM $bd.$tabla A
	WHERE A.id = $id AND A.estado='A' $razonSocial";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id" => $row["id"]
				, 'id_sector' => $row["id_sector"]
				, 'razon_social' => $row["razon_social"]
				, 'nombre_contacto' => $row["nombre_contacto"]
				, 'apellido_contacto' => $row["apellido_contacto"]
				, 'email' => $row["email"]
				, 'direccion' => $row["direccion"]
				, 'tel_fijo' => $row["tel_fijo"]
				, 'tel_celular' => $row["tel_celular"]
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
	
		$sql = "INSERT INTO $bd.$tabla(ID, ID_SECTOR, RAZON_SOCIAL, NOMBRE_CONTACTO, APELLIDO_CONTACTO, EMAIL_CONTACTO, DIRECCION, TEL_FIJO, TEL_CELULAR, ESTADO, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id, $idSector, '$razonSocial', '$nombreContacto', '$apellidoContacto', '$emailContacto', '$direccion', '$telFijo', '$telCelular', 'A', '$user', '$date')";
		
		echo $sql."<br>";
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
	
		$razonSocial = "RAZON_SOCIAL='".$razonSocial."'";
		$nombreContacto = ", NOMBRE_CONTACTO='".$nombreContacto."'";
		$apellidoContacto = ", APELLIDO_CONTACTO='".$apellidoContacto."'";
		$emailContacto = ", EMAIL_CONTACTO='".$emailContacto."'";
		$direccion = ", DIRECCION='".$direccion."'";
		$telFijo = ", TEL_FIJO='".$telFijo."'";
		$telCelular = ", TEL_CELULAR='".$telCelular."'";
		
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $razonSocial $nombreContacto $apellidoContacto $emailContacto $direccion $telFijo $telCelular 
		$estado $user $date WHERE id = $id";
		
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
