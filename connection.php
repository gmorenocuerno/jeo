
<?php
header(‘Content-type: application/json’);

// Include config.php
include_once('config.php');

if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Get data
	$item = isset($_POST['item']) ? mysql_real_escape_string($_POST['item']) : "";
	
	// Insert data into data base
	$sql = "SELECT A.item, A.item_description, A.item_short_desc, A.item_price, B.brand, B.brand_description,
	C.store, C.soh
	FROM inventory.t_item A
	inner join inventory.t_brand B on B.brand = A.brand
	inner join inventory.p_item_store C on C.item = A.item
	where a.item_status='A' 
	where A.item ="+$item;
	$result = $conn->query($sql);

	if($qur){
		$json = array("status" => 1, "msg" => "Done User added!");
	}else{
		$json = array("status" => 0, "msg" => "Error adding user!");
	}
}else{
	$json = array("status" => 0, "msg" => "Request method not accepted");
}

@mysql_close($conn);

/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);