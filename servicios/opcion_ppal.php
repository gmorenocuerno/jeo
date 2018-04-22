<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "sec_opc_principal";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$desc = utf8_decode(isset($_GET['desc']) ? $_GET['desc'] : '');
$menu_icon = utf8_decode(isset($_GET['menu_icon']) ? $_GET['menu_icon'] : '');

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(!empty($id)) $id="A.id='$id'";
	else $id="1=1";
	if(!empty($menu_icon)) $desc="AND A.menu_icon LIKE '%$menu_icon%'";
	else $menu_icon="";
	if(!empty($desc)) $desc="AND A.descripcion LIKE '%$desc%'";
	else $desc="";
	if(!empty($estado)) $estado="AND A.estado='$estado'";
	else $estado="";
	
	$sql = "
	SELECT A.id, A.descripcion, A.menu_icon, A.estado
	FROM $bd.$tabla A
	WHERE $id $menu_icon $desc $estado ";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id" => $row["id"]
				, 'descripcion' => utf8_decode($row["descripcion"])
				, 'menu_icon' => utf8_decode($row["menu_icon"])
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
	
		$sql = "INSERT INTO $bd.$tabla(ID, DESCRIPCION, menu_icon, ESTADO, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id,'$desc', '$menu_icon', 'A', '$user', '$date')";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
		$descripcion = "descripcion='".$descripcion."'";
		$menu_icon = ", menu_icon='".$menu_icon."'";
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $desc $menu_icon $estado $user $date WHERE id = $id";
		
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
