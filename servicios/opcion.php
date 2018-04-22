<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "sec_opcion";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$id_opc_ppal = isset($_GET['id_opc_ppal']) ? $_GET['id_opc_ppal'] : '';
$id_opc_padre = isset($_GET['id_opc_padre']) ? $_GET['id_opc_padre'] : '';
$padre = isset($_GET['padre']) ? $_GET['padre'] : '';

$desc = utf8_decode(isset($_GET['desc']) ? $_GET['desc'] : '');
$url = utf8_decode(isset($_GET['url']) ? $_GET['url'] : '');

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(!empty($id)) $id="A.id='$id'";
	else $id="1=1";
	if(!empty($id_opc_ppal)) $id_opc_ppal="and A.id_opc_principal='$id_opc_ppal'";
	else $id_opc_ppal="and 1=1";
	if(!empty($id_opc_padre)) $id_opc_padre="and A.id_opc_padre='$id_opc_padre'";
	else $id_opc_padre="and 1=1";
	
	if(!empty($desc)) $desc="AND A.descripcion LIKE '%$desc%'";
	else $desc="";
	if(!empty($estado)) $estado="AND A.estado='$estado'";
	else $estado="";
	
	$sql = "
	SELECT A.id, A.id_opc_principal, A.id_opc_padre, A.padre, A.descripcion, A.url, A.estado
	FROM $bd.$tabla A
	WHERE $id $id_opc_ppal $id_opc_padre $desc $estado ";
	
	//echo $sql;
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
				, 'estado'=>$row["estado"]
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
	
		$sql = "INSERT INTO $bd.$tabla(ID, id_opc_principal, id_opc_padre, padre, DESCRIPCION, url, ESTADO, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id,$id_opc_ppal,$id_opc_padre,$padre,'$desc', '$url', 'A', '$user', '$date')";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
		$id_opc_ppal = "id_opc_principal=".strtoupper($id_opc_ppal);
		$id_opc_padre = ", id_opc_padre=".strtoupper($id_opc_padre);
		$padre = ", padre=".strtoupper($padre);
		$desc = ", descripcion='".strtoupper($desc)."'";
		$url = ", url='".$url."'";
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $id_opc_ppal $id_opc_padre $padre $desc $url $estado $user $date WHERE id = $id";
		
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
