<?PHP
$config = array();
$config["SITE_NAME"] = "Project Status";
// 添加可编辑IP权限.
$config["ALLOW_IP"] = array("127.0.0.1","192.168.23.100","192.168.23.96","192.168.23.83");
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

        $this->con = mysql_connect($config["DB_HOST"],$config["DB_USER"],$config["DB_PASSWORD"]);
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

        return $this->to_array($re,true);
    }

    public function find($id){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"]." where id = ".$id;
        $re = $this->query($sql);

        return $this->to_array($re,true);
    }

    public function finish($id){
        global $config;
        $sql = "UPDATE ".$config["DB_TABLE"]." SET finish = 1  where id = ".$id;

        return $this->query_one($sql);
    }

    public function deleted($id){
        global $config;
        $sql = "UPDATE ".$config["DB_TABLE"]." SET deleted = 1  where id = ".$id;

        return $this->query_one($sql);
    }

    public function filter(){
        global $config;
        $from_array = array();
        $get_db_array = array();
        $db_array = array();
        $return_array = array();
        $stage_json = new stage_date_json();

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

            $from_array[$item] = array('PlanDate' => $plan_date,'PlanEndDate' => $plan_enddate, 'PlanColor' => $plan_color,'RealDate' => $real_date,'RealEndDate' => $real_enddate, 'RealColor' => $real_color);
        }


        $where = "";
        if(empty($include_deleted)) {
            $where .= " AND deleted = 0 ";
        }

        if(empty($include_finish)) {
            $where .= " AND finish = 0 ";
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
                $from_plan_st = strtotime($from_array[$item]["PlanDate"]);
                $from_plan_et = strtotime($from_array[$item]["PlanEndDate"]);

                if(! empty($from_plan_st) && ! empty($from_plan_et)) {
                    $date_array[$item.".plan"] = ($from_plan_st <= $db_plan_time && $db_plan_time <= $from_plan_et) ? true : false;
                } else if(! empty($from_plan_st) && empty($from_plan_et)) {
                    $date_array[$item.".plan"] = ($from_plan_st <= $db_plan_time) ? true : false;
                } else if(empty($from_plan_st) && ! empty($from_plan_et)) {
                    $date_array[$item.".plan"] = ($db_plan_time <= $from_plan_et) ? true : false;
                }

                // real 时间比对.
                $db_real_time = strtotime($db_array[$item]["RealDate"]);
                $from_real_st = strtotime($from_array[$item]["RealDate"]);
                $from_real_et = strtotime($from_array[$item]["RealEndDate"]);

                if(! empty($from_real_st) && ! empty($from_real_et)) {
                    $date_array[$item.".real"] = ($from_real_st <= $db_real_time && $db_real_time <= $from_real_et) ? true : false;
                } else if(! empty($from_real_st) && empty($from_real_et)) {
                    $date_array[$item.".real"] = ($from_real_st <= $db_real_time) ? true : false;
                } else if(empty($from_real_st) && ! empty($from_real_et)) {
                    $date_array[$item.".real"] = ($db_real_time <= $from_real_et) ? true : false;
                }

                // plan 颜色比对.
                $db_plan_color = $db_array[$item]["PlanColor"];
                $from_plan_color = $from_array[$item]["PlanColor"];

                if(! empty($from_plan_color)){
                    $colors[$item.".plan"]  = (strcmp($from_plan_color,$db_plan_color) == 0) ? true : false;
                }

                // real 颜色比对.
                $db_real_color = $db_array[$item]["RealColor"];
                $from_real_color = $from_array[$item]["RealColor"];

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
        $_SESSION["filter_array"]["Status"] = $status;
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

class allow{
    public function pass(){
        global $config;

        foreach($config["ALLOW_IP"] as $ip){
            if(strcmp($ip,@$_SERVER["REMOTE_ADDR"]) == 0) return true;
        }
        return false;
    }
}

// 实例数据库
$mysql = new mysql_lib();
$mysql->conn_db();
// 实例JSON 解释器
$stage_json = new stage_date_json();
// 实例ALLOW编辑权限
$allow = new allow();
?>

