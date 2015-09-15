<?PHP
    include "lib.php";
    $tbl_data = $mysql->get_all();

    print_r($_REQUEST);
    if($_REQUEST){
        echo "YES";
    } else {
        echo "NO";
    }
?>

<!DOCTYPE HTML>
    <HEAD>
       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
       <meta http-equiv="pragma" content="no-cache" />
       <meta http-equiv="cache-control" content="no-cache" />
       <TITLE> <?PHP echo $config["SITE_NAME"] ?></TITLE>
       <link type="text/css" rel="stylesheet" href="/public/css/all.css" />
    </HEAD>
    <BODY>
        <form action="add.php" method="post">
            <table>
                <tr>
                    <td>项目:</td>
                    <td><INPUT type="text" name="name" /></td>
                </tr>
                <tr>
                    <td>主题/功能:</td>
                    <td><INPUT type="text" name="theme_function" /></td>
                </tr>
                <tr>
                    <td>版本:</td>
                    <td><INPUT type="text" name="version" /></td>
                </tr>
                <tr>
                    <td>状态:</td>
                    <td><INPUT type="text" name="status" /></td>
                </tr>
                <tr>
                    <td>价段:</td>
                    <td><INPUT type="text" name="stage" /></td>
                </tr>
                <tr>
                    <td>状态:</td>
                    <td><INPUT type="text" name="stage_date_json" /></td>
                </tr>
                <tr>
                    <td>备注:</td>
                    <td><INPUT type="text" name="note" /></td>
                </tr>
                <tr>
                    <td colspan="2"><INPUT type="SUBMIT" value="Add" /></td>
                </tr>
        </form>
    </BODY>
</HTML>
