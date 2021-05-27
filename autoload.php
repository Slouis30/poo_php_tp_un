<?php
    function autoload($classname){
        require($classname . ".php");
    }
    spl_autoload_register("autoload");
    session_start();