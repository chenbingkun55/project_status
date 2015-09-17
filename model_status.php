<?PHP
session_start();

    $enable = @trim($_REQUEST["enable"]);
    $_SESSION["model_edit"] = $enable;
?>
