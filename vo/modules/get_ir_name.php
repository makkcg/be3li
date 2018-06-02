<?php
$sql = "SELECT CONCAT(title, ' ', f_name, ' ', l_name, ' ') AS name from ir where ir_id = '" . $_GET['id'] . "'"; 
$result = $database_manager->query($sql);
if ($row = mysqli_fetch_assoc($result)){
    echo $row['name'];
}else {
    echo "Wrong IR ID";
}


?>