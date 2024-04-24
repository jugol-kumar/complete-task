<?php

//category_fetch.php

global $connect;
include('database_connection.php');

$query = '';

$output = array();

$query .= "SELECT * FROM category ";

if(isset($_POST["search"]["value"]))
{
    $query .= 'WHERE category_name LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= 'OR category_status LIKE "%'.$_POST["search"]["value"].'%" ';
}

$columns = array(
    0 => 'category_id',
    1 => 'category_name',
    2 => 'category_status'
);

// Build the ORDER BY clause using the column name mapped from $_POST['order']['0']['column']
if(isset($_POST['order']))
{
    $query .= 'ORDER BY ' . $columns[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
}
else
{
    $query .= 'ORDER BY category_id DESC ';
}

if($_POST['length'] != -1)
{
    $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();
$result = $statement->get_result();

$data = array();

$filtered_rows = $statement->num_rows;


while($row = $result->fetch_assoc())
{
    if($row){

        $status = '';
        if($row['category_status'] == 'active')
        {
            $status = '<span class="label label-success">Active</span>';
        }
        else
        {
            $status = '<span class="label label-danger">Inactive</span>';
        }
        $sub_array = array();
        $sub_array[] = $row['category_id'];
        $sub_array[] = $row['category_name'];
        $sub_array[] = $status;
        $sub_array[] = '<button type="button" name="update" id="'.$row["category_id"].'" class="btn btn-warning btn-xs update">Update</button>';
        $sub_array[] = '<button type="button" name="delete" id="'.$row["category_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["category_status"].'">Delete</button>';
        $data[] = $sub_array;
    }
}

$output = array(
    "draw"          =>  intval($_POST["draw"]),
    "recordsTotal"      =>  $filtered_rows,
    "recordsFiltered"   =>  get_total_all_records($connect),
    "data"              =>  $data
);

function get_total_all_records($connect)
{

    $statement = mysqli_query($connect, "SELECT * FROM category");
    return $statement->fetch_row();
//    $statement = $connect->prepare("SELECT * FROM category");
//    $statement->execute();
//    $result = $statement->get
//    return $statement->;
}

echo json_encode($output);


?>