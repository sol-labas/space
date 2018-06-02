<?php

function login($row, $rememberMe)
{
    $_SESSION["login"] = $row;
    if($rememberMe)
    {
        setcookie("Autologin", "1", time() + 86400, "/");
    }
}

function isLogin()
{
    return !empty($_SESSION["login"]);
}

function logout() 
{
    unset($_SESSION["login"]);
    setcookie("Autologin", "", time() + 86400, "/");
}