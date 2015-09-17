<?PHP
#ifundef PROJECT_STATUS
    die("access deny.");
#else
session_start();

    $enable = @trim($_REQUEST["enable"]);

    $_SESSION["model_edit"] = $enable;
#endif
?>
