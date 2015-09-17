<?PHP
#define PROJECT_STATUS

    include "lib.php";
    $tbl_data = $mysql->get_in_process();
    $allow->pass(); //通过IP检查
?>
<!DOCTYPE HTML>
    <HEAD>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <meta http-equiv="pragma" content="no-cache" />
       <meta http-equiv="cache-control" content="no-cache" />
       <TITLE> <?PHP echo $config["SITE_NAME"] ?></TITLE>
       <link type="text/css" rel="stylesheet" href="public/css/theme.default.css" />
       <!--<link type="text/css" rel="stylesheet" href="public/css/blue/style.css" />-->
       <link type="text/css" rel="stylesheet" href="public/css/common.css" />
       <link href="public/css/wdDatePicker/page.css" rel="stylesheet" type="text/css" />
       <link href="public/css/wdDatePicker/dp.css" rel="stylesheet" type="text/css" />

       <!--<script type="text/javascript" src="public/js/jquery-1.2.6.min.js"></script>-->
       <script type="text/javascript" src="public/js/jquery-2.1.4.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.tablesorter.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.tablesorter.widgets.min.js"></script>
       <script type="text/javascript" src="public/js/jquery.metadata.js"></script>
       <script type="text/javascript" src="public/js/jquery.datepicker.min.js"></script>
       <script>

       $(document).ready(function(){
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

               $('.show_opt').remove();
               row_edit_bool = false;
           }});

           $.extend({finish:function(fid){
             if(confirm("是否己完成") == true) {
               $.get("admin.php?finish="+fid,function(date,status){
                    if(status) window.location.reload();
               });
             }
             $.edit_cancel();
           }});

           $.extend({delete:function(did){
             if(confirm("是否删除") == true) {
               $.get("admin.php?deleted="+did,function(date,status){
                    if(status) window.location.reload();
               });
              }
              $.edit_cancel();
           }});

           var row_edit_bool = false;
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

            $("html,table.tablesorter th").dblclick(function(){
               if(row_edit_bool){
                   row_edit_bool = false;
               } else {
                   $.get("admin.php",function(data,status){
                       $("table.tablesorter").append("<td class=\"show_opt\" colspan=\"14\">"+data+"</td>");
                   });
                   row_edit_bool = true;
               }
               $('.show_opt').remove();
            });

           $('table.tablesorter td').dblclick(function(){
               var edit_row = $(this).parent();
               var show_opt = "<tr class=\"show_opt\"><td style=\"text-align:center\" colspan=\"14\">[<a href=\"admin.php?id="+edit_row.attr("id")+"\">修改</a>] [删除] [己完成]<td></tr>";

               if(row_edit_bool){
                   $('.show_opt').parent().find("td").each(function(){
                       $(this).show();
                   });

                   row_edit_bool = false;
               } else {
                   edit_row.find("td").each(function(){
                       $(this).hide();
                   });

                   $.get("admin.php?id="+edit_row.attr("id"),function(data,status){
                       edit_row.append("<td class=\"show_opt\" colspan=\"14\">"+data+"</td>");
                   });
                   row_edit_bool = true;
               }

               $('.show_opt').remove();
           });

       });
       </script>
    </HEAD>
    <BODY>
        <table id="project_status_list" class="tablesorter">
            <thead>
                <tr>
                <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>" style="font-size:16px;text-align:center">项目状态</th>
                </tr>
                <tr>
                    <th colspan="<?PHP echo 6 + count($config["STAGE"]) * 2?>">
                        <div class="filter" style="width:100%;height:100%;">
                                Filter
                        </div>
                    </th>
                </tr>
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
                        echo "<tr id=\"".$row["id"]."\">";
                        echo "<td>".$row["name"]."</td>";
                        echo "<td>".$row['theme_function']."</td>";
                        echo "<td>".$row['version']."</td>";
                        echo "<td>".$row['status']."</td>";
                        echo "<td>".$row['stage']."</td>";

                        $stage_data = $stage_json->decode($row['stage_date_json']);
                        foreach($config["STAGE"] as $stage){
                            echo "<td style=\"background:".$stage_data[$stage]["PlanColor"]."\">".$stage_data[$stage]["PlanDate"]."</td>";
                            echo "<td style=\"background:".$stage_data[$stage]["RealColor"]."\">".$stage_data[$stage]["RealDate"]."</td>";
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
