<?php

$mode_development = TRUE;

//$mode_production = TRUE;

function dev_config($key) {

    $configs = array(
        'TIMES' => 70
    );

    return $configs[$key];
}
