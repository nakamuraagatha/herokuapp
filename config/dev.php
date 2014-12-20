<?php

function getenv($key) {

    $configs = array(
        'TIMES' => 20
    );

    return $configs[$key];
}
