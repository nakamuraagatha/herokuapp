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
$url_authorize = function (Request $request, Application $app) {
    $user = $app['session']->get('user');
    if (NULL == $user) {
        return $app->redirect('/login');
    }
};
// Routes
$app->get('/', controller('auth/index'));
$app->get('/login', controller('auth/login'));
$app->get('/logout', controller('auth/logout'));
$app->get('/auth/{provider}', controller('auth/auth'));
$app->get('/userDetails/{appName}', controller('auth/user_details'));

// Apps
$app->get('/appList', controller('app/app_list'));
$app->post('/appList', controller('app/app_create'));
$app->delete('/appList', controller('app/app_delete'));

// Users
$app->get('/users', controller('users/index'));
$app->get('api/usersList', controller('users/users_list'))->before($authorize);
$app->get('api/appList', controller('users/app_list'))->before($authorize);
$app->get('api/appPermissions/{appName}/{email}', controller('users/get_permissions'))->before($authorize);
$app->post('api/appPermissions/{appName}/{email}', controller('users/set_permissions'))->before($authorize);

// Quotes
$app->get('/quotes-app', controller('category/index'))->before($url_authorize);
$app->get('api/category', controller('category/read'))->before($authorize);
$app->post('api/category', controller('category/create'))->before($authorize);
$app->put('api/category/{id}', controller('category/update'))->before($authorize);
$app->delete('api/category/{id}', controller('category/delete'))->before($authorize);
$app->get('api/quote/{ctg}', controller('quote/read'))->before($authorize);
$app->post('api/quote', controller('quote/create'))->before($authorize);
$app->put('api/quote/{id}', controller('quote/update'))->before($authorize);
$app->delete('api/quote/{id}', controller('quote/delete'))->before($authorize);


$app->error(function (\Exception $e, $code) use($app) {
    switch ($code) {
        case 404:
            $message = $app['twig']->render('error404.twig');
            break;
        default:
            $message = $app['twig']->render('error500.twig');
    }
    return new Response($message, $code);
});
$app->run();
/*
    $start = new MongoDate(strtotime("2010-01-15 00:00:00"));
*/