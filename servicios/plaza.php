<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "prc_plaza";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$idEmpresa = isset($_GET['idEmpresa']) ? $_GET['idEmpresa'] : '';
$idTipoContratacion = utf8_decode(isset($_GET['idTipoContratacion']) ? $_GET['idTipoContratacion'] : '');
$idTiempoContratacion = utf8_decode(isset($_GET['idTiempoContratacion']) ? $_GET['idTiempoContratacion'] : '');
$idDepto = utf8_decode(isset($_GET['idDepto']) ? $_GET['idDepto'] : '');
$descripcion = utf8_decode(isset($_GET['descripcion']) ? $_GET['descripcion'] : '');
$salario = utf8_decode(isset($_GET['salario']) ? $_GET['salario'] : '');
$fechaPublicacion = utf8_decode(isset($_GET['fechaPublicacion']) ? $_GET['fechaPublicacion'] : '');
$cantidad = utf8_decode(isset($_GET['cantidad']) ? $_GET['cantidad'] : '');
$fechaContratacion = utf8_decode(isset($_GET['fechaContratacion']) ? $_GET['fechaContratacion'] : '');
$fechaInicio = utf8_decode(isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '');
$fechaFinal = utf8_decode(isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '');

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	if(empty($id)) $id="A.id";
	else $id="'$id'";
	if(empty($idEmpresa)) $id="A.id_empresa";
	else $idEmpresa="AND A.id_empresa = $idEmpresa";
	if(isset($idDepto)) $idDepto="AND A.id_depto=$idDepto";
	if(isset($descripcion)) $descripcion="AND A.descripcion LIKE '%$descripcion%'";
	if(isset($fechaPublicacion)) $fechaPublicacion="AND A.fecha_publicacion=$fechaPublicacion";
	if(isset($fechaContratacion)) $fechaContratacion="AND A.fecha_contratacion=$fechaContratacion";
	
	$sql = "
	SELECT A.id, A.id_empresa, A.id_tipo_contratacion, A.id_tiempo_contratacion, A.id_depto, A.descripcion, A.salario, A.fecha_publicacion
	, A.cantidad, A.fecha_contratacion, A.fecha_inicio, A.fecha_final
	, B.razon_social
	, C.descripcion AS desc_tip_c
	, D.descripcion AS desc_tie_c
	FROM $bd.$tabla A
	INNER JOIN jeo.ctg_empresa B ON B.id = A.id_empresa
	INNER JOIN jeo.ctg_tipo_contratacion C ON C.id = A.id_tipo_contratacion
	INNER JOIN jeo.ctg_tiempo_contratacion D ON D.id = A.id_tiempo_contratacion
	WHERE A.id = $id $idEmpresa ";
	
	//echo $sql;
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id" => $row["id"]
				, 'id_empresa' => $row["id_empresa"]
				, 'id_tipo_contratacion' => $row["id_tipo_contratacion"]
				, 'id_tiempo_contratacion' => $row["id_tiempo_contratacion"]
				, 'id_depto' => $row["id_depto"]
				, 'descripcion' => $row["descripcion"]
				, 'salario' => $row["salario"]
				, 'fecha_publicacion' => $row["fecha_publicacion"]
				, 'cantidad' => $row["cantidad"]
				, 'fecha_contratacion'=>$row["fecha_contratacion"]
				, 'fecha_inicio' => $row["fecha_inicio"]
				, 'fecha_final'=>$row["fecha_final"]
				, 'razon_social'=>$row["razon_social"]
				, 'desc_tipo_contratacion'=>utf8_decode($row["desc_tip_c"])
				, 'desc_tiempo_contratacion'=>utf8_decode($row["desc_tie_c"])
				
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
	
		$sql = "INSERT INTO $bd.$tabla(id, id_empresa, id_tipo_contratacion, id_tiempo_contratacion, id_depto, descripcion, salario, fecha_publicacion
		, cantidad, fecha_contratacion, fecha_inicio, fecha_final, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id, $idEmpresa, $idTipoContratacion, $idTiempoContratacion, $idDepto, '$descripcion', $salario, '$fechaPublicacion'
		, $cantidad, '$fechaContratacion', '$fechaInicio', '$fechaFinal', '$user', '$date')";
		
		//echo $sql."<br>";
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
		$idEmpresa = "id_empresa='".$idEmpresa."'";
		$idTipoContratacion = ", id_tipo_contratacion=".$idTipoContratacion;
		$idTiempoContratacion = ", id_tiempo_contratacion=".$idTiempoContratacion;
		$idDepto = ", id_depto=".$idDepto;
		$descripcion = ", descripcion='".$descripcion."'";
		$salario = ", salario=".$salario;
		$fechaPublicacion = ", fecha_publicacion='".$fechaPublicacion."'";
		$cantidad = ", cantidad='".$cantidad."'";
		$fechaContratacion = ", fecha_contratacion='".$fechaContratacion."'";
		$fechaInicio = ", fecha_inicio='".$fechaInicio."'";
		$fechaFinal = ", fecha_final='".$fechaFinal."'";
		
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $idEmpresa $idTipoContratacion $idTiempoContratacion $idDepto $descripcion $salario $fechaPublicacion 
		$cantidad $fechaContratacion $fechaInicio $fechaFinal $user $date WHERE id = $id";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro actualizado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='D'){// VERIFICACION SI LA ACCION ES ELIMINACION
		$sql = "DELETE FROM $bd.$tabla WHERE id=$id and id_empresa=$idEmpresa";
		
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro eliminado exitosamente.");
		} else {
			$json = array("status"=>0, "error"=>$conn->error);
		}
	}	
}
$conn->close();

/* Output header */
header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
echo json_encode($json);
//*/
 ?>
