<?php
    include "../classes/User.php";

    $user = new User;

    $user->update($_POST,$_FILES);
//    $_POSTの中にeditの記入欄が含まれてる
// 　 ＄
?>