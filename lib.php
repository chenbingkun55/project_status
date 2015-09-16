<?PHP
$config = array();
$config["SITE_NAME"] = "Project Status";
$config["ALLOW_IP"] = array("127.0.0.1");
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

    public function get_in_process(){
        global $config;
        $sql = "select * from ".$config["DB_TABLE"]." where finish = 0 AND deleted = 0";
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
        $sql = "UPDATE ".$config["DB_TABLE"]." SET finish = 1  where id = ".$id;

        return $this->query_one($sql);
    }

    public function deleted($id){
        global $config;
        $sql = "UPDATE ".$config["DB_TABLE"]." SET deleted = 1  where id = ".$id;

        return $this->query_one($sql);
    }

    public function insert(){
        global $config;
        $tmp_array = array();
        $stage_json = new stage_date_json();

        $id = trim($_REQUEST['id']);
        $name = trim($_REQUEST['name']);
        $theme_function = trim($_REQUEST['theme_function']);
        $version = trim($_REQUEST['version']);
        $status = trim($_REQUEST['status']);
        $stage = trim($_REQUEST['stage']);
        $note = trim($_REQUEST['note']);

        foreach($config["STAGE"] as $item){
            @$plan_date = trim($_REQUEST["PlanDate-".$item]) ? trim($_REQUEST["PlanDate-".$item]) : "N\A";
            @$plan_color = trim($_REQUEST["PlanColor-".$item]) ? trim($_REQUEST["PlanColor-".$item]) : "N\A";
            @$real_date = trim($_REQUEST["RealDate-".$item]) ? trim($_REQUEST["RealDate-".$item]) : "N\A";
            @$real_color = trim($_REQUEST["RealColor-".$item]) ? trim($_REQUEST["RealColor-".$item]) : "N\A";

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

    public function stage_date_init(){
        global $config;
        $tmp_array = array();

        foreach($config["STAGE"] as $stage){
            $tmp_array[$stage] = array('PlanDate' => 'N/A','PlanColor' => '','RealDate' => 'N/A','RealColor' => '');
        }

        return $tmp_array;
    }
}

class allow{
    public function pass(){
        global $config;

        foreach($config["ALLOW_IP"] as $ip){
            if(strcmp($ip,$_SERVER["REMOTE_ADDR"]) == 0) return true;
        }
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

