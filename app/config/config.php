<?php

function local_configs($key) {

    $prod_mode = getenv('MODE_PROD') ? getenv('MODE_PROD') : FALSE;

    $configs = array(
        'MONGO_URI' => "mongodb://localhost:27017/heroku",
        'MONGO_DB' => "heroku",
        'MODE_PROD' => $prod_mode,
        'APP_URL' => "http://mystical.com/",
        'SUPER_EMAIL' => "abc@xyz.com"
    );

    return $configs[$key];
}

function auth_configs() {
    $fb_id = getenv('FB_ID');
    $fb_secret = getenv('FB_SECRET');
    $gplus_id = getenv('GPLUS_ID');
    $gplus_secret = getenv('GPLUS_SECRET');
    return array(
        "base_url" => "http://ajaymore.herokuapp.com/hybridauth.php",
        "providers" => array(
            "Google" => array(
                "enabled" => true,
                "keys" => array("id" => $gplus_id, "secret" => $gplus_secret),
            ),
            "Facebook" => array(
                "enabled" => true,
                "keys" => array("id" => $fb_id, "secret" => $fb_secret),
                "trustForwarded" => false
            ),
            "debug_mode" => false,
            "debug_file" => ""
    ));
}
