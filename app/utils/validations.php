<?php

function is_api_authorized($permitted, $appName, $type)
{
    $operations = array("read" => 0, "write" => 1);
    $index = $operations[$type];
    foreach ($permitted as $key => $val) {
        $app_arr = (array)$val;
        if (isset($app_arr[$appName])) {
            return $app_arr[$appName][$index] == TRUE;
        }
    }
}

function is_super_user($request, $app)
{
    $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
    $decoded = decode_jwt_from_request($request, $app);
    $email = $decoded->message->email;
    return $superUser == $email;
}
