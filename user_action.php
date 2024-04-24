<?php

//user_action.php

global $connect;
include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
//		$query = "
//		INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status)
//		VALUES (:user_email, :user_password, :user_name, :user_type, :user_status)
//		";
//
//
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':user_email'		=>	$_POST["user_email"],
//				':user_password'	=>	password_hash($_POST["user_password"], PASSWORD_DEFAULT),
//				':user_name'		=>	$_POST["user_name"],
//				':user_type'		=>	'user',
//				':user_status'		=>	'active'
//			)
//		);
//		$result = $statement->fetchAll();
//
//		if(isset($result))
//		{
//			echo 'New User Added';
//		}



        $isError = false;
        $errors = "<ul>";

        $userName = trim($_POST['user_name']);
        if(empty($userName)){
            $isError = true;
            $_SESSION['error_type'] = 'error';
            $errors .= "<li>User Name Field Is Required...!</li>";
        }
        if(isset($_POST['user_name_unique'])){
            $query = "SELECT * FROM user_details WHERE user_name = '$userName'";
            $statement = mysqli_query($connect, $query);
            if($statement->num_rows > 0){
                $isError = true;
                $_SESSION['error_type'] = 'error';
                $errors .= "<li>User Name Already Exist...!</li>";
            }
        }

        $userPassword = $_POST['user_password'];
        if(empty($userPassword)){
            $isError = true;
            $_SESSION['error_type'] = 'error';
            $errors .= "<li>Password Field Is Required...!</li>";
        }elseif(strlen($userPassword) < 6){
            $isError = true;
            $_SESSION['error_type'] = 'error';
            $errors .= "<li>Password Min 6 Char...!</li>";
        }

        $userEmail = trim($_POST["user_email"]);
        if(empty($userEmail)){
            $_SESSION['error_type'] = 'error';
            $isError = true;
            $errors .= "<li>Email Field Is Required...!</li>";
        }elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)){
            $isError = true;
            $errors .= "<li>Invalid email format...!</li>";
        }

        $errors .= "</ul>";

        echo $errors;


        if(!$isError){
            $password_hash = password_hash($_POST["user_password"], PASSWORD_DEFAULT);
            $user_type = 'user';
            $user_status = 'active';
            $query = "
            INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status) 
            VALUES (
                '".$_POST["user_email"]."', 
                '$password_hash', 
                '".$_POST["user_name"]."',
                '$user_type',
                '$user_status'
            )";

            $statement = mysqli_query($connect, $query);

            if($statement) {
                $_SESSION['error_type'] = 'success';
                echo "User inserted successfully.";
            }

        }
    }
	if($_POST['btn_action'] == 'fetch_single')
	{

//        $mypassword = mysqli_real_escape_string($connect,$_POST['user_password']);
//
//        $query = "SELECT * FROM user_details WHERE user_email = '$myusername'";



        $userId = mysqli_real_escape_string($connect, $_POST["user_id"]);
		$query = "SELECT * FROM user_details WHERE user_id = '$userId'";
//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':user_id'	=>	$_POST["user_id"]
//			)
//		);
//		$result = $statement->fetchAll();

        $statement = mysqli_query($connect, $query);
        $result = $statement->fetch_assoc();
//        print_r($result->fetch_assoc());
//        exit();
        foreach($result as $row)
        {
            $output['user_name'] = $result['user_name'];
            $output['user_email'] = $result['user_email'];
        }
//		foreach($result->fetch_assoc() as $row)
//		{
//            print_r($row);
//
//			$output['user_email'] = $row['user_email'];
//			$output['user_name'] = $row['user_name'];
//		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['user_password'] != '')
		{
			$query = "
			UPDATE user_details SET 
				user_name = '".$_POST["user_name"]."', 
				user_email = '".$_POST["user_email"]."',
				user_password = '".password_hash($_POST["user_password"], PASSWORD_DEFAULT)."' 
				WHERE user_id = '".$_POST["user_id"]."'
			";
		}
		else
		{
			$query = "
			UPDATE user_details SET 
				user_name = '".$_POST["user_name"]."', 
				user_email = '".$_POST["user_email"]."'
				WHERE user_id = '".$_POST["user_id"]."'
			";
		}

//		$statement = $connect->prepare($query);
//		$statement->execute();
//		$result = $statement->fetchAll();

        $statement = mysqli_query($connect, $query);

		if($statement)
		{
			echo 'User Details Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'Active';
		if($_POST['status'] == 'Active')
		{
			$status = 'Inactive';
		}
		$query = "
		UPDATE user_details 
		SET user_status = '$status'
		WHERE user_id = '".$_POST['user_id']."'
		";

//		$statement = $connect->prepare($query);
//		$statement->execute(
//			array(
//				':user_status'	=>	$status,
//				':user_id'		=>	$_POST["user_id"]
//			)
//		);
//		$result = $statement->fetchAll();

		if(mysqli_query($connect, $query))
		{
			echo 'User Status change to ' . $status;
		}
	}
}

?>