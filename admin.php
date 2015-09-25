<?PHP
/**
* Author: @-ChenBk <chenbingkun55@163.com> 2015-09-19 16:30
* Version Info ------------------------------------------------
4432a9b * 项目只有一个时,get_names()不能正常工作
:100644 100644 621223d... a3b4997... M	admin.php
e225fb6 * 发布版本 0.1
:100644 100644 9d4de96... 621223d... M	admin.php
78a822c * 时间过滤，Color过滤功能。
:100644 100644 1ae9672... 9d4de96... M	admin.php
316058b * filter 过滤己删除和己完成.
:100644 100644 e21c40a... 1ae9672... M	admin.php
14d3915 * filter 完成一半, stage date 这块搜索比较困难.
:100644 100644 7064228... e21c40a... M	admin.php
366c0ce * 修改Export Excel时不加载css样式.
:100644 100644 30472b6... 7064228... M	admin.php
c184bcd * 修复搜索面板id和添加修改冲突BUG
:100644 100644 28c0b8b... 30472b6... M	admin.php
3c503c1 * 添加搜垵面板。
:100644 100644 2f3fa09... 28c0b8b... M	admin.php
cebfe57 * 添加index.php导出Excel功能。
:100644 100644 f69078e... 2f3fa09... M	admin.php
b0e12bd * 添加index.php改出Excel。
:100644 100644 f69078e... 2f3fa09... M	admin.php
04cc415 * 使用Session保存编辑状态
:100644 100644 f20485b... f69078e... M	admin.php
49c6355 + jquery.datepicker 日期选择器。 + get_names.php 取项目名称下拉列表。
:100644 100644 59f483f... f20485b... M	admin.php
598952c * 基本功能完成.
:100644 100644 995e216... 59f483f... M	admin.php
f04540a * 加载 tablestore * 添加、删除、修改功能
:000000 100644 0000000... 995e216... A	admin.php
* -------------------------------------------------------------
*
*
**/

session_start();

    include "lib.php";

    $show_save_filter = (strcmp(trim(@$_REQUEST['show_save_filter']),"1") == 0) ? true : false;
    $find_global_filter = (strcmp(trim(@$_REQUEST['find_global_filter']),"1") == 0) ? true : false;
    $filter = (strcmp(@$_REQUEST["filter"],"1") == 0) ? true : false;

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
<?PHP
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'):
    if($filter){
        $update = @$_SESSION["filter_array"];

        echo "<form action=\"index.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"filter_submit\" value=\"1\">";
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
            <table class="admin_page">
                <tr>
                    <td class="name" style="background-color:khaki;"><INPUT id="name" class="input_ajax" type="text" name="name" value="<?PHP echo @$update["name"] ?>" onClick="$.list_names();"/></td>
                    <td class="theme_function" style="background-color:khaki;" ><div class="theme_function_div"><INPUT id="theme_function" class="input_ajax" type="text" name="theme_function" value="<?PHP echo @$update["theme_function"] ?>"/></td>
                    <td class="version" style="background-color:khaki;" ><INPUT id="version" class="input_ajax" type="text" name="version" value="<?PHP echo @$update["version"] ?>"/></td>
                    <td class="status" style="background-color:khaki;" >
                        <select id="status" class="select_ajax" name="status">
                        <?PHP
                            if($filter) {
                                echo "<option value=\"\">空</option>";
                            }
                            $status_array = array("提前","正常","延迟");
                            foreach($status_array as $status){
                                if(strcmp($status,@$update["status"]) == 0){
                                    echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                } else {
                                    if(strcmp($status,"正常") == 0 && ! $filter){
                                        echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
                                    } else {
                                        echo "<option value=\"".$status."\">".$status."</option>";
                                    }
                                }
                            }
                        ?>
                        </select>
                    </td>
                    <td class="stage"  style="background-color:khaki;">
                        <select id="stage" class="select_ajax" name="stage">
                        <?PHP
                            if($filter) {
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
                        if($filter){
                            if(empty($update['stage_date_json'])) {
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

                        // [FIX] 当添加新 stage 后, 修改之前的数据时, 新的 stage 
                        // 不显示出来
                        $stage_empty = $stage_json->stage_date_init();
                        $stage_array = array_merge($stage_empty,$stage_array);

                        $colors = array("无" => "","Red" => "Red","Yellow" => "GreenYellow","Green" => "MediumSeaGreen");
                        foreach($stage_array as $stage => $date){
                            echo "<td class=\"stage_date\" style=\"background:".$date["PlanColor"]."\">";
                            echo "<input id=\"plandate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"PlanDate-".$stage."\" value=\"".$date["PlanDate"]."\">";
                            if($filter) {
                                echo "<input id=\"planenddate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"PlanEndDate-".$stage."\" value=\"".@$date["PlanEndDate"]."\">";
                            }
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["PlanColor"]) == 0) {
                                    echo "<div class=\"stage_color\" style=\"background-color:".$color.";\"><input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"vertical-align:middle;\">".$key."</div>";
                                } else {
                                    echo "<div class=\"stage_color\" style=\"background-color:".$color.";\"><input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" style=\"vertical-align:middle;\">".$key."</div>";
                                }
                            }
                            echo "</td>";
                            echo "<td class=\"stage_date\" style=\"background:".$date["RealColor"]."\">";
                            echo "<input id=\"realdate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"RealDate-".$stage."\" value=\"".$date["RealDate"]."\">";
                            if($filter) {
                                echo "<input id=\"realenddate_".$stage."\" class=\"input_ajax\" type=\"text\" name=\"RealEndDate-".$stage."\" value=\"".@$date["RealEndDate"]."\">";
                            }
                            foreach($colors as $key => $color){
                                if(strcmp($color,$date["RealColor"]) == 0) {
                                    echo "<div class=\"stage_color\" style=\"background-color:".$color.";\"><input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" checked=\"checked\" style=\"vertical-align:middle;\">".$key."</div>";
                                } else {
                                    echo "<div class=\"stage_color\" style=\"background-color:".$color.";\"><input type=\"radio\" name=\"RealColor-".$stage."\" value=\"".$color."\" style=\"vertical-align:middle;\">".$key."</div>";
                                }
                            }
                            echo "</td>";
                        }
?>
                    <td class="note"  style="background-color:khaki;">
                    <div class="note_div">
                        <textarea id="note" class="textarea_ajax" name="note" row="5"><?PHP echo @$update["note"]?></textarea>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>" id="add_filter" style="background-color:khaki;">
                    </td>
                </tr>
                <tr>
                    <td colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>" style="text-align:center;background-color: khaki;"><INPUT type="hidden" name="id" value="<?PHP echo @$update["id"]?>" />
<?PHP
if($id):
?>
<?PHP
                if(@$update["finish"] == 0 && @$update["deleted"] == 0){
                        echo "<INPUT type=\"SUBMIT\" value=\"更新\" onClick=\"$.submit();\" />";
                        echo "<INPUT type=\"BUTTON\" value=\"取消\" onClick=\"$.edit_cancel();\" />";
                        echo "---";
                }

                if(@$update["finish"] == 0 ){
                    if(@$update["deleted"] == 0 ){
                        echo "<INPUT type=\"BUTTON\" value=\"标记[己完成]\" style=\"background:red;\" onClick=\"$.finish(".@$update["id"].");\" />";
                    }
                } else {
                    echo "<INPUT type=\"BUTTON\" value=\"标记[未完成]\" style=\"background:yellow;\" onClick=\"$.finish(".@$update["id"].",true);\" />";
                }

                if(@$update["deleted"] == 0 ){
                    if(@$update["finish"] == 0 ){
                        echo "<INPUT type=\"BUTTON\" value=\"删除\" style=\"background:red;\" onClick=\"$.delete(".@$update["id"].");\" />";
                    }
                } else {
                    echo "<INPUT type=\"BUTTON\" value=\"还原\" style=\"background:yellow;\" onClick=\"$.delete(".@$update["id"].",true);\" />";
                }

else:

    if($filter):
?>
                        <INPUT type="SUBMIT" value="搜索" />
                        <INPUT type="BUTTON" value="清空条件" onClick="$.clean_filter_plan();"/>
                        <INPUT type="BUTTON" value="收起面板" onClick="$.hide_filter();" />

<?PHP
            if($_SESSION["admin"] == true && $allow->pass()):
                        echo ($show_save_filter) ? "<INPUT type=\"BUTTON\" value=\"将当前Filter设置为默认\" onClick=\"$.save_filter();\" />" : "";
                        echo ($find_global_filter) ? "<INPUT type=\"BUTTON\" value=\"清空默认Filter过滤\" onClick=\"$.unsave_filter();\" />" : "";
            endif;

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
                                    echo "<input type=\"radio\" name=\"PlanColor-".$stage."\" value=\"".$color."\" checked=\"checked\">".$key;
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
                    <td><textarea name="note" class=\"textarea_ajax\" row="5"><?PHP echo @$update["note"]?></textarea></td>
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
   <script>
       $(document).ready(function(){
        <?PHP
            foreach($config["STAGE"] as $stage){
                if($filter) {
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
       $.col_width();
       });
   </script>
<?PHP
?>
