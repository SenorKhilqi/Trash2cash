<?php
session_start();
session_destroy();
header("Location: /project_root/public/login.php");
exit;
