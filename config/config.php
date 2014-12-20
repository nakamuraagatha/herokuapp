<?php

$mode_development = TRUE;

//$mode_production = TRUE;

function dev_config($key) {

    $configs = array(
        'TIMES' => 70,
        'MONGO_URI' => "mongodb://localhost:27017/heroku",
        'MONGO_DB' => "heroku"
    );

    return $configs[$key];
}
