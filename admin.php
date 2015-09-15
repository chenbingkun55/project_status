<?PHP
#ifundef PROJECT_STATUS
    //die("access deny.");
#else

    include "lib.php";

    $finish = trim($_REQUEST['finish']);
    $deleted = trim($_REQUEST['deleted']);
    $id = trim($_REQUEST['id']);

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
    }
?>

<!DOCTYPE HTML>
    <HEAD>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <meta http-equiv="pragma" content="no-cache" />
       <meta http-equiv="cache-control" content="no-cache" />
       <TITLE> <?PHP echo $config["SITE_NAME"] ?></TITLE>
       <!--<link type="text/css" rel="stylesheet" href="/public/css/theme.default.css" />-->
    </HEAD>
    <BODY>
        <form action="admin.php" method="post">
            <table>
                <tr>
                    <td>项目:</td>
                    <td><INPUT type="text" name="name" value="<?PHP echo $update["name"] ?>"/></td>
                </tr>
                <tr>
                    <td>主题/功能:</td>
                    <td><INPUT type="text" name="theme_function" value="<?PHP echo $update["theme_function"] ?>"/></td>
                </tr>
                <tr>
                    <td>版本:</td>
                    <td><INPUT type="text" name="version" value="<?PHP echo $update["version"] ?>"/></td>
                </tr>
                <tr>
                    <td>状态:</td>
                    <td>
                        <select name="status">
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
                </tr>
                <tr>
                    <td>价段:</td>
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
                </tr>
                <tr>
                    <td>备注:</td>
                    <td><INPUT type="text" name="note" value="<?PHP echo $update["note"]?>" /></td>
                </tr>
                <tr>
                    <INPUT type="hidden" name="id" value="<?PHP echo $update["id"]?>" />
                    <td colspan="2"><INPUT type="SUBMIT" value="Add" /></td>
                </tr>
        </form>
    </BODY>
</HTML>
<?PHP
#endif
?>
