<?PHP
session_start();

    include "lib.php";
    if(strcmp(@$_REQUEST["filter"],"1") != 0) {
        switch(trim(@$_REQUEST["status"])) {
            case "all":
                $tbl_data = $mysql->get_all();
                break;
            case "deleted":
                $tbl_data = $mysql->get_deleted();
                break;
            case "finish":
                $tbl_data = $mysql->get_finish();
                break;
            case "in_process":
                $tbl_data = $mysql->get_in_process();
                break;
            default :
                $tbl_data = $mysql->get_in_process();
        }
    } else {
        $tbl_data = $mysql->filter();
    }

    print_r($_SESSION["filter_array"]);

    // 导出Html表格到 Excel
    if(strcmp(@$_REQUEST['export'],"1") == 0) {
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=export_project_status.xls");
    }
?>
<!DOCTYPE HTML>
    <HEAD>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <meta http-equiv="pragma" content="no-cache" />
       <meta http-equiv="cache-control" content="no-cache" />
       <TITLE> <?PHP echo $config["SITE_NAME"] ?></TITLE>
       <!--<link type="text/css" rel="stylesheet" href="public/css/theme.default.css" />-->
<?PHP
    if(strcmp(@$_REQUEST['export'],"1") != 0):
?>
       <link type="text/css" rel="stylesheet" href="public/css/blue/style.css" />
       <link type="text/css" rel="stylesheet" href="public/css/common.css" />
<?PHP
    endif;
?>

       <!--<script type="text/javascript" src="public/js/jquery-1.2.6.min.js"></script>-->
       <script type="text/javascript" src="public/js/jquery-2.1.4.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.tablesorter.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.tablesorter.widgets.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.metadata.js"></script>
       <script type="text/javascript" src="public/js/jquery.datepicker.min.js"></script>
       <script>

       $(document).ready(function(){
           $.extend({notify:function(notify_text){
               $("#main_notify").html(notify_text);
               $("#main_notify").stop().fadeIn();
               $("#main_notify").fadeOut(2500);
           }});

           $.extend({clean_filter_plan:function(notify_text){
                $("#name").attr("value","");
                $("#theme_function").attr("value","");
                $("#version").attr("value","");
                $("#status").find("option[text='空']").attr("selectd",true);
                $("#stage").find("option[text='空']").attr("selectd",true);
                $("#note").text("");
                $("#include_deleted").attr("value","");
                $("#include_finish").attr("value","");
                $("#note_empty").attr("value","");
           }});

           $.extend({export:function(){
<?PHP
            if(trim(@$_REQUEST["status"])){
               echo "window.open(window.location.href + \"&export=1\");";
            } else {
               echo "window.open(window.location.href + \"?export=1\");";

            }
?>
           }});

           $.extend({show_filter:function(){
               if(row_edit_bool) {
                   $.notify("<span style=\"color: red; font-size: 24px;\">表格编辑中,请先取消编辑.</span>");
                   return;
               }

               $("#filter").load('admin.php?filter=1').slideToggle(500);
           }});

           $.extend({hide_filter:function(){
               $("#filter").empty().slideUp(1000);
           }});

           $.extend({export:function(){
               window.open("index.php?export=1");
           }});

           $.extend({model_switch:function(){
               // 默认打开只读模式
               var read_only = $("#model_status").attr("enable");
               if(read_only == 1){
                    $.get("model_status.php?enable=0",function(data,status){
                       if(status == "success") {
                           $.notify("进入只读模式");
                           model_edit = false;
                           $("#model_status").attr("enable","0");
                           $("#model_text").text("只读模式");
                           $.edit_cancel();
                       }
                    });
               } else {
                    $.get("model_status.php?enable=1",function(data,status){
                       if(status == "success") {
                           $.notify("进入编辑模式<BR>[添加]: 双击表格标头.<BR>[修改]: 双击要编辑的行.");
                           model_edit = true;
                           $("#model_status").attr("enable","1");
                           $("#model_text").text("编辑模式");
                       }
                    });
               }
           }});

           $.extend({list_names:function(){
                 var $list_name = $("<ul class='autocomplete'></ul>").hide().insertAfter("#name");
                 var $name = $('#name');

                 $.getJSON("get_names.php",function(items,status){
                      $list_name.empty();

                      $.each(items, function(i, item)
                      {
                          $("<li></li>").text(item.name).appendTo($list_name).mouseover(function(){
                              $(this).css("background","#000000");
                          }).mouseout(function(){
                              $(this).css("background","#FFFFFF");
                          }).click(function(){
                              $name.val($(this).text());
                              $list_name.hide();
                          });
                          $list_name.hide();
                      });
                 $list_name.show();
                 });
           }});

           $.extend({edit_cancel:function(){
               $('.show_opt').parent().find("td").each(function(){
                   $(this).show();
               });

               $.notify('编辑取消');
               $('.show_opt').remove();
               row_edit_bool = false;
           }});

           $.extend({finish:function(fid){
             if(confirm("是否己完成") == true) {
               $.get("admin.php?finish="+fid,function(date,status){
                   if(status == "success") {
                       $.notify('己标记完成');
                       setTimeout("window.location.reload();", 1000);
                   }
               });
             }
             $.edit_cancel();
           }});

           $.extend({delete:function(did){
             if(confirm("是否删除") == true) {
               $.get("admin.php?deleted="+did,function(date,status){
                   if(status == "success") {
                       $.notify('删除成功');
                       setTimeout("window.location.reload();", 1000);
                   }
               });
              }
              $.edit_cancel();
           }});

           $.extend({submit:function(){
              $("form").submit(function(e){
                $.notify('更新成功');
              });
           }});

           var row_edit_bool = false;
           var model_status = false;
           //第一列不进行排序(索引从0开始)
           $.tablesorter.defaults.headers = {
               0: {sorter: false},
               1: {sorter: false},
               7: {sorter: false},
               8: {sorter: false},
               9: {sorter: false},
               10: {sorter: false},
           };
           $("#project_status_list").tablesorter({
                widgets        : ['zebra', 'columns'],
                usNumberFormat : false,
                sortReset      : true,
                sortRestart    : true
           });

            $("table.tablesorter th").dblclick(function(){
               if(! model_edit) return;

               if(row_edit_bool){
                   row_edit_bool = false;
                   $.edit_cancel();
               } else {
                   $.notify('添加 Row');
                   $.get("admin.php",function(data,status){
                       if(status != "success") {
                           $.notify('添加失败');
                       } else {
                           $("table.tablesorter").append("<td class=\"show_opt\" colspan=\"14\">"+data+"</td>");
                       }
                   });
                   row_edit_bool = true;
                   $.hide_filter();
               }
               $('.show_opt').remove();
            });


           $('table.tablesorter td').dblclick(function(){
               if(! model_edit) return;

               var edit_row = $(this).parent();
               var show_opt = "<tr class=\"show_opt\"><td style=\"text-align:center\" colspan=\"14\">[<a href=\"admin.php?id="+edit_row.attr("id")+"\">修改</a>] [删除] [己完成]<td></tr>";

               if(row_edit_bool){
                   $('.show_opt').parent().find("td").each(function(){
                       $(this).show();
                   });

                   row_edit_bool = false;
               } else {
                   first_td = edit_row.find("td").first();
                   $.notify('编辑 Row: ['+edit_row.attr("id")+'] <BR>项目: ' + first_td.text()+"<BR>主题/功能: "+ first_td.next().text());
                   edit_row.find("td").each(function(){
                       $(this).hide();
                   });

                   $.get("admin.php?id="+edit_row.attr("id"),function(data,status){
                       if(status != "success") {
                           $.notify('更新失败');
                       } else {
                           edit_row.append("<td class=\"show_opt\" colspan=\"14\">"+data+"</td>");
                       }
                   });
                   row_edit_bool = true;
                   $.hide_filter();
               }
               $('.show_opt').remove();
           });
<?PHP
        if(@$_SESSION["model_edit"] == 1) {
            echo "model_edit = true;";
            echo "$(\"#model_status\").attr(\"enable\",\"1\");";
            echo "$(\"#model_text\").text(\"编辑模式\");";
        } else {
            echo "model_edit = false;";
            echo "$(\"#model_status\").attr(\"enable\",\"0\");";
            echo "$(\"#model_text\").text(\"只读模式\");";
        }
?>
       });
       </script>
    </HEAD>
    <BODY>
        <div class="main_notify" id="main_notify"></div>
        <div class="main_header" id="main_header"></div>
        <table id="project_status_list" class="tablesorter">
            <thead>
                <tr>
                <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>" style="font-size:16px;text-align:center;height:60px;">项目状态</th>
                </tr>
<?PHP
    if(strcmp(@$_REQUEST['export'],"1") != 0):
?>
                <tr>
                    <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>">
                    <div class="function">
                        <div class="filter_plan">
                            <div class="php">
                                <span onClick="$.show_filter();"><img src="public/img/filter_24x24.png" style="vertical-align: middle;"></span>&nbsp;
                                <a href="index.php?status=in_process">进行中</a>&nbsp;
                                <a href="index.php?status=all">所有</a>
                                <a href="index.php?status=finish">己完成</a>&nbsp;
                                <a href="index.php?status=deleted">己删除</a>&nbsp;
                            </div>
                        </div>
                        <div class="export" onClick="$.export();">
                            导出 Excel
                        </div>
<?PHP
    if($allow->pass()):
?>
                        <div class="model_text" id="model_text" onClick="$.model_switch();">
                            只读模式
                        </div>
<?PHP
    endif;
?>
                    </div>
<?PHP
    if($allow->pass()):
?>
                    <div class="admin">
                        <div class="model_status" id="model_status" enable="0">
                        </div>
                    </div>
<?PHP
    endif;
?>
                    <div id="filter" class="filter">
                    </div>
                    </th>
                </tr>
<?PHP
    endif;
?>
                <tr>
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
            </thead>
            <tbody>
                <?PHP
                    foreach($tbl_data as $row){
                        if(strcmp($row["deleted"],0) != 0) {
                            $tag = "<td><del>%s</del></td>";
                        } else if(strcmp($row["finish"],0) != 0) {
                            $tag = "<td><i><u>%s</u></i></td>";
                        }else {
                            $tag = "<td>%s</td>";
                        }
                        echo "<tr id=\"".$row["id"]."\">";
                        printf($tag,$row["name"]);
                        printf($tag,$row["theme_function"]);
                        printf($tag,$row["version"]);
                        printf($tag,$row["status"]);
                        printf($tag,$row["stage"]);

                        $stage_data = $stage_json->decode($row['stage_date_json']);
                        $stage_tag = "<td style=\"background:%s\">%s</td>";
                        foreach($config["STAGE"] as $stage){
                            printf($stage_tag,$stage_data[$stage]["PlanColor"],$stage_data[$stage]["PlanDate"]);
                            printf($stage_tag,$stage_data[$stage]["RealColor"],$stage_data[$stage]["RealDate"]);
                        }
                        echo "<td>".$row["note"]."</td>";
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
