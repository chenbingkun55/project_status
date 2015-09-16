<?PHP
#ifundef PROJECT_STATUS
    //die("access deny.");
#else

    include "lib.php";

    @$finish = trim($_REQUEST['finish']);
    @$deleted = trim($_REQUEST['deleted']);
    @$id = trim($_REQUEST['id']);

    if(!empty($finish)){
        $re = $mysql->finish($finish);
        die("finish status.");
    }

    if(!empty($deleted)){
        $re = $mysql->deleted($deleted);
        die("deleted .");
    }

    if(!empty($id)){
        $update = $mysql->find($id);
    }

    if($_POST){
        $mysql->insert();
        echo "<script>location.href='index.php'; </script>";
    }

?>
<!DOCTYPE HTML>
    <HEAD>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <meta http-equiv="pragma" content="no-cache" />
       <meta http-equiv="cache-control" content="no-cache" />
       <TITLE> <?PHP echo $config["SITE_NAME"] ?></TITLE>
       <link type="text/css" rel="stylesheet" href="public/css/common.css" />
    </HEAD>
    <BODY>
<?PHP
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
?>
        <form action="admin.php" method="post">
            <table style="background:red">
                <tr>
                    <td><INPUT class="input_ajax" type="text" name="name" value="<?PHP echo $update["name"] ?>"/></td>
                    <td><INPUT class="input_ajax" type="text" name="theme_function" value="<?PHP echo $update["theme_function"] ?>"/></td>
                    <td><INPUT class="input_ajax" type="text" name="version" value="<?PHP echo $update["version"] ?>"/></td>
                    <td>
                        <select name="status" style=\"width:100%\">
                        <?PHP
                            $status_array = array("提前","正常","延迟");
                            foreach($status_array as $status){
                                if(strcmp($status,$update["status"]) == 0){
                                    echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                } else {
                                    if(strcmp($status,"正常") == 0){
                                        echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                    } else {
                                        echo "<option value=\"".$status."\">".$status."</option>";
                                    }
                                }
                            }
                        ?>
                        </select>
                    </td>
                    <td>
                        <select name="stage">
                        <?PHP
                            foreach($config["STAGE"] as $stage){
                                if(strcmp($update["stage"],$stage) == 0){
                                    echo "<option value=\"".$stage."\" selected=\"selected\">".$stage."</option>";
                                } else {
                                    echo "<option value=\"".$stage."\">".$stage."</option>";
                                }
                            }
                        ?>
                        </select>
                    </td>
<?PHP
                        $colors = array("red","green","yellow");
                        foreach($stage_json->decode($update['stage_date_json']) as $stage => $date){
                            echo "<td style=\"background:".$date["PlanColor"]."\">";
                            echo "<input class=\"input_ajax\" type=\"date\" name=\"PlanDate-".$stage."\" value=\"".$date["PlanDate"]."\">";
                            foreach($colors as $color){
                                if(strcmp($color,$date["PlanColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"\">".$color;
                                } else {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\">".$color;
                                }
                            }
                            echo "</td>";
                            echo "<td style=\"background:".$date["RealColor"]."\">";
                            echo "<input class=\"input_ajax\" type=\"date\" name=\"RealDate-".$stage."\" value=\"".$date["RealDate"]."\">";
                            foreach($colors as $color){
                                if(strcmp($color,$date["RealColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" checked=\"checked\">".$color;
                                } else {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\">".$color;
                                }
                            }
                            echo "</td>";
                        }
?>
                    <td><INPUT class="input_ajax" type="text" name="note" value="<?PHP echo $update["note"]?>" /></td>
                </tr>
                <tr>
                    <td colspan="14" style="text-align:center"><INPUT type="hidden" name="id" value="<?PHP echo $update["id"]?>" />
                        <INPUT type="SUBMIT" value="更新" />
                        <INPUT type="BUTTON" value="取消" onClick="$.edit_cancel();" />
                        <INPUT type="BUTTON" value="己完成" onClick="$.finish(<?PHP echo $update["id"] ?>)" />
                        <INPUT type="BUTTON" value="删除" onClick="$.delete(<?PHP echo $update["id"] ?>)" />
                    </td>
                </tr>
            </table>
        </form>

<?PHP
else:
?>
<form action="admin.php" method="post">
            <table>
                <tr>
                    <td>项目:</td>
                    <td><INPUT type="text" name="name" value="<?PHP echo @$update["name"] ?>"/></td>
                </tr>
                <tr>
                    <td>主题/功能:</td>
                    <td><INPUT type="text" name="theme_function" value="<?PHP echo @$update["theme_function"] ?>"/></td>
                </tr>
                <tr>
                    <td>版本:</td>
                    <td><INPUT type="text" name="version" value="<?PHP echo @$update["version"] ?>"/></td>
                </tr>
                <tr>
                    <td>状态:</td>
                    <td>
                        <select name="status">
                        <?PHP
                            $status_array = array("提前","正常","延迟");
                            foreach($status_array as $status){
                                if(strcmp($status,@$update["status"]) == 0){
                                    echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                } else {
                                    if(strcmp($status,"正常") == 0){
                                        echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                    } else {
                                        echo "<option value=\"".$status."\">".$status."</option>";
                                    }
                                }
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>价段:</td>
                    <td>
                        <select name="stage">
                        <?PHP
                            foreach($config["STAGE"] as $stage){
                                if(strcmp(@$update["stage"],$stage) == 0){
                                    echo "<option value=\"".$stage."\" selected=\"selected\">".$stage."</option>";
                                } else {
                                    echo "<option value=\"".$stage."\">".$stage."</option>";
                                }
                            }
                        ?>
                        </select>
                    </td>
                </tr>
<?PHP
                        $colors = array("red","green","yellow");
                        foreach($stage_json->stage_date_init() as $stage => $date){
                            echo "<tr><td>".$stage."计划:</td>";
                            echo "<td>";
                            echo "<input type=\"date\" name=\"PlanDate-".$stage."\" value=\"".$date["PlanDate"]."\">";
                            foreach($colors as $color){
                                if(strcmp($color,$date["PlanColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"\">".$color;
                                } else {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\">".$color;
                                }
                            }
                            echo "</td></tr>";
                            echo "<tr><td>".$stage."实际:</td>";
                            echo "<td>";
                            echo "<input type=\"date\" name=\"RealDate-".$stage."\" value=\"".$date["RealDate"]."\">";
                            foreach($colors as $color){
                                if(strcmp($color,$date["RealColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" checked=\"checked\">".$color;
                                } else {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\">".$color;
                                }
                            }
                            echo "</td></tr>";
                        }
?>
                <tr>
                    <td>备注:</td>
                    <td><INPUT type="text" name="note" value="<?PHP echo @$update["note"]?>" /></td>
                </tr>
                <tr>
                    <INPUT type="hidden" name="id" value="<?PHP echo @$update["id"]?>" />
                    <td colspan="2"><INPUT type="SUBMIT" value="Add" /></td>
                </tr>
            </table>
        </form>
<?PHP
endif;
?>
    </BODY>
</HTML>
<?PHP
#endif
?>
