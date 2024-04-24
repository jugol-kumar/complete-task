<?php

//order_fetch.php

global $connect;
include('database_connection.php');

include('function.php');


$query = '';

$output = array();

//SELECT users.username, orders.product, orders.quantity
//FROM users
//INNER JOIN orders ON users.user_id = orders.user_id;
$query = "SELECT inventory_order.*, user_details.user_name FROM inventory_order INNER JOIN user_details ON inventory_order.user_id = user_details.user_id";

if($_SESSION['type'] == 'user') {
    $query .= ' WHERE user_id = "'.$_SESSION["user_id"].'"';
}

if(isset($_POST["search"]["value"])) {
    $query .= ' AND (';
    $query .= ' inventory_order.inventory_order_name LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= ' OR inventory_order.inventory_order_total LIKE "'.$_POST["search"]["value"].'%" ';
    $query .= ' OR inventory_order.inventory_order_status LIKE "'.$_POST["search"]["value"].'%" ';
    $query .= ' OR inventory_order.inventory_order_date LIKE "'.$_POST["search"]["value"].'%" ';
    $query .= ' OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= ')';
}

if(isset($_POST["order"])) {
    $val = intval($_POST['order']['0']['column'])+1;
    $query .= ' ORDER BY '.$val.' '.$_POST['order']['0']['dir'];
} else {
    $query .= ' ORDER BY inventory_order.inventory_order_id DESC ';
}

if($_POST["length"] != -1) {
    $query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}


//$statement = $connect->prepare($query);
//$statement->execute();
//$result = $statement->fetchAll();



$data = array();
$statement = mysqli_query($connect, $query);
$filtered_rows = $statement->num_rows;

//print_r($filtered_rows);
//print_r($statement->fetch_assoc());
//exit();

if($filtered_rows > 0){
    while($row = $statement->fetch_assoc())
{

    if($row){
        $payment_status = '';

        if($row['payment_status'] == 'cash')
        {
            $payment_status = '<span class="label label-primary">Cash</span>';
        }
        else
        {
            $payment_status = '<span class="label label-warning">Credit</span>';
        }

        $status = '';
        if($row['inventory_order_status'] == 'active')
        {
            $status = '<span class="label label-success">Active</span>';
        }
        else
        {
            $status = '<span class="label label-danger">Inactive</span>';
        }

        $dateObj = new DateTime($row['inventory_order_date']);

        $sub_array = array();
        $sub_array[] = $row['inventory_order_id'];
        $sub_array[] = $row['inventory_order_name'];
        $sub_array[] = $row['inventory_order_total'];
        $sub_array[] = $payment_status;
        $sub_array[] = $status;
        $sub_array[] = $dateObj->format("d F Y");
        if($_SESSION['type'] == 'master')
        {
            $sub_array[] = get_user_name($connect, $row['user_id']);
        }
        $sub_array[] = '<a href="view_order.php?pdf=1&order_id='.$row["inventory_order_id"].'" class="btn btn-info btn-xs">View PDF</a>';
        $sub_array[] = '<button type="button" name="update" id="'.$row["inventory_order_id"].'" class="btn btn-warning btn-xs update">Update</button>';
        $sub_array[] = '<button type="button" name="delete" id="'.$row["inventory_order_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["inventory_order_status"].'">Delete</button>';
        $data[] = $sub_array;


    }
}
}
function get_total_all_records($connect)
{
	$statement = mysqli_query($connect, "SELECT * FROM inventory_order");
	return $statement->num_rows;
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);


echo json_encode($output);

?>