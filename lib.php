<?PHP
/**
* Author: @-ChenBk <chenbingkun55@163.com> 2015-09-19 16:30
* Version Info ------------------------------------------------
4432a9b * 项目只有一个时,get_names()不能正常工作
:100644 100644 5e41ac6... 4e94b09... M	lib.php
e225fb6 * 发布版本 0.1
:100644 100644 a478a44... 5e41ac6... M	lib.php
78a822c * 时间过滤，Color过滤功能。
:100644 100644 ab520e2... a478a44... M	lib.php
316058b * filter 过滤己删除和己完成.
:100644 100644 500ee9d... ab520e2... M	lib.php
14d3915 * filter 完成一半, stage date 这块搜索比较困难.
:100644 100644 5c8398d... 500ee9d... M	lib.php
366c0ce * 修改Export Excel时不加载css样式.
:100644 100644 4cccd69... 5c8398d... M	lib.php
c184bcd * 修复搜索面板id和添加修改冲突BUG
:100644 100644 b0347eb... 4cccd69... M	lib.php
3c503c1 * 添加搜垵面板。
:100644 100644 a557d9d... b0347eb... M	lib.php
cebfe57 * 添加index.php导出Excel功能。
:100644 100644 aa27f3f... a557d9d... M	lib.php
b0e12bd * 添加index.php改出Excel。
:100644 100644 aa27f3f... a557d9d... M	lib.php
49c6355 + jquery.datepicker 日期选择器。 + get_names.php 取项目名称下拉列表。
:100644 100644 18c9375... aa27f3f... M	lib.php
598952c * 基本功能完成.
:100644 100644 190ae5e... 18c9375... M	lib.php
f04540a * 加载 tablestore * 添加、删除、修改功能
:100644 100644 eea5fea... 190ae5e... M	lib.php
5d6ab00 * 初始化Dev
:000000 100644 0000000... eea5fea... A	lib.php
* -------------------------------------------------------------
*
*
**/

$config = array();
$config["SITE_NAME"] = "Project Status";
$config["WHITE_IP"] = array("127.0.0.1"); // 添加IP访问白名单.
$config["WHITE_NET"] = array("192.168.23","192.168.1","10.6.0","10.4.0"); // 可以添加其它网段进来,只需要用到前面3个段位.
$config["ADMIN_IP"] = array(
    "10.4.0.4",
    "10.0.17.3",
    "10.0.17.120",
    "10.0.17.103",
    "10.0.17.115",
    "127.0.0.1"); // 添加可编辑Admin IP权限.

// DB 配置
$config["DB_HOST"] = "localhost";
$config["DB_USER"] = "root";
$config["DB_PASSWORD"] = "123456";
$config["DB_NAME"] = "test";
$config["DB_TABLE"] = "project_status";
// STAGE 配置
$config["STAGE"] = array("0" => "PreDEV","1" => "DEV","2" => "PreAlpha","3" => "Production");

class mysql_lib {
    var $con;
    public function conn_db(){
        global $config;

        $this->con = @mysql_connect($config["DB_HOST"],$config["DB_USER"],$config["DB_PASSWORD"]);
        if(!$this->con) {
            die('could not connect: '. mysql_error());
        }
        $db_selected = mysql_select_db($config["DB_NAME"],$this->con);
        if(!$db_selected) {
            die('could use '.$config["DB_NAME"].': '. mysql_error());
        }
    }

    public function close_db(){
        mysql_close($this->con);
    }

    public function query($sql){
        mysql_ping($this->con);

        $re = mysql_query($sql,$this->con);
        return $re;
    }

    public function query_one($sql){
        mysql_ping($this->con);

        mysql_query($sql,$this->con);
    }

    public function to_array($result,$bool=false){
        $return_array = array();

        while($row=mysql_fetch_row($result)){
            $tmp_array = array();
            for($i= 0; $i < count($row);$i++){
                $filed_name = mysql_field_name($result, $i);
                $tmp_array[$filed_name] = $row[$i];
            }

            if(mysql_num_rows($result) == 1 && $bool ){
                return $tmp_array;
            }

            array_push($return_array, $tmp_array);
        }
        return $return_array;
    }

    public function get_all(){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"];
        $re = $this->query($sql);

        return $this->to_array($re);
    }

    public function get_deleted(){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"]." where deleted != 0";
        $re = $this->query($sql);

        return $this->to_array($re);
    }

    public function get_finish(){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"]." where finish != 0";
        $re = $this->query($sql);

        return $this->to_array($re);
    }

    public function get_in_process(){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"]." where finish = 0 AND deleted = 0";
        $re = $this->query($sql);

        return $this->to_array($re);
    }

    public function get_names(){
        global $config;
        $sql = "select name from ".$config["DB_TABLE"]." group by name";
        $re = $this->query($sql);

        return $this->to_array($re);
    }

    public function find($id){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"]." where id = ".$id;
        $re = $this->query($sql);

        return $this->to_array($re,true);
    }

    public function finish($id){
        global $config;

        $finish_row = $this->find($id);
        $finish = ($finish_row["finish"] != 0) ? 0 : 1;
        $sql = "UPDATE ".$config["DB_TABLE"]." SET finish = ".$finish." where id = ".$id;

        return $this->query_one($sql);
    }

    public function deleted($id){
        global $config;

        $delete_row = $this->find($id);
        $deleted = ($delete_row["deleted"] != 0) ? 0 : 1;

        $sql = "UPDATE ".$config["DB_TABLE"]." SET deleted = ".$deleted." where id = ".$id;

        return $this->query_one($sql);
    }

    public function filter($load_filter = false,$filter_stage="",$filter_status=""){
        global $config;
        $from_array = array();
        $get_db_array = array();
        $db_array = array();
        $return_array = array();
        $stage_json = new stage_date_json();

        // 过滤变量
        $name = "";
        $theme_function = "";
        $version = "";
        $status = "";
        $stage = "";
        $note = "";
        $include_deleted = "";
        $include_finish = "";
        $note_empty = "";

        if(strcmp(@$_REQUEST["filter"],"1") == 0 || $load_filter){
            $name = $_SESSION["filter_array"]["name"];
            $theme_function = $_SESSION["filter_array"]["theme_function"];
            $version = $_SESSION["filter_array"]["version"];
            $status = $_SESSION["filter_array"]["status"];
            $stage = $_SESSION["filter_array"]["stage"];
            $note = $_SESSION["filter_array"]["note"];
            $include_deleted = $_SESSION["filter_array"]["include_deleted"];
            $include_finish = $_SESSION["filter_array"]["include_finish"];
            $note_empty = $_SESSION["filter_array"]["note_empty"];
            $from_array = $stage_json->decode($_SESSION["filter_array"]["stage_date_json"]);
        } else if(strcmp($filter_stage,"") != 0) {
            $stage = $filter_stage;
        } else if(strcmp($filter_status,"") != 0) {
            $status = $filter_status;
        } else {
            $id = trim(@$_REQUEST['id']);
            $name = trim(@$_REQUEST['name']);
            $theme_function = trim(@$_REQUEST['theme_function']);
            $version = trim(@$_REQUEST['version']);
            $status = trim(@$_REQUEST['status']);
            $stage = trim(@$_REQUEST['stage']);
            $note = trim(@$_REQUEST['note']);
            $note_empty = trim(@$_REQUEST['note_empty']);
            $include_deleted = trim(@$_REQUEST['include_deleted']);
            $include_finish = trim(@$_REQUEST['include_finish']);

            foreach($config["STAGE"] as $item){
                $plan_date = trim(@$_REQUEST["PlanDate-".$item]) ? trim(@$_REQUEST["PlanDate-".$item]) : "";
                $plan_enddate = trim(@$_REQUEST["PlanEndDate-".$item]) ? trim(@$_REQUEST["PlanEndDate-".$item]) : "";

                //if(strtotime($plan_date) > strtotime($plan_enddate)){
                    //die("搜索的装间 StartDate 不能大于 EndDate");
                //}

                $plan_color = trim(@$_REQUEST["PlanColor-".$item]) ? trim(@$_REQUEST["PlanColor-".$item]) : "";


                $real_date = trim(@$_REQUEST["RealDate-".$item]) ? trim(@$_REQUEST["RealDate-".$item]) : "";
                $real_enddate = trim(@$_REQUEST["RealEndDate-".$item]) ? trim(@$_REQUEST["RealEndDate-".$item]) : "";
                //if(strtotime($real_date) > strtotime($real_enddate)){
                    //die("Filter StartDate 不能大于 EndDate");
                //}
                $real_color = trim(@$_REQUEST["RealColor-".$item]) ? trim(@$_REQUEST["RealColor-".$item]) : "";

                @$from_array[$item] = array('PlanDate' => $plan_date,'PlanEndDate' => $plan_enddate, 'PlanColor' => $plan_color,'RealDate' => $real_date,'RealEndDate' => $real_enddate, 'RealColor' => $real_color);
            }
        }

        $where = "";

        if(empty($include_deleted)) {
                $where .= " AND deleted = 0 ";
        }

        if(empty($include_finish)) {
                $where .= " AND finish = 0 ";
        }

        if(! empty($filter_status)) {
            $where .= " AND status = '".$filter_status."'";
        }

        if(! empty($filter_stage)) {
            $where .= " AND stage = '".$filter_stage."'";
        }


        $where .= empty($name) ? "" : " AND name = '".$name."'";
        $where .= empty($theme_function) ? "" : " AND theme_function = '".$theme_function."'";
        $where .= empty($version) ? "" : " AND version = '".$version."'";
        $where .= empty($status) ? "" : " AND status = '".$status."'";
        $where .= empty($stage) ? "" : " AND stage = '".$stage."'";
        $where .= (strcmp($note_empty,"on") == 0) ? " AND note = ''" : (empty($note) ? "" : " AND note like '%".$note."%'");
        $where = ltrim($where," AND");

        $sql = empty($where) ? "select * from ".$config["DB_TABLE"] : "select * from ".$config["DB_TABLE"]." where ".$where;

        $re = $this->query($sql);
        //echo $sql;

        $get_db_array = $this->to_array($re);
        // 暂时不能搜索 Stage Date数据.
        //$return_array = $get_db_array;
        // 过滤 Stage Date 数据

        foreach($get_db_array as $row) {
            $add_bool = true;  // add_bool 全局决定是否添加到返回数组.
            $colors = array(); // 各 stage Color 比较。
            $date_array = array(); // 各 stage Color 比较。
            $db_array = $stage_json->decode($row['stage_date_json']);
            //print_r($db_array);

            foreach($config["STAGE"] as $item){
                $colors[$item.".plan"] = true;  // stage color 判断.
                $colors[$item.".real"] = true;  // stage color 判断.
                $date_array[$item.".plan"] = true;  // stage date判断.
                $date_array[$item.".real"] = true;  // stage date判断.

                // plan 时间比对.
                $db_plan_time = strtotime($db_array[$item]["PlanDate"]);
                $from_plan_st = strtotime(@$from_array[$item]["PlanDate"]);
                $from_plan_et = strtotime(@$from_array[$item]["PlanEndDate"]);

                if(! empty($from_plan_st) && ! empty($from_plan_et)) {
                    $date_array[$item.".plan"] = ($from_plan_st <= $db_plan_time && $db_plan_time <= $from_plan_et) ? true : false;
                } else if(! empty($from_plan_st) && empty($from_plan_et)) {
                    $date_array[$item.".plan"] = ($from_plan_st <= $db_plan_time) ? true : false;
                } else if(empty($from_plan_st) && ! empty($from_plan_et)) {
                    $date_array[$item.".plan"] = ($db_plan_time <= $from_plan_et) ? true : false;
                }

                // real 时间比对.
                $db_real_time = strtotime($db_array[$item]["RealDate"]);
                $from_real_st = strtotime(@$from_array[$item]["RealDate"]);
                $from_real_et = strtotime(@$from_array[$item]["RealEndDate"]);

                if(! empty($from_real_st) && ! empty($from_real_et)) {
                    $date_array[$item.".real"] = ($from_real_st <= $db_real_time && $db_real_time <= $from_real_et) ? true : false;
                } else if(! empty($from_real_st) && empty($from_real_et)) {
                    $date_array[$item.".real"] = ($from_real_st <= $db_real_time) ? true : false;
                } else if(empty($from_real_st) && ! empty($from_real_et)) {
                    $date_array[$item.".real"] = ($db_real_time <= $from_real_et) ? true : false;
                }

                // plan 颜色比对.
                $db_plan_color = $db_array[$item]["PlanColor"];
                $from_plan_color = @$from_array[$item]["PlanColor"];

                if(! empty($from_plan_color)){
                    $colors[$item.".plan"]  = (strcmp($from_plan_color,$db_plan_color) == 0) ? true : false;
                }

                // real 颜色比对.
                $db_real_color = $db_array[$item]["RealColor"];
                $from_real_color = @$from_array[$item]["RealColor"];

                if(! empty($from_real_color)){
                    $colors[$item.".real"]  = (strcmp($from_real_color,$db_real_color) == 0) ? true : false;
                }
            }

            // 判断颜色是否相等。
            foreach($colors as $color) {
                if(! $color){
                    $add_bool = false;
                }
            }

            // 判断是间否相等。
            foreach($date_array as $date) {
                if(! $date){
                    $add_bool = false;
                }
            }

            // 将适合条件的放到 返回数组
            if($add_bool) {
                array_push($return_array,$row);
            }
        }

	// 保存 SESSION Filter 面板数据。
	$_SESSION["filter_array"]["name"] = $name;
	$_SESSION["filter_array"]["theme_function"] = $theme_function;
	$_SESSION["filter_array"]["version"] = $version;
	$_SESSION["filter_array"]["status"] = $status;
	$_SESSION["filter_array"]["stage"] = $stage;
	$_SESSION["filter_array"]["note"] = $note;
	$_SESSION["filter_array"]["include_deleted"] = $include_deleted;
	$_SESSION["filter_array"]["include_finish"] = $include_finish;
	$_SESSION["filter_array"]["note_empty"] = $note_empty;
	$_SESSION["filter_array"]["stage_date_json"] = $stage_json->encode($from_array);

        return $return_array;
    }

    public function insert(){
        global $config;
        $tmp_array = array();
        $stage_json = new stage_date_json();

        $id = trim(@$_REQUEST['id']);
        $name = trim(@$_REQUEST['name']);
        $theme_function = trim(@$_REQUEST['theme_function']);
        $version = trim(@$_REQUEST['version']);
        $status = trim(@$_REQUEST['status']);
        $stage = trim(@$_REQUEST['stage']);
        $note = trim(@$_REQUEST['note']);

        foreach($config["STAGE"] as $item){
            @$plan_date = trim(@$_REQUEST["PlanDate-".$item]) ? trim(@$_REQUEST["PlanDate-".$item]) : "N\A";
            @$plan_color = trim(@$_REQUEST["PlanColor-".$item]) ? trim(@$_REQUEST["PlanColor-".$item]) : "N\A";
            @$real_date = trim(@$_REQUEST["RealDate-".$item]) ? trim(@$_REQUEST["RealDate-".$item]) : "N\A";
            @$real_color = trim(@$_REQUEST["RealColor-".$item]) ? trim(@$_REQUEST["RealColor-".$item]) : "N\A";

            $tmp_array[$item] = array('PlanDate' => $plan_date,'PlanColor' => $plan_color,'RealDate' => $real_date,'RealColor' => $real_color);
        }
        $stage_date_json = mysql_escape_string($stage_json->encode($tmp_array));

        if(empty($name) || empty($theme_function) || empty($version) || empty($status) || empty($stage)){
            die("insert fileds is empty.");
        }

        if(empty($id)){
            $sql = "INSERT INTO ".$config["DB_TABLE"]." values ('','".$name."','".$theme_function."','".$version."','".$status."','".$stage."','".$stage_date_json."','".$note."','','')";
        } else {
            $sql = "UPDATE ".$config["DB_TABLE"]." set name = '".$name."', theme_function = '".$theme_function."', version = '".$version."',status = '".$status."', stage = '".$stage."',stage_date_json='".$stage_date_json."', note = '".$note."' WHERE id = ".$id;
        }

        //echo $sql;
        $this->query_one($sql);
    }
}

class stage_date_json{
    public function decode($json_str){
        global $config;

        $tmp_array = json_decode($json_str,true);
        return $tmp_array;
    }

    public function encode($json_array){
        if(is_array($json_array)){
            return json_encode($json_array);
        }
        return "";
    }

    public function stage_date_init($search=false){
        global $config;
        $tmp_array = array();

        foreach($config["STAGE"] as $stage){
                $tmp_array[$stage] = array('PlanDate' => '','PlanColor' => '','RealDate' => '','RealColor' => '');
            if($search){
                $tmp_array[$stage] = array('PlanDate' => '','PlanColor' => '','RealDate' => '','RealColor' => '');
            } else {
                $tmp_array[$stage] = array('PlanDate' => 'N/A','PlanColor' => '','RealDate' => 'N/A','RealColor' => '');
            }
        }

        return $tmp_array;
    }
}

class allow {
    public function is_allow_ip(){
        global $config;
        $ip = $_SERVER["REMOTE_ADDR"];
        $ip_net = str_replace(strrchr($ip,"."),"",$ip);

        foreach($config["WHITE_IP"] as $allow_ip){
            if(strcmp($allow_ip,$ip) == 0) return true;
        }

        foreach($config["WHITE_NET"] as $allow_net){
            if(strcmp($allow_net,$ip_net) == 0) return true;
        }

        return false;
    }

    public function pass(){
        global $config;

        foreach($config["ADMIN_IP"] as $ip){
            if(strcmp($ip,@$_SERVER["REMOTE_ADDR"]) == 0) return true;
        }
        return false;
    }
}

function find_global_filter(){
    $load_file = fopen("global_filter.set", "r") or die("Unable to open file!");
    $filter_json =  @fread($load_file,filesize("global_filter.set"));
    $filter_array = json_decode($filter_json,true);

    if(is_array($filter_array)){
        return true;
    }

    fclose($load_file);
    return false;
}

function load_filter(){
    $load_file = fopen("global_filter.set", "r") or die("Unable to open file!");
    $filter_json =  fread($load_file,filesize("global_filter.set"));
    $filter_array = json_decode($filter_json,true);

    if(is_array($filter_array)){
        $_SESSION["filter_array"] = $filter_array;
        return true;
    }

    fclose($load_file);
    return false;
}

// 实例数据库
$mysql = new mysql_lib();
$mysql->conn_db();
// 实例JSON 解释器
$stage_json = new stage_date_json();
// 实例ALLOW编辑权限
$allow = new allow();
?>

