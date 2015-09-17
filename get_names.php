<?PHP
session_start();
    include "lib.php";

    $name_array = $mysql->get_names();
    echo json_encode($name_array);
?>
