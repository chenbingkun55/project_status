<?PHP
session_start();

    include "lib.php";
    $tbl_data = $mysql->get_in_process();

    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=export_data.xls");

    $export_str = '';


        <th class="header name" rowspan="2">项目</th>
        <th class="header theme-function" rowspan="2">主题/功能</th>
        <th class="header version" rowspan="2">版本</th>
        <th class="header status" rowspan="2">状态</th>
        <th class="header stage" rowspan="2">阶段</th>
        <?PHP
            foreach($config["STAGE"] as $stage){
                echo "<th class=\"stage_title\" colspan=\"2\">".$stage."</th>";
            }
        ?>
        <th class="header note" rowspan="2">备注</th>
    </tr>
    <tr>
    <?PHP
        for($i=0; $i < count($config["STAGE"]); $i++){
            echo "<th class=\"header stage_date\">计划</th>";
            echo "<th class=\"header stage_date\">实际</th>";
        }
    ?>
    </tr>

    foreach($tbl_data as $row){
        $export_str .= $row["id"]."\t";
        $export_str .= $row["name"]."\t";
        $export_str .= $row['theme_function']."\t";
        $export_str .= $row['version']."\t";
        $export_str .= $row['status']."\t";
        $export_str .= $row['stage']."\t";

        $stage_data = $stage_json->decode($row['stage_date_json']);
        foreach($config["STAGE"] as $stage){
            $export_str .= $stage_data[$stage]["PlanDate"]."\t";
            $export_str .= $stage_data[$stage]["RealDate"]."\t";
        }
        $export_str .= $row['note']."\t";
    $export_str .= "\n";
    }

    echo $export_str;
 ?>
