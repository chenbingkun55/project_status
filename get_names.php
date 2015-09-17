<?PHP
#ifundef PROJECT_STATUS
    die("access deny.");
#else
session_start();
    include "lib.php";

    $name_array = $mysql->get_names();
    echo json_encode($name_array);
#endif
?>
