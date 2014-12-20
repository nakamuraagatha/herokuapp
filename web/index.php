<?php

require('../vendor/autoload.php');
require('../config/config.php');
require_once( __DIR__ . "/../hybridauth/Hybrid/Auth.php" );
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
    return 'Hello World!';
});

$app->get('/facebook', function () use ($app) {

    $config_file_path = __DIR__ . '/../hybridauth/config.php';
    $hybridauth = new Hybrid_Auth($config_file_path);
    $adapter = $hybridauth->authenticate("Facebook");
    $user_profile = $adapter->getUserProfile();
    $access_token_array = $adapter->getAccessToken();
    echo $access_token_array['access_token'];
    print_details($user_profile);
    return 'Facebook!!!';
});

$app->get('/google', function () use ($app) {

    $config_file_path = __DIR__ . '/../hybridauth/config.php';
    $hybridauth = new Hybrid_Auth($config_file_path);
    $adapter = $hybridauth->authenticate("Google");
    $user_profile = $adapter->getUserProfile();
    $adapter->getAccessToken()['access_token'];
    print_details($user_profile);
    return 'Google!!!';
});

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
                'name' => $name,
    ));
});
$app->get('/mongo', function () use ($app) {

    try {
        $uri = getenv('MONGO_URI') ? getenv('MONGO_URI') : dev_config('MONGO_URI');
        $db = getenv('MONGO_DB') ? getenv('MONGO_DB') : dev_config('MONGO_DB');
        $col = 'posts';
        $options = array("connectTimeoutMS" => 30000);
        $connection = new MongoClient($uri, $options);
        $database = $connection->selectDB($db);
        $posts = $database->selectCollection($col);

        //CREATE
        $post = array(
            'title' => 'What is MongoDB',
            'content' => 'MongoDB is a document database that provides high performance...',
            'saved_at' => new MongoDate()
        );
        $posts->insert($post);


        $cursor = $posts->find()->limit(1);
        $row = $cursor->getNext();
        echo $row['_id'];
        echo $row['title'];
        echo $row['content'];
        echo date('Y-m-d H:i:s', $row['saved_at']->sec);
        foreach ($cursor as $document) {
            echo '<pre>' . json_encode($document) . '</pre>';
            echo "\n";
        }

        //UPDATE
        $posts->update(
                array("_id" => $row['_id']), array('$set' => array("title" => "A little update"))
        );
        $cursor1 = $posts->find();
        foreach ($cursor1 as $document) {
            echo '<pre>' . json_encode($document) . '</pre>';
            echo "\n";
        }

        //DELETE
        $posts->remove(array("_id" => $row['_id']));
    } catch (MongoConnectionException $e) {
        die('Error connecting to MongoDB server' . $e->getMessage());
    } catch (MongoException $e) {
        die('Mongo Error: ' . $e->getMessage());
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }

    return 'Mongo';

    /*
      $start = new MongoDate(strtotime("2010-01-15 00:00:00"));
     */
});

$app->run();

function print_details($user_profile) {
    echo "Profile URL: " . $user_profile->profileURL . "\n";
    echo "Display Name: " . $user_profile->displayName . "\n";
    echo "First Name: " . $user_profile->firstName . "\n";
    echo "Last Name: " . $user_profile->lastName . "\n";
    echo "Email: " . $user_profile->email . "\n";
}
