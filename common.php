<?PHP
session_start();
    include "lib.php";

    $o =  new option();
    $opt = trim($_REQUEST["opt"]);
    $enable = @trim(@$_REQUEST["enable"]);

    switch($opt){
        case "clean_filter":
            $o->clean_filter();
            break;
        case "get_names":
            $o->get_names();
            break;
        case "model_status":
            $o->model_status();
            break;
    }


class option {
    public function clean_filter(){
        $_SESSION["filter_array"] = null;
    }

    public function get_names(){
        global $mysql;
        $name_array = $mysql->get_names();
        echo json_encode($name_array);
    }

    public function model_status()
    {
        global $enable;
        $_SESSION["model_edit"] = $enable;
    }
}
?>
