<?php
//login.php

global $connect;
include('database_connection.php');

if(isset($_SESSION['type']))
{
	header("location:index.php");
}

$message = '';

if(isset($_POST["login"]))
{

//    $data = mysqli_real_escape_string($connect, $_POST['user_email']);
//    $query = "SELECT * FROM user_details WHERE user_email = '$data'";
    /*$query = "SELECT * FROM user_details WHERE user_email = 'john_smith@example.com'";

    $statement = $connect->prepare($query);

    if ($statement) {
        $statement->bind_param("s", $_POST['user_email']);
        $statement->execute();
        $result = $statement->get_result();

        // You can fetch the result as an associative array if needed
         $row = $result->fetch_assoc();
        print_r($row);
    }



	$statement = $connect->prepare($query);

    $statement->bind_param("s", $_POST["user_email"]);

    $statement->execute();



	$statement->execute(
		array(
				'user_email'	=>	$_POST["user_email"]
			)
	);

    $statement = mysqli_query($connect, $query);
    $result = $statement->fetch_assoc();

	$count = $statement->rowCount();

    $count = $statement->num_rows();*/

    /*
     * Edited By: Jugol Kumar
     * Code Type: Added New Code
     ***************/
    $myusername = mysqli_real_escape_string($connect,$_POST['user_email']);
    $mypassword = mysqli_real_escape_string($connect,$_POST['user_password']);
    $query = "SELECT * FROM user_details WHERE user_email = '$myusername'";


    $statement = mysqli_query($connect,$query);
    $count = mysqli_num_rows($statement);

	if($count > 0)
	{
        $row = $statement->fetch_assoc();

		foreach($row as $key => $item)
		{
			if(isset($row['user_status']) && $row['user_status'] == 'Active')
			{
				if(password_verify($_POST["user_password"], $row["user_password"]))
				{

					$_SESSION['type'] = $row['user_type'];
					$_SESSION['usar_id'] = $row['user_id'];
					$_SESSION['usar_name'] = $row['user_name'];

					header("location:index.php");
				}
				else
				{
					$message = "<label>Wrong Password</label>";
				}
			}
			else
			{
				$message = "<label>Your account is disabled, Contact Master</label>";
			}
		}
	}
	else
	{
		$message = "<label>Wrong Email Address</labe>";
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Inventory Management System using PHP with Ajax Jquery</title>		
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h2 align="center">Inventory Management System using PHP with Ajax Jquery</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form method="post">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Email</label>
							<input type="text" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="user_password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>