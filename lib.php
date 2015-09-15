<?PHP
$config = array();
$config["SITE_NAME"] = "Project Status";
// DB 配置
$config["DB_HOST"] = "localhost";
$config["DB_USER"] = "root";
$config["DB_PASSWORD"] = "123456";
$config["DB_NAME"] = "test";
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
        mysql_ping($this-con);

        $re = mysql_query($sql,$this->con);
        return $this->to_array($re);
    }

    public function to_array($result){
        $return_array = array();

        while($row=mysql_fetch_row($result)){
            $tmp_array = array();
            for($i= 0; $i < count($row);$i++){
                $filed_name = mysql_field_name($result, $i);
                $tmp_array[$filed_name] = $row[$i];
            }
            array_push($return_array, $tmp_array);
        }
        return $return_array;
    }

    public function get_all(){
        $sql = "select * from project_status";
        return $this->query($sql);
    }
}

class stage_date_json{
    public function decode($json_str){
        global $config;

        $tmp_array = json_decode($json_str,true);
        return $tmp_array;
    }

    public function encode($str){
        $tmp_array = array();
        echo json_encode($tmp_array);
    }

    public function stage_date_init(){
        global $config;
        $tmp_array = array();

        foreach($config["STAGE"] as $stage){
            $tmp_array[$stage] = array('PlanDate' => 'N/A','PlanColor' => '','RealDate' => 'N/A','RealColor' => '');
        }

        return json_encode($tmp_array);
    }
}

// 实例数据库
$mysql = new mysql_lib();
$stage_json = new stage_date_json();

$mysql->conn_db();
?>
