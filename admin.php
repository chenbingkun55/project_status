<?PHP
session_start();

    include "lib.php";

    $finish = trim(@$_REQUEST['finish']);
    $deleted = trim(@$_REQUEST['deleted']);
    $id = trim(@$_REQUEST['id']);

    if(!empty($finish)){
        $re = $mysql->finish($finish);
        die("finish status.");
    }

    if(!empty($deleted)){
        $re = $mysql->deleted($deleted);
        die("deleted .");
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
       <TITLE> <?PHP echo @$config["SITE_NAME"] ?></TITLE>
       <link type="text/css" rel="stylesheet" href="public/css/common.css" />
    </HEAD>
    <BODY>
<?PHP
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
    if(strcmp(@$_REQUEST["filter"],"1") == 0){
        $update = $_SESSION["filter_array"];

        echo "<form action=\"index.php?filter=1\" method=\"post\">";
        echo "<div class=\"filter_term\">";
        echo "<span><input id=\"include_deleted\" type=\"checkbox\" name=\"include_deleted\" style=\"vertical-align: middle\"".(empty($update["include_deleted"]) ? "" : "checked").">包括己删除</span>";
        echo "&nbsp;&nbsp;";
        echo "<span><input id=\"include_finish\" type=\"checkbox\" name=\"include_finish\" style=\"vertical-align: middle\" ".(empty($update["include_finish"]) ? "" : "checked").">包括己完成</span>";
        echo "&nbsp;&nbsp;";
        echo "<span><input id=\"note_empty\" type=\"checkbox\" name=\"note_empty\" style=\"vertical-align: middle\" ".(empty($update["note_empty"]) ? "" : "checked")."> 备注为空</span>";
        echo "</div>";
    } else {
        echo "<form action=\"admin.php\" method=\"post\">";
    }
?>
            <table style="background:red;">
                <tr>
                    <td class="name"><INPUT id="name" class="input_ajax" type="text" name="name" value="<?PHP echo @$update["name"] ?>" onClick="$.list_names();"/></td>
                    <td class="theme_function"><INPUT id="theme_function" class="input_ajax" type="text" name="theme_function" value="<?PHP echo @$update["theme_function"] ?>"/></td>
                    <td class="version"><INPUT id="version" class="input_ajax" type="text" name="version" value="<?PHP echo @$update["version"] ?>"/></td>
                    <td class="status">
                        <select id="status" class="select_ajax" name="status" style=\"width:100%\">
                        <?PHP
                            if(strcmp(@$_REQUEST["filter"],"1") == 0) {
                                echo "<option value=\"\">空</option>";
                            }
                            $status_array = array("提前","正常","延迟");
                            foreach($status_array as $status){
                                if(strcmp($status,@$update["status"]) == 0){
                                    echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                } else {
                                    if(strcmp($status,"正常") == 0 && strcmp(@$_REQUEST["filter"],"1") != 0){
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
                        <select id="stage" class="select_ajax" name="stage">
                        <?PHP
                            if(strcmp(@$_REQUEST["filter"],"1") == 0) {
                                echo "<option value=\"\">空</option>";
                            }
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
                        if(strcmp(@$_REQUEST["filter"],"1") == 0){
                            if(empty(@$update['stage_date_json'])) {
                                $stage_array = $stage_json->stage_date_init(true);
                            } else {
                                $stage_array = $stage_json->decode(@$update['stage_date_json']);
                            }
                        } else {
                            $stage_array = $stage_json->stage_date_init();
                        }

                        if($id) {
                            $stage_array = $stage_json->decode(@$update['stage_date_json']);
                        }

                        $colors = array("无" => "","red" => "red","green" => "green","yellow" => "yellow");
                        foreach($stage_array as $stage => $date){
                            echo "<td class=\"stage_date\" style=\"background:".$date["PlanColor"]."\">";
                            echo "<input id=\"plandate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"PlanDate-".$stage."\" value=\"".$date["PlanDate"]."\">";
                            if(strcmp(@$_REQUEST["filter"],"1") == 0) {
                                echo "<input id=\"planenddate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"PlanEndDate-".$stage."\" value=\"".$date["PlanEndDate"]."\">";
                            }
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["PlanColor"]) == 0) {
                                    echo "<input class=\"stage_color\" type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"\">".$key."<BR>";
                                } else {
                                    echo "<input class=\"stage_color\" type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\">".$key."<BR>";
                                }
                            }
                            echo "</td>";
                            echo "<td class=\"stage_date\" style=\"background:".$date["RealColor"]."\">";
                            echo "<input id=\"realdate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"RealDate-".$stage."\" value=\"".$date["RealDate"]."\">";
                            if(strcmp(@$_REQUEST["filter"],"1") == 0) {
                                echo "<input id=\"realenddate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"RealEndDate-".$stage."\" value=\"".$date["RealEndDate"]."\">";
                            }
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["RealColor"]) == 0) {
                                    echo "<input class=\"stage_color\" type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" checked=\"checked\">".$key."<BR>";
                                } else {
                                    echo "<input class=\"stage_color\" type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\">".$key."<BR>";
                                }
                            }
                            echo "</td>";
                        }
?>
                    <td class="note">
                        <textarea id="note" class="textarea_ajax" rows="5" name="note"><?PHP echo @$update["note"]?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="14" style="text-align:center"><INPUT type="hidden" name="id" value="<?PHP echo @$update["id"]?>" />
<?PHP
if($id):
?>
<?PHP
                if($update["finish"] == 0 && $update[deleted] == 0){
                        echo "<INPUT type=\"SUBMIT\" value=\"更新\" onClick=\"$.submit();\" />";
                        echo "<INPUT type=\"BUTTON\" value=\"取消\" onClick=\"$.edit_cancel();\" />";
                        echo "---";
                }

                if($update["finish"] == 0 ){
                    if($update["deleted"] == 0 ){
                        echo "<INPUT type=\"BUTTON\" value=\"标记[己完成]\" style=\"background:red;\" onClick=\"$.finish(".$update["id"].");\" />";
                    }
                } else {
                    echo "<INPUT type=\"BUTTON\" value=\"标记[未完成]\" style=\"background:yellow;\" onClick=\"$.finish(".$update["id"].",true);\" />";
                }

                if($update["deleted"] == 0 ){
                    if($update["finish"] == 0 ){
                        echo "<INPUT type=\"BUTTON\" value=\"删除\" style=\"background:red;\" onClick=\"$.delete(".$update["id"].");\" />";
                    }
                } else {
                    echo "<INPUT type=\"BUTTON\" value=\"还原\" style=\"background:yellow;\" onClick=\"$.delete(".$update["id"].",true);\" />";
                }

else:

    if(strcmp(@$_REQUEST["filter"],"1") == 0):
?>
                        <INPUT type="SUBMIT" value="搜索" />
                        <INPUT type="RESET" value="清空" onClick="$.clean_filter_plan();"/>
                        <INPUT type="BUTTON" value="收起过滤面板" onClick="$.hide_filter();" />

<?PHP
    else:
?>
                        <INPUT type="SUBMIT" value="添加" />
                        <INPUT type="BUTTON" value="取消" onClick="$.edit_cancel();" />
<?PHP
    endif;
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
                        $stage_array = $stage_json->stage_date_init();
                        foreach($stage_array as $stage => $date){
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
                if(strcmp(@$_REQUEST["filter"],"1") == 0) {
                   echo "$('#plandate_".$stage."').datePicker();\n";
                   echo "$('#realdate_".$stage."').datePicker();\n";
                   echo "$('#planenddate_".$stage."').datePicker();\n";
                   echo "$('#realenddate_".$stage."').datePicker();\n";
                } else {
                   echo "$('#plandate_".$stage."').datePicker();\n";
                   echo "$('#realdate_".$stage."').datePicker();\n";
                }
            }
        ?>
       });
   </script>
</HTML>
<?PHP
?>
