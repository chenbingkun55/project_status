<?PHP
/**
* Author: @-ChenBk <chenbingkun55@163.com> 2015-09-19 16:28
* Version Info ------------------------------------------------
e225fb6 * 发布版本 0.1
:100644 100644 313dac9... 680f676... M	index.php
78a822c * 时间过滤，Color过滤功能。
:100644 100644 d402ef2... 313dac9... M	index.php
12b7b65 [BUG] 在编辑模式下, 打开的row编辑时,切换到只读模式, 没有撤消编辑,导到只读模式下, 关不掉row编辑.
:100644 100644 156995e... d402ef2... M	index.php
14d3915 * filter 完成一半, stage date 这块搜索比较困难.
:100644 100644 a90aa62... 156995e... M	index.php
366c0ce * 修改Export Excel时不加载css样式.
:100644 100644 cf9578b... a90aa62... M	index.php
c184bcd * 修复搜索面板id和添加修改冲突BUG
:100644 100644 f3a12de... cf9578b... M	index.php
4b757e6 * 合并冲突问题处理。
:100644 100644 799791d... f3a12de... M	index.php
3c503c1 * 添加搜垵面板。
:100644 100644 98c5c7e... d6168a3... M	index.php
cebfe57 * 添加index.php导出Excel功能。
:100644 100644 999741f... 98c5c7e... M	index.php
b0e12bd * 添加index.php改出Excel。
:100644 100644 999741f... 98c5c7e... M	index.php
04cc415 * 使用Session保存编辑状态
:100644 100644 eaa3965... 999741f... M	index.php
22dcde2 * 添加己完成确认弹框。
:100644 100644 7403026... eaa3965... M	index.php
49c6355 + jquery.datepicker 日期选择器。 + get_names.php 取项目名称下拉列表。
:100644 100644 10f3362... 7403026... M	index.php
598952c * 基本功能完成.
:100644 100644 27c7002... 10f3362... M	index.php
f04540a * 加载 tablestore * 添加、删除、修改功能
:100644 100644 c55ec86... 27c7002... M	index.php
5d6ab00 * 初始化Dev
:000000 100644 0000000... c55ec86... A	index.php
* -------------------------------------------------------------
*
**/

session_start();

    include "lib.php";
    $status = trim(@$_REQUEST["status"]);
    $filter = (strcmp(@$_REQUEST["filter"],"1") == 0 || strcmp(@$_REQUEST["filter_submit"],"1") == 0) ? true : false;

    if($filter) {
        $tbl_data = $mysql->filter();
    } else {
	switch($status) {
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
    }

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
           $.extend({notify:function(notify_text,long){
               var set_time = 3000;
               if(long) {
                   set_time = 20000;
               }

               $("#main_notify").html(notify_text);
               $("#main_notify").stop().fadeIn();
               $("#main_notify").fadeOut(set_time);
            }});

            $.extend({RGBToHex:function(rgb){
               var regexp = /[0-9]{0,3}/g;
               var re = rgb.match(regexp);//利用正则表达式去掉多余的部分，将rgb中的数字提取
               var hexColor = "#"; var hex = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
               for (var i = 0; i < re.length; i++) {
                    var r = null, c = re[i], l = c;
                    var hexAr = [];
                    while (c > 16){
                          r = c % 16;
                          c = (c / 16) >> 0;
                          hexAr.push(hex[r]);
                     } hexAr.push(hex[c]);
                     if(l < 16&&l != ""){
                         hexAr.push(0)
                     }
                   hexColor += hexAr.reverse().join('');
                }
               //alert(hexColor)
               return hexColor;
            }});


           $.extend({clean_filter_plan:function(notify_text){
                $("#name").attr("value","");
                $("#theme_function").attr("value","");
                $("#version").attr("value","");
                $("#status").find("option:selected").removeAttr("selected");
                $("#stage").find("option:selected").removeAttr("selected");
                $(".stage_color").removeAttr("checked");
                $("#note").text("");
                $("#include_deleted").removeAttr("checked");
                $("#include_finish").removeAttr("checked");
                $("#note_empty").removeAttr("checked");
<?PHP
                foreach($config["STAGE"] as $stage){
                    echo "$(\"#plandate_".$stage."\").attr(\"value\",\"\");";
                    echo "$(\"#planenddate_".$stage."\").attr(\"value\",\"\");";
                    echo "$(\"#realdate_".$stage."\").attr(\"value\",\"\");";
                    echo "$(\"#realenddate_".$stage."\").attr(\"value\",\"\");";
                }
?>
                $.get("common.php?opt=clean_filter",function(data,status){
                    if(status == "success") {
                        $.notify("清空过滤面板条件完成");
                    }
                });

           }});

           $.extend({export:function(){
<?PHP
            if(trim(@$_REQUEST["status"])){
               echo "window.open(window.location.href + \"&export=1\");";
            } else if($filter) {
               echo "window.open(window.location.href + \"?export=1&filter=1\");";
            } else {
  		echo "window.open(window.location.href + \"?export=1\");";
	     }
?>
           }});

           $.extend({add_filter:function(){
               $("#add_filter").load('admin.php?add_filter=1');
           }});


           $.extend({show_filter:function(){
               if(row_edit_bool) {
                   $.notify("<span style=\"color: red; font-size: 24px;\">表格编辑中,请先取消编辑.</span>");
                   return;
               }

               $("#filter").load('admin.php?filter=1').slideToggle(500,function(){
                   $("#filter_add_img").toggle();
               });
           }});

           $.extend({hide_filter:function(){
               $("#filter").empty().slideUp(1000);
               $("#filter_add_img").hide();
           }});


           $.extend({model_switch:function(){
               // 默认打开只读模式
               var read_only = $("#model_status").attr("enable");
               if(read_only == 1){
                    $.get("common.php?opt=model_status&enable=0",function(data,status){
                       if(status == "success") {
                           $.edit_cancel();
                           $.notify("进入只读模式");
                           model_edit = false;
                           $("#model_status").attr("enable","0");
                           $("#model_text").html("<button class=\"minimal\">只读模式</button>");
                       }
                    });
               } else {
                    $.get("common.php?opt=model_status&enable=1",function(data,status){
                       if(status == "success") {
                           $.notify("进入编辑模式<BR>[添加]: 双击表格标头.<BR>[修改]: 双击要编辑的行.",true);
                           model_edit = true;
                           $("#model_status").attr("enable","1");
                           $("#model_text").html("<button class=\"cupid-green\">编辑模式</button>");
                       }
                    });
               }
           }});

           $.extend({list_names:function(){
                 var $list_name = $("<ul class='autocomplete'></ul>").hide().insertAfter("#name");
                 var $name = $('#name');

                 $.getJSON("common.php?opt=get_names",function(items,status){
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

           $.extend({finish:function(fid,unfinish){
             if(unfinish == true) {
               $.get("admin.php?finish="+fid,function(date,status){
                   if(status == "success") {
                       $.notify('己标记未完成');
                       setTimeout("window.location.reload();", 1000);
                   }
               });
             } else {
               $.get("admin.php?finish="+fid,function(date,status){
                   if(status == "success") {
                       $.notify('己标记完成');
                       setTimeout("window.location.reload();", 1000);
                   }
               });
             }
             $.edit_cancel();
           }});

           $.extend({delete:function(did,revert){
             if(revert == true) {
               $.get("admin.php?deleted="+did,function(date,status){
                   if(status == "success") {
                       $.notify('还原成功');
                       setTimeout("window.location.reload();", 1000);
                   }
               });
             } else {
                 if(confirm("是否删除") == true) {
                   $.get("admin.php?deleted="+did,function(date,status){
                       if(status == "success") {
                           $.notify('删除成功');
                           setTimeout("window.location.reload();", 1000);
                       }
                   });
                  }
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

           $('table.tablesorter td').mouseover(function(){
               var td_index = $(this).index();
               $("#project_status_list tr:not(:first) td:nth-child("+(td_index + 1)+")").each(function(){
                   var color = $.RGBToHex($(this).css("background-color"));
                   if(color != "#FF0000" && color != "#008000" && color != "#FFFF00") {
                       $(this).css("background-color","khaki");
                   }
               });

               $(this).siblings().each(function(){
                   var color = $.RGBToHex($(this).css("background-color"));
                   if(color != "#FF0000" && color != "#008000" && color != "#FFFF00") {
                       $(this).css("background-color","khaki");
                   }
               });
           }).mouseout(function(){
               var td_index = $(this).index();
               $("#project_status_list tr:not(:first) td:nth-child("+(td_index + 1)+")").each(function(){
                   var color = $.RGBToHex($(this).css("background-color"));
                   if(color == "#F0E68C") {
                       $(this).css("background-color","");
                   }
               });

               $(this).siblings().each(function(){
                   var color = $.RGBToHex( $(this).css("background-color"));
                   if(color == "#F0E68C") {
                       $(this).css("background-color","");
                   }
               });
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
        switch($status) {
            case "all":
                echo "$(\"#all\").attr(\"class\",\"cupid-green\");";
                break;
            case "deleted":
                echo "$(\"#deleted\").attr(\"class\",\"cupid-green\");";
                break;
            case "finish":
                echo "$(\"#finish\").attr(\"class\",\"cupid-green\");";
                break;
            case "in_process":
                echo "$(\"#in_process\").attr(\"class\",\"cupid-green\");";
                break;
            default :
                echo "$(\"#in_process\").attr(\"class\",\"cupid-green\");";
        }

        if($filter) {
            echo "$(\"#filter_img\").attr(\"src\",\"public/img/filter_yes_24x24.png\");";
            echo "$(\"#in_process\").attr(\"class\",\"minimal\");";
            echo "$.show_filter();";
        }

        if(@$_SESSION["model_edit"] == 1) {
            echo "model_edit = true;";
            echo "$(\"#model_status\").attr(\"enable\",\"1\");";
            echo "$(\"#model_text\").html(\"<button class='cupid-green'>编辑模式</button>\");";
        } else {
            echo "model_edit = false;";
            echo "$(\"#model_status\").attr(\"enable\",\"0\");";
            echo "$(\"#model_text\").html(\"<button class='minimal'>只读模式</button>\");";
        }
?>

        //$(".note_td").ellipsis({maxWidth:300,maxLine:2});
        // 备注显示在一行，鼠标Over 显示全部。
        $(".note_td").mouseover(function(){
            $(this).prev().css("display","inline-block");
            $(this).prev().css("z-index","1");
        });

        $(".note_td_full").mouseout(function(){
            $(".note_td_full").css("display","none");
            $(".note_td_full").css("z-index","-1");
        });
       });
       </script>
    </HEAD>
    <BODY>
        <div class="main_notify" id="main_notify"></div>
        </div>
        <table id="project_status_list" class="tablesorter">
            <thead>
                <tr>
                <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>" style="font-size:16px;text-align:center;height:60px;">
                    <h1><a href="http://bzb.igg.com"><img class="logo" src="http://192.168.23.220/food_order/public/images/BZBee%20logo%20100X100.png" /></a>&nbsp;BZBee Productions 项目状态</h1>
                </th>
                </tr>
<?PHP
    if(strcmp(@$_REQUEST['export'],"1") != 0):
?>
                <tr>
                    <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>" style="background-color: khaki;">
                        <div class="function">
                            <div class="filter_plan">
                                <div class="php">
                                    <span onClick="$.show_filter();"><img id="filter_img" src="public/img/filter_24x24.png" style="vertical-align: middle;"></span>&nbsp;
                                    <!--<span onClick="$.add_filter();"><img id="filter_add_img" src="public/img/filter_add_24x24.png" style="vertical-align: middle;display: none;"></span>&nbsp;-->
                                    <a href="index.php?status=in_process"><button id="in_process" class="minimal">进行中</button></a>&nbsp;
                                    <a href="index.php?status=all"><button id="all" class="minimal">所有</button></a>
                                    <a href="index.php?status=finish"><button id="finish" class="minimal">己完成</button></a>&nbsp;
                                    <a href="index.php?status=deleted"><button id="deleted" class="minimal">己删除</button></a>&nbsp;
                                </div>
                            </div>
                            <div class="export" onClick="$.export();">
                                <button class="minimal">导出 Excel</button>
                            </div>
<?PHP
        if($allow->pass()):
?>
                            <div class="model_text" id="model_text" onClick="$.model_switch();">
                                <button class="minimal">只读模式</button>
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
                            $tag = "<td><s><del>%s</del></s></td>";
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
                        $note_td_full = str_replace("\n","<br>",$row["note"]);
                        $note_td = $row["note"];
                        echo "<td><div class=\"note_td_full\">".$note_td_full."</div><div class=\"note_td\">".$note_td."</div></td>";
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
