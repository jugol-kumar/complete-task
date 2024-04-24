<?php

//edit_profile.php

global $connect;
include('database_connection.php');

if(isset($_POST['user_name']))
{
    $query = "SELECT * FROM user_details WHERE user_id = '".$_SESSION['usar_id']."'";
    $statment = mysqli_query($connect, $query);
    $data = $statment->fetch_assoc();

if($statment->num_rows > 0){
    if($_POST["user_new_password"] != '')
	{

        if($_POST['user_re_enter_password'] == ''){
            echo '<div class="alert alert-danger">Re-enter Password Field Is Required...!</div>';
        }

        elseif($_POST['user_re_enter_password'] != $_POST["user_new_password"]){
            echo '<div class="alert alert-danger">Re-enter Password Not Matched...!</div>';
        }

        elseif($_POST['user_old_passwrd'] == ''){
            echo '<div class="alert alert-danger">Old Password Field Is Required...!</div>';
        }

        elseif(!password_verify($_POST['user_old_passwrd'], $data["user_password"])){
            echo '<div class="alert alert-danger">Old Password Is Not Valid...!</div>';
        }

        else{
            $query = "
            UPDATE user_details SET 
                user_name = '".$_POST["user_name"]."', 
                user_email = '".$_POST["user_email"]."', 
                user_password = '".password_hash($_POST["user_new_password"], PASSWORD_DEFAULT)."' 
                WHERE user_id = '".$_SESSION['usar_id']."'
            ";

            $statment = mysqli_query($connect, $query);
            echo '<div class="alert alert-success">Profile Edited</div>';
        }

	}

	elseif($_POST["user_new_password"] == '' && $_POST["user_re_enter_password"] == '' && $_POST["user_old_passwrd"] == ''){
		$query = "
		UPDATE user_details SET 
			user_name = '".$_POST["user_name"]."', 
			user_email = '".$_POST["user_email"]."'
			WHERE user_id = '".$_SESSION['usar_id']."'
		";

        $statment = mysqli_query($connect, $query);
        echo '<div class="alert alert-success">Profile Edited</div>';
	}
}
    else{
        echo '<div class="alert alert-danger">User Not Valid...!</div>';
    }


}

?>