<?php

//user_fetch.php

global $connect;
include('database_connection.php');



$query = '';

$output = array();

$query .= "
SELECT * FROM user_details 
WHERE user_type = 'user' AND 
";

if(isset($_POST["search"]["value"]))
{
	$query .= '(user_email LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_status LIKE "%'.$_POST["search"]["value"].'%") ';
}



if(isset($_POST["order"]))
{
    $val = intval($_POST['order']['0']['column'])+1;

    $query .= 'ORDER BY ' .$val. ' ' . $_POST['order']['0']['dir'] . ' ';
}
else
{
	$query .= 'ORDER BY user_id DESC ';
}

if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}


$statement = mysqli_query($connect, $query);

$data = array();

$filtered_rows = $statement->num_rows;

//$statement = $connect->prepare($query);
//
//$statement->execute();
//
//$result = $statement->fetchAll();

if ($statement->num_rows > 0){

    while($row = $statement->fetch_assoc())
    {
        if($row){
            $status = '';
            if($row["user_status"] == 'Active')
            {
                $status = '<span class="label label-success">Active</span>';
            }
            else
            {
                $status = '<span class="label label-danger">Inactive</span>';
            }
            $sub_array = array();
            $sub_array[] = $row['user_id'];
            $sub_array[] = $row['user_email'];
            $sub_array[] = $row['user_name'];
            $sub_array[] = $status;
            $sub_array[] = '<button type="button" name="update" id="'.$row["user_id"].'" class="btn btn-warning btn-xs update">Update</button>';
            $sub_array[] = '<button type="button" name="delete" id="'.$row["user_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["user_status"].'">Delete</button>';
            $data[] = $sub_array;

        }
    }

    $output = array(
        "draw"				=>	intval($_POST["draw"]),
        "recordsTotal"  	=>  $filtered_rows,
        "recordsFiltered" 	=> 	get_total_all_records($connect),
        "data"    			=> 	$data
    );
}

//print_r($output);
//exit();

echo json_encode($output);

function get_total_all_records($connect)
{
    $statement = mysqli_query($connect, "SELECT * FROM user_details WHERE user_type='user'");
	return $statement->fetch_row();
}

?>