<?php

//order_action.php

global $connect;
include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
        $query = "
		INSERT INTO inventory_order (user_id, inventory_order_total, inventory_order_date, inventory_order_name, inventory_order_address, payment_status, inventory_order_status, inventory_order_created_date) 
		VALUES ('".$_SESSION['usar_id']."', 0, 
		'".$_POST['inventory_order_date']."',  
		'".$_POST['inventory_order_name']."', 
		'".$_POST['inventory_order_address']."', 
		'".$_POST['payment_status']."', 
		'active', 
		'".date("Y-m-d")."'
		)";

//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':user_id'						=>	$_SESSION["user_id"],
//				':inventory_order_total'		=>	0,
//				':inventory_order_date'			=>	$_POST['inventory_order_date'],
//				':inventory_order_name'			=>	$_POST['inventory_order_name'],
//				':inventory_order_address'		=>	$_POST['inventory_order_address'],
//				':payment_status'				=>	$_POST['payment_status'],
//				':inventory_order_status'		=>	'active',
//				':inventory_order_created_date'	=>	date("Y-m-d")
//			)
//		);


//		$result = $statement->fetchAll();
//		$statement = $connect->query("SELECT LAST_INSERT_ID()");
//		$inventory_order_id = $statement->fetchColumn();


        $result = mysqli_query($connect, $query);
        $inventory_order_id = mysqli_insert_id($connect);

		if(isset($inventory_order_id))
		{

//            var_dump('product_id', $id);
//            var_dump($_POST['product_id']);
//            var_dump($key);
//
			$total_amount = 0;
			foreach($_POST["product_id"] as $key => $id)
			{
                $product_details = fetch_product_details(intval($id), $connect);
				$sub_query = "
				INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax) 
				VALUES ('".$inventory_order_id."', $id, '".$_POST["quantity"][$key]."', '".$product_details['price']."',  '".$product_details['tax']."')
				";

/*				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':inventory_order_id'	=>	$inventory_order_id,
						':product_id'			=>	$_POST["product_id"][$count],
						':quantity'				=>	$_POST["quantity"][$count],
						':price'				=>	$product_details['price'],
						':tax'					=>	$product_details['tax']
					)
				);*/



                mysqli_query($connect, $sub_query);

				$base_price = $product_details['price'] * $_POST["quantity"][$key];
				$tax = ($base_price/100)*$product_details['tax'];
				$total_amount = $total_amount + ($base_price + $tax);
			}

			$update_query = "
			UPDATE inventory_order 
			SET inventory_order_total = '".$total_amount."' 
			WHERE inventory_order_id = '".$inventory_order_id."'
			";

//			$statement = $connect->prepare($update_query);
//			$statement->execute();
//			$result = $statement->fetchAll();
            $statement = mysqli_query($connect, $update_query);

			if(isset($statement))
			{
				echo 'Order Created...';
				echo '<br />';
				echo $total_amount;
				echo '<br />';
				echo $inventory_order_id;
			}
		}else{
            echo "Hanve an error";
        }
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM inventory_order WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		";
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':inventory_order_id'	=>	$_POST["inventory_order_id"]
//			)
//		);
//		$result = $statement->fetchAll();

        $query = mysqli_query($connect, $query);
        $row = $query->fetch_assoc();
		$output = array();
//		foreach($result as $row)
//		{
			$output['inventory_order_name'] = $row['inventory_order_name'];
			$output['inventory_order_date'] = $row['inventory_order_date'];
			$output['inventory_order_address'] = $row['inventory_order_address'];
			$output['payment_status'] = $row['payment_status'];
//		}

		$sub_query = "
		SELECT * FROM inventory_order_product 
		WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		";
//		$statement = $connect->prepare($sub_query);
//		$statement->execute();
//		$sub_result = $statement->fetchAll();

        $query = mysqli_query($connect, $sub_query);

		$product_details = '';
		$count = '';
//		foreach($sub_result as $sub_row)
//		{
        while ($sub_row = $query->fetch_assoc()){
			$product_details .= '
			<script>
			$(document).ready(function(){
				$("#product_id'.$count.'").selectpicker("val", '.$sub_row["product_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-8">
						<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
							'.fill_product_list($connect).'
						</select>
						<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$sub_row["product_id"].'" />
					</div>
					<div class="col-md-3">
						<input type="text" name="quantity[]" class="form-control" value="'.$sub_row["quantity"].'" required />
					</div>
					<div class="col-md-1">
			';

			if($count == '')
			{
				$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			else
			{
				$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
			}
			$product_details .= '
						</div>
					</div>
				</div><br />
			</span>
			';

			$count = intval($count) + 1;
		}
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$delete_query = "DELETE FROM inventory_order_product WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'";

//		$statement = $connect->prepare($delete_query);
//		$statement->execute();
//		$delete_result = $statement->fetchAll();

        $statement = mysqli_query($connect, $delete_query);

		if($statement)
		{
            $total_amount = 0;
            foreach($_POST["product_id"] as $key => $id)
            {
                $product_details = fetch_product_details(intval($id), $connect);
                $sub_query = "
				INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax) 
				VALUES ('".$_POST["inventory_order_id"]."', $id, '".$_POST["quantity"][$key]."', '".$product_details['price']."',  '".$product_details['tax']."')
				";
                mysqli_query($connect, $sub_query);

                $base_price = $product_details['price'] * $_POST["quantity"][$key];
                $tax = ($base_price/100)*$product_details['tax'];
                $total_amount = $total_amount + ($base_price + $tax);
            }


			$update_query = "
			UPDATE inventory_order 
			SET inventory_order_name = '".$_POST["inventory_order_name"]."', 
			inventory_order_date = '".$_POST["inventory_order_date"]."', 
			inventory_order_address = '".$_POST["inventory_order_address"]."', 
			inventory_order_total = $total_amount, 
			payment_status = '".$_POST["payment_status"]."'
			WHERE inventory_order_id ='".$_POST["inventory_order_id"]."'
			";




//			$statement = $connect->prepare($update_query);
//			$statement->execute(
//				array(
//					':inventory_order_name'			=>	$_POST["inventory_order_name"],
//					':inventory_order_date'			=>	$_POST["inventory_order_date"],
//					':inventory_order_address'		=>	$_POST["inventory_order_address"],
//					':inventory_order_total'		=>	$total_amount,
//					':payment_status'				=>	$_POST["payment_status"],
//					':inventory_order_id'			=>	$_POST["inventory_order_id"]
//				)
//			);
//			$result = $statement->fetchAll();

			if(mysqli_query($connect, $update_query))
			{
				echo 'Order Edited...';
			}
		}
	}

	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
		}
		$query = "
		UPDATE inventory_order 
		SET inventory_order_status = '$status'
		WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		";
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':inventory_order_status'	=>	$status,
//				':inventory_order_id'		=>	$_POST["inventory_order_id"]
//			)
//		);
//		$result = $statement->fetchAll();
		if(mysqli_query($connect, $query))
		{
			echo 'Order status change to ' . $status;
		}
	}
}

?>