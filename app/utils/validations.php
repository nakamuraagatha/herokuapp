<?php

function is_api_authorized($permitted, $appName, $type) {
    $operations = array("read" => 0, "write" => 1);
    $index = $operations[$type];
    foreach ($permitted as $key => $val) {
        if (isset($val[$appName])) {
            return $val[$appName][$index] == TRUE;
        }
    }
}
