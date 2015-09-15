<?PHP
    include "lib.php";
    $tbl_data = $mysql->get_all();
?>
<!DOCTYPE HTML>
    <HEAD>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <meta http-equiv="pragma" content="no-cache" />
       <meta http-equiv="cache-control" content="no-cache" />
       <TITLE> <?PHP echo $config["SITE_NAME"] ?></TITLE>
       <link type="text/css" rel="stylesheet" href="/public/css/all.css" />
    </HEAD>
    <BODY>
        <table border=1 width="800px" class="sort-table">
            <thead>
                <tr>
                <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>">项目状态</th>
                </tr>
                <tr>
                    <th rowspan="2">项目</th>
                    <th rowspan="2">主题/功能</th>
                    <th rowspan="2">版本</th>
                    <th rowspan="2">状态</th>
                    <th rowspan="2">阶段</th>
                    <?PHP
                        foreach($config["STAGE"] as $stage){
                            echo "<th colspan=\"2\">".$stage."</th>";
                        }
                    ?>
                    <th rowspan="2">备注</th>
                </tr>
                <tr>
                <?PHP
                    for($i=0; $i < count($config["STAGE"]); $i++){
                        echo "<th>计划</th>";
                        echo "<th>实际</th>";
                    }
                ?>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    foreach($tbl_data as $row){
                        echo "<tr><td>"
                            .$row['name']."</td><td>"
                            .$row['theme_function']."</td><td>"
                            .$row['version']."</td><td>"
                            .$row['status']."</td><td>"
                            .$row['stage']."</td>";

                        $stage_data = $stage_json->decode($row['stage_date_json']);
                        foreach($config["STAGE"] as $stage){
                            echo "<td bgcolor=\"".$stage_data[$stage]["PlanColor"]."\">".$stage_data[$stage]["PlanDate"]."</td>";
                            echo "<td bgcolor=\"".$stage_data[$stage]["RealColor"]."\">".$stage_data[$stage]["RealDate"]."</td>";
                        }
                        echo "<td>".$row['note']."</td>";
                    echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </BODY>
</HTML>

<?PHP
    $mysql->close_db();
?>
