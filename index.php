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

    // 允许IP访问.
    if(! $allow->is_allow_ip()) die("<H1 style=\"color:red;\">This ip [".$_SERVER["REMOTE_ADDR"]."] access deny!</H1>请联系管理员: bingkunchen");

    // 管理员模式
    $_SESSION["admin"] = $allow->pass();

    $col_num = 6 + count($config["STAGE"]) * 2;
    $status = trim(@$_REQUEST["status"]);
    $filter_submit = (strcmp(@$_REQUEST["filter_submit"],"1") == 0) ? true : false;
    $filter_status = (strcmp(@$_REQUEST["filter_status"],"") == 0) ? "" : trim(@$_REQUEST["filter_status"]);
    $filter_stage = (strcmp(@$_REQUEST["filter_stage"],"") == 0) ?  "" : trim(@$_REQUEST["filter_stage"]);
    $filter = (strcmp(@$_REQUEST["filter"],"1") == 0 || $filter_submit) ? true : false;
    $find_global_filter = find_global_filter();
    $export_bool = (strcmp(@$_REQUEST['export'],"1") == 0) ? true : false;
    $load_filter = false;

    if($filter || $filter_status || $filter_stage) {
        if(strcmp($filter_stage,"") != 0) {
            $tbl_data = $mysql->filter(false,$filter_stage,"");
            $_SESSION["chart_bool"] = false;
        } else if(strcmp($filter_status,"") != 0){
            $tbl_data = $mysql->filter(false,"",$filter_status);
            $_SESSION["chart_bool"] = false;
        } else {
            $tbl_data = $mysql->filter();
        }
    } else if($find_global_filter && empty($status)){
        if(load_filter()) {
            $load_filter = true;
            $tbl_data = $mysql->filter($load_filter);
        }
    } else {
        switch($status) {
            case "all":
                $tbl_data = $mysql->get_all();
                $chart_title = "所有";
                break;
            case "deleted":
                $tbl_data = $mysql->get_deleted();
                $chart_title = "己删除";
                break;
            case "finish":
                $tbl_data = $mysql->get_finish();
                $chart_title = "己完成";
                break;
            case "in_process":
                $tbl_data = $mysql->get_in_process();
                $chart_title = "进行中";
                break;
            default :
                $tbl_data = $mysql->get_in_process();
                $chart_title = "进行中";
        }
    }

    // 导出Html表格到 Excel
    if($export_bool) {
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
    if(! $export_bool):
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
       <script type="text/javascript" src="public/js/highcharts.js"></script>
       <script>

       $(document).ready(function(){
           $.extend({col_width:function(){
               var body_width = $(document.body).width();
               if(body_width < 1600){
                   $("body").css("width","1600px");
                   body_width = 1600;
               }
               $(".main_notify").css("left",(body_width/10*6)+"px");
               $(".theme_function").css("width",(body_width/7)+"px");
               $(".theme_function").find("div").css("width",(body_width/7)+"px");
               $(".note").css("width",(body_width/20*2)+"px");
               $(".note").find("div").css("width",(body_width/20*2)+"px");
           }});

           $.extend({notify:function(notify_text,long){
               var set_time = 3000;
               if(long) {
                   set_time = 10000;
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
                $(".stage_color").find("input").removeAttr("checked");
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
            } else if($filter || $load_filter) {
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
<?PHP
    $get_filter_plan = "";
    if($filter_submit){
        $get_filter_plan .= "&show_save_filter=1";
    }

    if($find_global_filter){
        $get_filter_plan .= "&find_global_filter=1";
    }

    echo "$(\"#filter\").load(\"admin.php?filter=1".$get_filter_plan."\").slideToggle(500,function(){";
?>
                   $("#filter_add_img").toggle();
                   show_filter_bool = true;
               });
           $.col_width();
           }});

           $.extend({hide_filter:function(){
               $("#filter").empty().slideUp(1000);
               $("#filter_add_img").hide();
               show_filter_bool = false;
           }});

           $.extend({save_filter:function(){
                $.get("common.php?opt=save_filter",function(data,status){
                   if(status == "success") {
                       $.notify("保存Filter 成功");
                   }
                });

           }});

           $.extend({unsave_filter:function(){
                $.get("common.php?opt=unsave_filter",function(data,status){
                   if(status == "success") {
                       $.notify("取消Filter 成功");
                   }
                });

           }});

           $.extend({show_chart:function(){
                $.get("common.php?opt=show_chart",function(data,status){
                   if(status == "success") {
                       if(chart_bool){
                           $.notify("表格模式。");
                       } else {
                           $.notify("图表模式。");
                       }
                       setTimeout("window.location.reload();", 1000);
                   }
                });


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
                           $.notify("进入编辑模式<BR>[添加\\修改]: 双击表格标题或行<BR>[触屏]: Touch表格标题或行下拉.",true);
                           model_edit = true;
                           $("#model_status").attr("enable","1");
                           $("#model_text").html("<button class=\"cupid-green\" title=\"只读\\编辑模式切换按钮\">编辑模式</button>");
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
           var show_filter_bool = false;
           var model_status = false;
           var touch_on = true;
           //第一列不进行排序(索引从0开始)
           $.tablesorter.defaults.headers = {
               0: {sorter: false},
               1: {sorter: false},
               2: {sorter: false},
               7: {sorter: false},
               8: {sorter: false},
               9: {sorter: false},
               10: {sorter: false},
               11: {sorter: false},
           };
           $("#project_status_list").tablesorter({
                widgets        : ['zebra', 'columns'],
                usNumberFormat : false,
                sortReset      : true,
                sortRestart    : true,
                headerClass: 'header',
           });
<?PHP
        if($_SESSION["admin"] == true && $allow->pass()):
?>

            $("table.tablesorter").on("touchstart", function(e) {
                startX = e.originalEvent.changedTouches[0].pageX,
                startY = e.originalEvent.changedTouches[0].pageY;
                touch_on = true;
            });

            $("table.tablesorter").on("touchmove", function(e) {
                //e.preventDefault();
                moveEndX = e.originalEvent.changedTouches[0].pageX,
                moveEndY = e.originalEvent.changedTouches[0].pageY,
                X = moveEndX - startX,
                Y = moveEndY - startY;

                if ( Math.abs(X) > Math.abs(Y) && X > 0 ) {
                    touch_on = true; // 向右
                }
                else if ( Math.abs(X) > Math.abs(Y) && X < 0 ) {
                    touch_on = true; // 向左
                }
                else if ( Math.abs(Y) > Math.abs(X) && Y > 0) {
                    touch_on = false; // 向下
                }
                else if ( Math.abs(Y) > Math.abs(X) && Y < 0 ) {
                    touch_on = true; // 向上
                }
                else{
                    touch_on = true;
                }
            });

            $("table.tablesorter th").bind("touchend dblclick",function(){
               if(! model_edit) return;
               if(touch_bool && touch_on) return;

               if(row_edit_bool){
                   row_edit_bool = false;
                   $.edit_cancel();
               } else {
                   $.notify('添加 Row');
                   $.get("admin.php",function(data,status){
                       if(status != "success") {
                           $.notify('添加失败');
                       } else {
                           $("table.tablesorter").append("<td class=\"show_opt\" colspan=\"<?PHP echo $col_num; ?>\">"+data+"</td>");
                       }
                   });
                   row_edit_bool = true;
                   $.hide_filter();
               }
               $('.show_opt').remove();
            });
           $('table.tablesorter td').bind("touchend dblclick",function(){
               if(! model_edit) return;
               if(touch_bool && touch_on) return;

               var edit_row = $(this).parent();
               var show_opt = "<tr class=\"show_opt\"><td style=\"text-align:center\" colspan=\"<?PHP echo $col_num; ?>\">[<a href=\"admin.php?id="+edit_row.attr("id")+"\">修改</a>] [删除] [己完成]<td></tr>";

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
                           edit_row.append("<td class=\"show_opt\" colspan=\"<?PHP echo $col_num; ?>\">"+data+"</td>");
                       }
                   });
                   row_edit_bool = true;
                   $.hide_filter();
               }

               $('.show_opt').remove();
           });
<?PHP
endif;
?>

<?PHP
        echo "$.col_width();";
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

        if($filter || $load_filter) {
            echo "$(\"#filter_img\").attr(\"src\",\"public/img/filter_yes_24x24.png\");";
            echo "$(\"#in_process\").attr(\"class\",\"minimal\");";
            echo "$.show_filter();";
            if($load_filter) {
                echo "$.notify(\"当前使用的是默认过滤器。\",\"true\");";
            }
        }

        if(@$_SESSION["model_edit"] == true) {
            echo "var model_edit = true;";
            echo "$(\"#model_status\").attr(\"enable\",\"1\");";
            echo "$(\"#model_text\").html(\"<button class='cupid-green' title='只读\\编辑模式切换按钮'>编辑模式</button>\");";
        } else {
            echo "var model_edit = false;";
            echo "$(\"#model_status\").attr(\"enable\",\"0\");";
            echo "$(\"#model_text\").html(\"<button class='minimal' title=\"只读\\编辑模式切换按钮\">只读模式</button>\");";
        }

        if(@$_SESSION["chart_bool"] == true) {
            echo "var chart_bool = true;";
        } else {
            echo "var chart_bool = false;";
        }
?>

           $('table.tablesorter td').mouseover(function(){
               if(row_edit_bool || show_filter_bool) return;

               var td_index = $(this).index();
               $("#project_status_list tr:not(:first) td:nth-child("+(td_index + 1)+")").each(function(){
                   var color = $.RGBToHex($(this).css("background-color"));
                   if(color != "#FF0000" && color != "#3CB371" && color != "#ADFF2F") {
                       $(this).css("background-color","LightSkyBlue");
                   }
               });

               $(this).siblings().each(function(){
                   var color = $.RGBToHex($(this).css("background-color"));
                   if(color != "#FF0000" && color != "#3CB371" && color != "#ADFF2F") {
                       $(this).css("background-color","LightSkyBlue");
                   }
               });
           }).mouseout(function(){
               if(row_edit_bool || show_filter_bool) return;

               var td_index = $(this).index();
               $("#project_status_list tr:not(:first) td:nth-child("+(td_index + 1)+")").each(function(){
                   var color = $.RGBToHex($(this).css("background-color"));
                   if(color == "#87CEFA") {
                       $(this).css("background-color","");
                   }
               });

               $(this).siblings().each(function(){
                   var color = $.RGBToHex( $(this).css("background-color"));
                   if(color == "#87CEFA") {
                       $(this).css("background-color","");
                   }
               });
           });

        // 判断是否支持 Touch 屏。
        function is_touch_device() {
             return !!('ontouchstart' in window);
        }

        var touch_bool = is_touch_device();

        //$(".note").ellipsis({maxWidth:300,maxLine:2});
        // 备注显示在一行，鼠标Over 显示全部。
        $("table.tablesorter td div").mouseover(function(){
            $(this).css("overflow","auto");
        }).mouseout(function(){
            $(this).css("overflow","hidden");
        });
       });

       $("document").ready(function(){
           $.col_width();
       });
       </script>
    </HEAD>
    <BODY>
        <div class="main_notify font-notify" id="main_notify"></div>
        </div>
        <table id="project_status_list" class="tablesorter font-face-display">
            <thead>
                <tr>
                <th colspan="<?PHP echo $col_num; ?>" style="font-size:16px;text-align:center;height:80px;">
                    <div class="font-header"><a href="http://bzb.igg.com"><img class="logo" src="http://192.168.23.220/food_order/public/images/BZBee%20logo%20100X100.png" /></a>&nbsp;BZBee Productions 项目状态</div>
                </th>
                </tr>
<?PHP
    if(! $export_bool):
?>
                <tr>
                    <th colspan="<?PHP echo $col_num; ?>" style="background-color: khaki;">
                        <div class="function">
                            <div class="filter_plan">
                                <div class="php">
                                    <span onClick="$.show_filter();"><img id="filter_img" title="显示\隐藏过滤面板" src="public/img/filter_24x24.png" style="vertical-align: middle;"></span>&nbsp;
                                    <span><img id="chart_img" src="public/img/chart_24x24.png" title="切换图表与表格" style="vertical-align: middle;" onClick="$.show_chart();"></span>&nbsp;
                                    <!--<span onClick="$.add_filter();"><img id="filter_add_img" src="public/img/filter_add_24x24.png" style="vertical-align: middle;display: none;"></span>&nbsp;-->
                                    <a href="index.php?status=in_process"><button id="in_process" class="minimal font-face-display" title="还未完成的主题\功能">进行中</button></a>&nbsp;
                                    <a href="index.php?status=all"><button id="all" class="minimal font-face-display" title="显示所有主题功能,包括己完成\己删除">所有</button></a>
                                    <a href="index.php?status=finish"><button id="finish" class="minimal font-face-display" title="显示己完成">己完成</button></a>&nbsp;
                                    <a href="index.php?status=deleted"><button id="deleted" class="minimal font-face-display" title="显示己删除">己删除</button></a>&nbsp;
                                    <a href="index.php"><button id="refresh" class="cupid-green font-face-display" title="重新载入index页面">刷新</button></a>&nbsp;
                                </div>
                            </div>
                            <div class="export" onClick="$.export();">
                                <button class="minimal font-face-display" title="将当前显示的表格导出为Excel文档">导出 Excel</button>
                            </div>
<?PHP
        if($allow->pass()):
?>
                            <div class="model_text" id="model_text" onClick="$.model_switch();">
                                <button class="minimal font-face-display" title="只读\编辑模式切换按钮">只读模式</button>
                            </div>
<?PHP
        endif;
?>
                        </div>
<?PHP
        if($_SESSION["admin"] == true && $allow->pass()):
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
        if($filter_submit) {
            echo "<tr><th class=\"chart_report\" colspan=\"".$col_num."\" style=\"background-color: khaki;
\">";
            $curent_where = "<div class=\"where\">过滤条件: ";
            foreach($_SESSION["filter_array"] as $key => $row){
                if(! empty($row) && strcmp($key,"stage_date_json") != 0){
                    $curent_where .= "<label class=\"where_key\">".$key."</label>=<label class=\"where_value\">".$row."</label> <label class=\"where_and\">AND</label> ";
                }

                if(strcmp($key,"stage_date_json") == 0){
                    $date_array = $stage_json->decode($row);

                    if(is_array($date_array)) {
                        foreach($config["STAGE"] as $item){
                            foreach($date_array[$item] as $k => $v){
                                if(! empty($v)){
                                    $curent_where .= "<label class=\"where_key\">".$item."[".$k."]</label>=<label class=\"where_value\">".$v."</label> <label class=\"where_and\">AND</label> ";
                                }
                            }
                        }
                    }
                }
            }
            $curent_where = rtrim($curent_where,"<label class=\"where_and\">AND</label> ");
            $curent_where .= "</div>";
            echo $curent_where;
            echo "</th></tr>";
        }
    endif;
    if(@$_SESSION["chart_bool"]):
?>
            </thead>
            <tbody>
                <tr>
                    <td id="chart_report" class="chart_report" colspan="<?PHP echo $col_num/2; ?>">
                        <div id="total_status_chart_pie" class="chart" style="width:385px;height:300px"></div>
                        <div id="total_stage_chart_pie" class="chart" style="width:385px;height:300px"></div>
                        <div id="total_status_chart" class="chart" style="width:385px;height:300px"></div>
                        <div id="total_stage_chart" class="chart" style="width:385px;height:300px"></div>
                    </td>
                </tr>
                <tr>
                    <td id="chart_report" class="chart_report" colspan="<?PHP echo $col_num/2; ?>">
                        <div id="project_status_chart" class="chart" style="width:385px;height:300px"></div>
                        <div id="project_stage_chart" class="chart" style="width:385px;height:300px"></div>
                    </td>
                </tr>
                <?PHP
                    $total_status_chart_array = array();
                    $total_stage_chart_array = array();
                    $total_num = 0;
                    $project_status_chart_array = array();
                    $project_stage_chart_array = array();

                    foreach($tbl_data as $row){
                        $total_num++;

                        // Chart Status Report
                        if(is_null($total_status_chart_array[$row["status"]])) {
                            $total_status_chart_array[$row["status"]] = 1;
                        } else {
                            $total_status_chart_array[$row["status"]] += 1;
                        }

                        // Chart Stage Report
                        if(is_null($total_stage_chart_array[$row["stage"]])) {
                            $total_stage_chart_array[$row["stage"]] = 1;
                        } else {
                            $total_stage_chart_array[$row["stage"]] += 1;
                        }

                        // Chart Project stage Report
                        if(is_null($project_stage_chart_array[$row["name"]][$row["stage"]])){
                            $project_stage_chart_array[$row["name"]][$row["stage"]] = 1;
                        } else {
                            $project_stage_chart_array[$row["name"]][$row["stage"]] += 1;
                        }

                        // Chart Project Status Report
                        if(is_null($project_status_chart_array[$row["name"]][$row["status"]])){
                            $project_status_chart_array[$row["name"]][$row["status"]] = 1;
                        } else {
                            $project_status_chart_array[$row["name"]][$row["status"]] += 1;
                        }
                    }

                    // Chart Status Pie Report
                    $total_status_chart_data_pie = "";
                    foreach($total_status_chart_array as $key => $num){
                        if($num != 0) {
                            $temp_num = $num/$total_num*100;
                        }
                        if(strcmp($key,"正常") == 0){
                            $total_status_chart_data_pie .= "{name: '".$key."', color: 'skyblue', y: ".number_format($temp_num,2).", sliced: true, selected: true},";
                        } else if(strcmp($key,"延迟") == 0){
                            $total_status_chart_data_pie .= "{name: '".$key."', color: 'Red', y: ".number_format($temp_num,2)."},";
                        } else if(strcmp($key,"提前") == 0){
                            $total_status_chart_data_pie .= "{name: '".$key."', color: 'MediumSeaGreen', y: ".number_format($temp_num,2)."},";
                        } else {
                            $total_status_chart_data_pie .= "{name: '".$key."', color: '', y: ".number_format($temp_num,2)."},";
                        }
                    }

                    // Chart Stage Pie Report
                    $total_stage_chart_data_pie = "";
                    foreach($total_stage_chart_array as $key => $num){
                        if($num != 0) {
                            $temp_num = $num/$total_num*100;
                        }
                        if(strcmp($key,"DEV") == 0){
                            $total_stage_chart_data_pie .= "{name: '".$key."', y: ".number_format($temp_num,2).", sliced: true, selected: true},";
                        } else {
                            $total_stage_chart_data_pie .= "['".$key."',".number_format($temp_num,2)."],";
                        }
                    }

                    $total_status_chart_data_pie = rtrim($total_status_chart_data_pie,",");

                    $total_status_chart_data = "";
                    foreach(array("skyblue" => "正常","MediumSeaGreen" => "提前","red" => "延迟") as $color => $status) {
                        $total_status_chart_categories .= "'".$status."',";
                        if(empty($total_status_chart_array[$status])){
                            $total_status_chart_data .= "'',";
                        } else {
                            $total_status_chart_data .= $total_status_chart_array[$status].",";
                        }
                    }

                    foreach($config["STAGE"] as $key){
                        $total_stage_chart_categories .= "'".$key."',";
                        if(empty($total_stage_chart_array[$key])){
                            $total_stage_chart_data .= "'',";
                        } else {
                            $total_stage_chart_data .= $total_stage_chart_array[$key].",";
                        }
                    }

                    foreach($project_stage_chart_array as $key => $status_array){
                        $project_chart_cagegories .= "'".$key."',";
                    }

                    $project_stage_chart_data = "";
                    foreach($config["STAGE"] as $stage) {
                        $project_stage_chart_data .= "{ name: '".$stage."',data: [";

                        foreach($project_stage_chart_array as $key => $stage_array){

                            if(is_null($stage_array[$stage])){
                                $project_stage_chart_data .= "0,";
                            } else {
                                $project_stage_chart_data .= $stage_array[$stage].",";
                            }
                        }
                        $project_stage_chart_data = rtrim($project_stage_chart_data,",");
                        $project_stage_chart_data .= "]},";
                    }

                    $project_status_chart_data = "";
                    foreach(array("skyblue" => "正常","MediumSeaGreen" => "提前","red" => "延迟") as $color => $status) {
                        $project_status_chart_data .= "{ name: '".$status."',color: '".$color."' ,data: [";

                        foreach($project_status_chart_array as $key => $status_array){

                            if(is_null($status_array[$status])){
                                $project_status_chart_data .= "0,";
                            } else {
                                $project_status_chart_data .= $status_array[$status].",";
                            }
                        }
                        $project_status_chart_data = rtrim($project_status_chart_data,",");
                        $project_status_chart_data .= "]},";
                    }
                ?>
            </tbody>
        </table>

        <script>
        $(function () {
            $('#total_status_chart_pie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: '<?PHP echo $chart_title; ?> 状态占比'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: '占比',
                    data: [
                    <?PHP echo $total_status_chart_data_pie; ?>
                    ]
                }]
            });
        });

        $(function () {
            $('#total_stage_chart_pie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: '<?PHP echo $chart_title; ?> 阶段占比'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: '占比',
                    data: [
                    <?PHP echo $total_stage_chart_data_pie; ?>
                    ]
                }]
            });
        });

        $(function () {
            $('#total_stage_chart').highcharts({
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            events: {
                                click: function(e) {
                                    location.href = "index.php?filter_stage=" + e.point.category;
                            }
                        },
                        }
                    },
                    chart: {
                        type: 'column',
                        margin: [ 50, 50, 100, 80]
                    },
                    title: {
                    text: '<?PHP echo $chart_title; ?> 阶段数量'
                    },
                    xAxis: {
                        categories: [
    <?PHP echo $total_stage_chart_categories; ?>
                        ],
                        labels: {
                            rotation: -45,
                            align: 'right',
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: '<b>{point.y}</b>',
                    },
                    series: [{
                        name: 'Population',
                            data: [
    <?PHP echo $total_stage_chart_data; ?>
                                ],
                        dataLabels: {
                            enabled: true,
                            rotation: -90,
                            color: '#FFFFFF',
                            align: 'right',
                            x: 4,
                            y: 10,
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif',
                                textShadow: '0 0 3px black'
                            }
                        }
                    }]
                });
            });

        $(function () {
            $('#total_status_chart').highcharts({
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            events: {
                                click: function(e) {
                                    location.href = "index.php?filter_status=" + e.point.category;
                            }
                        },
                        }
                    },
                    chart: {
                        type: 'column',
                        margin: [ 50, 50, 100, 80]
                    },
                    title: {
                        text: '<?PHP echo $chart_title; ?> 状态数量'
                    },
                    xAxis: {
                        categories: [
    <?PHP echo $total_status_chart_categories; ?>
                        ],
                        labels: {
                            rotation: -45,
                            align: 'right',
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: '<b>{point.y}</b>',
                    },
                    series: [{
                        name: 'Population',
                            data: [
    <?PHP echo $total_status_chart_data; ?>
                                ],
                        dataLabels: {
                            enabled: true,
                            rotation: -90,
                            color: '#FFFFFF',
                            align: 'right',
                            x: 4,
                            y: 10,
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif',
                                textShadow: '0 0 3px black'
                            }
                        }
                    }]
                });
            });

            $(function () {
                $('#project_stage_chart').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '<?PHP echo $chart_title; ?> 项目阶段百分比堆栈图'
                    },
                    xAxis: {
                    categories: [
<?PHP echo $project_chart_cagegories; ?>
                    ]
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                        shared: true
                    },
                    plotOptions: {
                        column: {
                            stacking: 'percent'
                        }
                    },
                        series: [
<?PHP echo $project_stage_chart_data; ?>
                    ]
                });
            });

            $(function () {
                $('#project_status_chart').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '<?PHP echo $chart_title; ?> 项目状态百分比堆栈图'
                    },
                    xAxis: {
                    categories: [
<?PHP echo $project_chart_cagegories; ?>
                    ]
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                        shared: true
                    },
                    plotOptions: {
                        column: {
                            stacking: 'percent'
                        }
                    },
                        series: [
<?PHP echo $project_status_chart_data; ?>
                    ]
                });
            });
        </script>
<?PHP
    else:
?>
                <tr>
                    <th class="name header font-table-header" rowspan="2">项目</th>
                    <th class="theme_function header font-table-header" rowspan="2">主题/功能</th>
                    <th class="version header font-table-heaser" rowspan="2">版本</th>
                    <th class="status header font-table-header" rowspan="2">状态</th>
                    <th class="stage header font-table-header" rowspan="2">阶段</th>
                    <?PHP
                        foreach($config["STAGE"] as $stage){
                            echo "<th class=\"stage_title font-table-header\" colspan=\"2\">".$stage."</th>";
                        }
                    ?>
                    <th class="note header font-table-header" rowspan="2">备注</th>
                </tr>
                <tr>
                <?PHP
                    for($i=0; $i < count($config["STAGE"]); $i++){
                        echo "<th class=\"stage_date header font-table-header\">计划</th>";
                        echo "<th class=\"stage_date header font-table-header\">实际</th>";
                    }
                ?>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $total_status_chart_array = array();
                    foreach($tbl_data as $row){
                        if(is_null($total_status_chart_array[$row["status"]])) {
                            $total_status_chart_array[$row["status"]] = 1;
                        } else {
                            $total_status_chart_array[$row["status"]] += 1;
                        }

                        if(strcmp($row["deleted"],0) != 0) {
                            $tag = "<td class=\"%s font-face-display\" title=\"%s\"><div><s><del>%s</del></s></div></td>";
                        } else if(strcmp($row["finish"],0) != 0) {
                            $tag = "<td class=\"%s font-face-display\" title=\"%s\"><div><i><u>%s</u></i></div></td>";
                        }else {
                            $tag = "<td class=\"%s font-face-display\" title=\"%s\"><div>%s</div></td>";
                        }

                        echo "<tr id=\"".$row["id"]."\">";
                        printf($tag,"name",$row["name"],$row["name"]);
                        printf($tag,"theme_function",$row["theme_function"],$row["theme_function"]);
                        printf($tag,"version",$row["version"],$row["version"]);
                        printf($tag,"status",$row["status"],$row["status"]);
                        printf($tag,"stage",$row["stage"],$row["stage"]);

                        $stage_data = $stage_json->decode($row['stage_date_json']);
                        $stage_tag = "<td class=\"stage_date font-face-display\" style=\"background:%s\"><div>%s</div></td>";
                        foreach($config["STAGE"] as $stage){
                            printf($stage_tag,$stage_data[$stage]["PlanColor"],$stage_data[$stage]["PlanDate"]);
                            printf($stage_tag,$stage_data[$stage]["RealColor"],$stage_data[$stage]["RealDate"]);
                        }
                        printf($tag,"note",$row["note"],$row["note"]);
                    echo "</tr>";
                    }

                    foreach($total_status_chart_array as $key => $num){
                        $total_status_chart_categories .= "'".$key."',";
                        $total_status_chart_data .= $num.",";
                    }
                ?>
            </tbody>
        </table>
<?PHP
    endif;
?>
    </BODY>
</HTML>

<?PHP
    $mysql->close_db();
?>
