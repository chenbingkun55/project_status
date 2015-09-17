<?PHP
session_start();

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
        @$update = $mysql->find($id);
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
                    <td class="name"><INPUT id="name" class="input_ajax" type="text" name="name" value="<?PHP echo @$update["name"] ?>" onClick="$.list_names();"/></td>
                    <td class="theme_function"><INPUT class="input_ajax" type="text" name="theme_function" value="<?PHP echo @$update["theme_function"] ?>"/></td>
                    <td class="version"><INPUT class="input_ajax" type="text" name="version" value="<?PHP echo @$update["version"] ?>"/></td>
                    <td class="status">
                        <select class="select_ajax" name="status" style=\"width:100%\">
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
                    <td class="stage">
                        <select class="select_ajax" name="stage">
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
<?PHP
                        $stage_array = $stage_json->stage_date_init();
                        if($id) {
                            $stage_array = $stage_json->decode(@$update['stage_date_json']);
                        }

                        $colors = array("无" => "","red" => "red","green" => "green","yellow" => "yellow");
                        foreach($stage_array as $stage => $date){
                            echo "<td class=\"stage_date\" style=\"background:".$date["PlanColor"]."\">";
                            echo "<input id=\"plandate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"PlanDate-".$stage."\" value=\"".$date["PlanDate"]."\">";
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["PlanColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"\">".$key."<BR>";
                                } else {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\">".$key."<BR>";
                                }
                            }
                            echo "</td>";
                            echo "<td class=\"stage_date\" style=\"background:".$date["RealColor"]."\">";
                            echo "<input id=\"realdate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"RealDate-".$stage."\" value=\"".$date["RealDate"]."\">";
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["RealColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" checked=\"checked\">".$key."<BR>";
                                } else {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\">".$key."<BR>";
                                }
                            }
                            echo "</td>";
                        }
?>
                    <td class="note"><textarea class="textarea_ajax" rows="5" name="note"><?PHP echo @$update["note"]?></textarea></td>
                </tr>
                <tr>
                    <td colspan="14" style="text-align:center"><INPUT type="hidden" name="id" value="<?PHP echo @$update["id"]?>" />
<?PHP
if($id):
?>
                        <INPUT type="SUBMIT" value="更新" onClick="$.submit();" />
                        <INPUT type="BUTTON" value="取消" onClick="$.edit_cancel();" />
                        ---
                        <INPUT type="BUTTON" value="己完成" style="background:red;" onClick="$.finish(<?PHP echo @$update["id"] ?>)" />
                        <INPUT type="BUTTON" value="删除" style="background:red;" onClick="$.delete(<?PHP echo @$update["id"] ?>)" />
<?PHP
else:
?>
                        <INPUT type="SUBMIT" value="添加" />
                        <INPUT type="BUTTON" value="取消" onClick="$.edit_cancel();" />
<?PHP
endif;
?>
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
                        $colors = array("无" => "","red" => "red","green" => "green","yellow" => "yellow");
                        foreach($stage_json->stage_date_init() as $stage => $date){
                            echo "<tr><td>".$stage."计划:</td>";
                            echo "<td>";
                            echo "<input id=\"plandate_".$stage."\" type=\"text\" name=\"PlanDate-".$stage."\" value=\"".$date["PlanDate"]."\">";
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["PlanColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"\">".$key;
                                } else {
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\">".$key;
                                }
                            }
                            echo "</td></tr>";
                            echo "<tr><td>".$stage."实际:</td>";
                            echo "<td>";
                            echo "<input id=\"realdate_".$stage."\" type=\"text\" name=\"RealDate-".$stage."\" value=\"".$date["RealDate"]."\">";
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["RealColor"]) == 0) {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" checked=\"checked\">".$key;
                                } else {
                                    echo "<input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\">".$key;
                                }
                            }
                            echo "</td></tr>";
                        }
?>
                <tr>
                    <td>备注:</td>
                    <td><textarea name="note" rows="5" /><?PHP echo @$update["note"]?></textarea></td>
                </tr>
                <tr>
                    <INPUT type="hidden" name="id" value="<?PHP echo @$update["id"]?>" />
                    <td colspan="2"><INPUT type="SUBMIT" value="Add" /></td>
                </tr>
            </table>
        </form>
       <script type="text/javascript" src="public/js/jquery-2.1.4.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.datepicker.min.js"></script>
<?PHP
endif;
?>
    </BODY>
   <script>
       $(document).ready(function(){
        <?PHP
            foreach($config["STAGE"] as $stage){
               echo "$('#plandate_".$stage."').datePicker();\n";
               echo "$('#realdate_".$stage."').datePicker();\n";
            }
        ?>
       });
   </script>
</HTML>
<?PHP
?>
