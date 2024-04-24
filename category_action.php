<?php

//category_action.php

global $connect;
include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO category (category_name) 
		VALUES ('".$_POST['category_name']."')";

//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':category_name'	=>	$_POST["category_name"]
//			)
//		);
//        $query = $statement->get_result();
//		$result = $query->fetchAll();


        $statement = mysqli_query($connect, $query);

		if(isset($statement))
		{
			echo 'Category Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM category WHERE category_id = '".$_POST['category_id']."'";
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':category_id'	=>	$_POST["category_id"]
//			)
//		);
//		$result = $statement->fetchAll();

        $statement = mysqli_query($connect, $query);
        $result = $statement->fetch_assoc();
		foreach($result as $row)
		{
			$output['category_name'] = $result['category_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE category set category_name = '".$_POST['category_name']."'
		WHERE category_id = '".$_POST['category_id']."'";;
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':category_name'	=>	$_POST["category_name"],
//				':category_id'		=>	$_POST["category_id"]
//			)
//		);
//		$result = $statement->fetchAll();


        $statement = mysqli_query($connect, $query);
//        $result = $statement->fetch_assoc();
		if($statement)
		{
			echo 'Category Name Edited';
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
		UPDATE category 
		SET category_status = '".$status."'
		WHERE category_id = '".$_POST['category_id']."'
		";
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':category_status'	=>	$status,
//				':category_id'		=>	$_POST["category_id"]
//			)
//		);
        $result = mysqli_query($connect, $query);
//		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Category status change to ' . $status;
		}
	}
}

?>