<?PHP
/**
* Author: @-ChenBk <chenbingkun55@163.com> 2015-09-19 16:30
* Version Info ------------------------------------------------
e225fb6 * 发布版本 0.1
:000000 100644 0000000... 02ccee6... A	common.php
* -------------------------------------------------------------
*
*
**/

session_start();
    include "lib.php";

    $o =  new option();
    $opt = trim($_REQUEST["opt"]);
    $enable = @trim(@$_REQUEST["enable"]);

    switch($opt){
        case "clean_filter":
            $o->clean_filter();
            break;
        case "save_filter":
            $o->save_filter();
            break;
        case "unsave_filter":
            $o->unsave_filter();
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

    public function save_filter()
    {
        $save_file = fopen("global_filter.set","w") or die("Unable to open file!");
        if(is_array($_SESSION["filter_array"])){
            $filter_json = json_encode($_SESSION["filter_array"]);
            fwrite($save_file, $filter_json);
        }
        fclose($save_file);
    }

    public function unsave_filter()
    {
        $save_file = fopen("global_filter.set","w") or die("Unable to open file!");
        fwrite($save_file, "");
        fclose($save_file);
    }

    public function get_names(){
        global $mysql;
        $name_array = $mysql->get_names();
        echo json_encode($name_array);
    }

    public function model_status()
    {
        global $enable;
        $_SESSION["model_edit"] = (strcmp($enable,"1") == 0) ? true : false;
    }
}
?>
