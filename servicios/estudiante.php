<?php
include_once('../config.php'); 

$bd = "jeo";
$tabla = "ctg_estudiante";

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$idDepto = isset($_GET['idDepto']) ? $_GET['idDepto'] : '';
$idEstCivil = utf8_decode(isset($_GET['idEstCivil']) ? $_GET['idEstCivil'] : '');
$idNivEstudio = utf8_decode(isset($_GET['idNivEstudio']) ? $_GET['idNivEstudio'] : '');
$carnet = utf8_decode(isset($_GET['carnet']) ? $_GET['carnet'] : '');
$pNombre = utf8_decode(isset($_GET['pNombre']) ? $_GET['pNombre'] : '');
$sNombre = utf8_decode(isset($_GET['sNombre']) ? $_GET['sNombre'] : '');
$tNombre = utf8_decode(isset($_GET['tNombre']) ? $_GET['tNombre'] : '');
$pApellido = utf8_decode(isset($_GET['pApellido']) ? $_GET['pApellido'] : '');
$sApellido = utf8_decode(isset($_GET['sApellido']) ? $_GET['sApellido'] : '');
$cApellido = utf8_decode(isset($_GET['cApellido']) ? $_GET['cApellido'] : '');
$sexo = utf8_decode(isset($_GET['sexo']) ? $_GET['sexo'] : '');
$fechaNac = utf8_decode(isset($_GET['fechaNac']) ? $_GET['fechaNac'] : '');
$dui = utf8_decode(isset($_GET['dui']) ? $_GET['dui'] : '');
$nit = utf8_decode(isset($_GET['nit']) ? $_GET['nit'] : '');
$numCelular = utf8_decode(isset($_GET['numCelular']) ? $_GET['numCelular'] : '');
$numFijo = utf8_decode(isset($_GET['numFijo']) ? $_GET['numFijo'] : '');
$direccion = utf8_decode(isset($_GET['direccion']) ? $_GET['direccion'] : '');
$email = utf8_decode(isset($_GET['email']) ? $_GET['email'] : '');
$graduado = utf8_decode(isset($_GET['graduado']) ? $_GET['graduado'] : '');

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$user = utf8_decode(isset($_GET['user']) ? $_GET['user'] : '');

$json = "no has seteado nada.";

if(strtoupper($accion) =='C'){ //VERIFICACION SI LA ACCION ES CONSULTA
	/*if(empty($id)) $id="A.id = A.id";
	else $id="'$id'";
	//*/
	if(!empty($id)) $id="A.id = $id";
	if(isset($carnet)) $carnet="AND A.carnet = $carnet";
	
	$sql = "
	SELECT A.id, A.id_depto, A.id_est_civil, A.id_niv_estudio, A.carnet
	, A.p_nombre, A.s_nombre, A.t_nombre, A.p_apellido, A.s_apellido, A.c_apellido
	, A.sexo, A.fecha_nacimiento, A.dui, A.nit, A.num_celular, A.num_fijo, A.direccion
	, A.email, A.graduado, A.estado
	FROM $bd.$tabla A
	WHERE $id $carnet AND A.estado='A'";
	
	$result = $conn->query($sql);
	
	if (!empty($result))
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$results[] = array(
				"id" => $row["id"]
				, 'id_depto' => $row["id_depto"]
				, 'id_est_civil' => $row["id_est_civil"]
				, 'id_niv_estudio' => $row["id_niv_estudio"]
				, 'carnet' => utf8_decode($row["carnet"])
				, 'p_nombre' => utf8_decode($row["p_nombre"])
				, 's_nombre' => utf8_decode($row["s_nombre"])
				, 't_nombre' => utf8_decode($row["t_nombre"])
				, 'p_apellido' => utf8_decode($row["p_apellido"])
				, 's_apellido' => utf8_decode($row["s_apellido"])
				, 'c_apellido' => utf8_decode($row["c_apellido"])
				, 'sexo' => $row["sexo"]
				, 'fecha_nacimiento' => $row["fecha_nacimiento"]
				, 'dui' => $row["dui"]
				, 'nit' => $row["nit"]
				, 'num_fijo' => $row["num_fijo"]
				, 'num_celular' => $row["num_celular"]
				, 'direccion' => utf8_decode($row["direccion"])
				, 'email' => utf8_decode($row["email"])
				, 'graduado' => utf8_decode($row["graduado"])
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
		
		$sql = "INSERT INTO $bd.$tabla(ID, id_depto, id_est_civil, id_niv_estudio, carnet
		, p_nombre, s_nombre, t_nombre, p_apellido, s_apellido, c_apellido
		, sexo, fecha_nacimiento, dui, nit, num_celular, num_fijo, direccion, email, graduado
		, ESTADO, USUARIO_CREACION, FECHA_CREACION) 
		VALUE($id, $idDepto, $idEstCivil, $idNivEstudio, $carnet
		, '$pNombre', '$sNombre', '$tNombre', '$pApellido', '$sApellido', '$cApellido'
		, '$sexo', '$fechaNac', '$dui', '$nit', '$numCelular', '$numFijo', '$direccion', '$email', '$graduado'
		, 'A', '$user', '$date')";
		
		//echo $sql."<br>";
		if ($conn->query($sql) === TRUE) {
			$json = array("status"=>1, "info"=>"Registro almacenado exitosamente.");
		} else {
			$json = array("status"=>0, "info"=>$conn->error);
		}
	}
	else if(strtoupper($accion) =='U'){// VERIFICACION SI LA ACCION ES MODIFICACION
		$idDepto = "id_depto=".$idDepto;
		$idEstCivil = ", id_est_civil=".$idEstCivil;
		$idNivEstudio = ", id_niv_estudio=".$idNivEstudio;
		$carnet = ", carnet=".$carnet;
		$pNombre = ", p_nombre='".$pNombre."'";
		$sNombre = ", s_nombre='".$sNombre."'";
		$tNombre = ", t_nombre='".$tNombre."'";
		$pApellido = ", p_apellido='".$pApellido."'";
		$sApellido = ", s_apellido='".$sApellido."'";
		$cApellido = ", c_apellido='".$cApellido."'";
		
		$sexo = ", sexo='".$sexo."'";
		$fechaNac = ", fecha_nacimiento='".$fechaNac."'";
		$dui = ", dui='".$dui."'";
		$nit = ", nit='".$nit."'";
		
		$numCelular = ", num_celular='".$numCelular."'";
		$numFijo = ", num_fijo='".$numFijo."'";
		$direccion = ", direccion='".$direccion."'";
		$email = ", email='".$email."'";
		$graduado = ", graduado='".$graduado."'";
		
		$estado = ", estado='".strtoupper($estado)."'";
		$user = ", usuario_modificacion='".$user."'";
		$date = ", fecha_modificacion='".date('Y-m-d')."'";
		
		
		$sql = "UPDATE $bd.$tabla SET $idDepto $idEstCivil $idNivEstudio $carnet 
		$pNombre $sNombre $tNombre $pApellido $sApellido $cApellido 
		$sexo $fechaNac $dui $nit $graduado
		$estado $user $date WHERE id = $id";
		
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
