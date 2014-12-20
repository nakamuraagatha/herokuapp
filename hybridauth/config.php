<?php

/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */
// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return
        array(
            "base_url" => "http://ajaymore.herokuapp.com/hybridauth.php",
            "providers" => array(
                // openid providers
                "OpenID" => array(
                    "enabled" => true
                ),
                "Yahoo" => array(
                    "enabled" => true,
                    "keys" => array("key" => "", "secret" => ""),
                ),
                "AOL" => array(
                    "enabled" => true
                ),
                "Google" => array(
                    "enabled" => true,
                    "keys" => array(
                        "id" => "526115007499-jm0nrgu9j3g1mnv3r4jv3p9v2o2lm1c3.apps.googleusercontent.com",
                        "secret" => "N38f104bBGikj0Fa-tvxV0U-"),
                ),
                "Facebook" => array(
                    "enabled" => true,
                    "keys" => array("id" => "384541095042102", "secret" => "bef0d1d0fed3ba57cc58182e6d42f6ea"),
                    "trustForwarded" => false
                ),
                "Twitter" => array(
                    "enabled" => true,
                    "keys" => array("key" => "", "secret" => "")
                ),
                // windows live
                "Live" => array(
                    "enabled" => true,
                    "keys" => array("id" => "", "secret" => "")
                ),
                "LinkedIn" => array(
                    "enabled" => true,
                    "keys" => array("key" => "", "secret" => "")
                ),
                "Foursquare" => array(
                    "enabled" => true,
                    "keys" => array("id" => "", "secret" => "")
                ),
            ),
            // If you want to enable logging, set 'debug_mode' to true.
            // You can also set it to
            // - "error" To log only error messages. Useful in production
            // - "info" To log info and error messages (ignore debug messages) 
            "debug_mode" => false,
            // Path to file writable by the web server. Required if 'debug_mode' is not false
            "debug_file" => "",
);
