<?php
require('../vendor/autoload.php');

function get_hash($input, $rounds = 7)
{
    $salt = "!29d3tgs'dle";
    return hash("sha512", $input . $salt);
}

function validate_hash($input, $hash)
{
    $salt = "!29d3tgs'dle";
    return hash("sha512", $input . $salt) == $hash;
}

function get_jwt($message)
{
    $payload = array(
        "message" => $message,
        "iat" => time(),
        "exp" => time() + (60 * 60 * 4)
    );
    return JWT::encode($payload, 'secret');
}

function decode_jwt($token, $app)
{
    try {
        return JWT::decode($token, 'secret');
    } catch (UnexpectedValueException $e) {
        $app['monolog']->addDebug('Caught exception: ' . $e->getMessage());
    } catch (DomainException $e) {
        $app['monolog']->addDebug('Caught exception: ' . $e->getMessage());
    } catch (Exception $e) {
        $app['monolog']->addDebug('Caught exception: ' . $e->getMessage());
    }
    return false;
}

function decode_jwt_from_request($request, $app)
{
    $authKey = $request->headers->get('authorization');
    return decode_jwt($authKey, $app);
}