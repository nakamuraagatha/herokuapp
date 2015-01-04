<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;
$app['asset_path'] = getenv('APP_URL') ? getenv('APP_URL') : local_configs('APP_URL');

//Providers
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../app/views',
));

$app->register(new Silex\Provider\SessionServiceProvider());
/*
$app->error(function (\Exception $e, $code) use ($app) {
    switch ($code) {
        case 404:
            $message = $app['twig']->render('error404.twig');
            break;
        default:
            $message = $app['twig']->render('error500.twig');
    }
    return new Response($message, $code);
});
*/
//Middleware
$authorize = function (Request $request, Application $app) {
    $authKey = $request->headers->get('authorization');
    $decoded = decode_jwt($authKey, $app);
    if (!$decoded) {
        return $app->json("Unauthorized access!", 401);
    }
};