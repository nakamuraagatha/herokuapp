<?php

require('../vendor/autoload.php');
require('../config/config.php');
if ($mode_development) {
//    require('../config/dev.php');
}

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));

// Register the Twig templating engine
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

// Our web handlers

$app->get('/', function() use($app) {
    $app['monolog']->addDebug('logging output.');
    return str_repeat('Hello', getenv('TIMES'));
//    return str_repeat('Hello', 5);
});

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
                'name' => $name,
    ));
});

$app->run();
?>
