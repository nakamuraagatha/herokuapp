<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AppController {

    private $collection_apps = "apps";
    private $apps;
    private $collection_app_permissions = "app_permissions";
    private $app_permissions;

    function __construct() {
        $uri = getenv('MONGO_URI') ? getenv('MONGO_URI') : local_configs('MONGO_URI');
        $db = getenv('MONGO_DB') ? getenv('MONGO_DB') : local_configs('MONGO_DB');
        $options = array("connectTimeoutMS" => 30000);
        $connection = new MongoClient($uri, $options);
        $database = $connection->selectDB($db);
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection_apps)) === null) {
            $this->apps = $database->createCollection($this->collection_apps);
        } else {
            $this->apps = $database->selectCollection($this->collection_apps);
        }
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection_app_permissions)) === null) {
            $this->app_permissions = $database->createCollection($this->collection_app_permissions);
        } else {
            $this->app_permissions = $database->selectCollection($this->collection_app_permissions);
        }
    }

    public function app_listAction(Application $app) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->redirect('/');
        }
        $appList = iterator_to_array($this->apps->find());
        return $app['twig']->render('app-list.twig', array('user' => $user,
                    'apps' => $appList));
    }

    public function app_createAction(Application $app, Request $request) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->json('Error!', 400);
        }
        $app_data = json_decode($request->getContent(), true);
        $this->apps->insert($app_data);
        return $app->json($app_data, 200);
    }

    public function app_deleteAction(Application $app, Request $request) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->json('Error!', 400);
        }
        $delete_data = json_decode($request->getContent(), true);
        $result = $this->apps->remove(array("_id" => new MongoId($delete_data['_id'])));
        $this->app_permissions->remove(array("app_name" => $delete_data['name']));
        if ($result['n'] == 1) {
            return $app->json('Delete success!', 200);
        } else {
            return $app->json('Error!', 400);
        }
    }

}
