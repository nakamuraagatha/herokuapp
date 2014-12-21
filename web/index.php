<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function controller($shortName) {
    list($shortClass, $shortMethod) = explode('/', $shortName, 2);
    return sprintf('%sController::%sAction', ucfirst($shortClass), $shortMethod);
}

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
$authorize = function (Request $request, Application $app) {
    $authKey = $request->headers->get('authorization');
    $user = $app['session']->get('user');
    if (!isset($user) || $user['access_token'] != $authKey) {
        return $app->json("Not authorized!", 401);
    }
};
// Routes
$app->get('/', controller('home/index'));
$app->get('/env', controller('home/env'));
$app->get('/login', controller('home/login'));
$app->get('/logout', controller('home/logout'));
$app->get('/auth/{provider}', controller('home/auth'));
$app->get('/userDetails', controller('home/userDetails'));

// Quotes
$app->get('api/category', controller('category/read'))->before($authorize);
$app->post('api/category', controller('category/create'))->before($authorize);
$app->put('api/category/{id}', controller('category/update'))->before($authorize);
$app->delete('api/category/{id}', controller('category/delete'))->before($authorize);
$app->get('api/quote/{ctg}', controller('quote/read'))->before($authorize);
$app->post('api/quote', controller('quote/create'))->before($authorize);
$app->put('api/quote/{id}', controller('quote/update'))->before($authorize);
$app->delete('api/quote/{id}', controller('quote/delete'))->before($authorize);


//$app->error(function (\Exception $e, $code) use($app) {
//    switch ($code) {
//        case 404:
//            $message = $app['twig']->render('error404.twig');
//            break;
//        default:
//            $message = $app['twig']->render('error500.twig');
//    }
//    return new Response($message, $code);
//});
$app->run();
/*
    $start = new MongoDate(strtotime("2010-01-15 00:00:00"));
*/