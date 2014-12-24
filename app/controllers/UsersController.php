<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UsersController {

    private $collection_users = "users";
    private $collection_app_permissions = "app_permissions";
    private $collection_apps = "apps";
    private $users;
    private $apps;
    private $app_permissions;

    function __construct() {
        $uri = getenv('MONGO_URI') ? getenv('MONGO_URI') : local_configs('MONGO_URI');
        $db = getenv('MONGO_DB') ? getenv('MONGO_DB') : local_configs('MONGO_DB');
        $options = array("connectTimeoutMS" => 30000);
        $connection = new MongoClient($uri, $options);
        $database = $connection->selectDB($db);
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection_users)) === null) {
            $this->users = $database->createCollection($this->collection_users);
        } else {
            $this->users = $database->selectCollection($this->collection_users);
        }
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection_app_permissions)) === null) {
            $this->app_permissions = $database->createCollection($this->collection_app_permissions);
        } else {
            $this->app_permissions = $database->selectCollection($this->collection_app_permissions);
        }
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection_apps)) === null) {
            $this->apps = $database->createCollection($this->collection_apps);
        } else {
            $this->apps = $database->selectCollection($this->collection_apps);
        }
    }

    public function indexAction(Application $app) {
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        $user = $app['session']->get('user');
        if ($superUser != $user['email']) {
            return $app->redirect('/');
        }
        return $app['twig']->render('users.twig', array());
    }

    public function users_listAction(Application $app) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->json("Unauthorized access!", 400);
        }
        $users = $this->users->find();
        return $app->json(iterator_to_array($users), 200);
    }

    public function app_listAction(Application $app) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->json("Unauthorized access!", 400);
        }
        $appList = iterator_to_array($this->apps->find());
        return $app->json($appList, 200);
    }

    public function get_permissionsAction($appName, $email, Application $app) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->json("Unauthorized access!", 400);
        }
        $cursor = $this->app_permissions->find(array("user_email" => $email, "app_name" => $appName))->limit(1);
        $row = $cursor->getNext();
        if (isset($row["permissions"])) {
            return $app->json($row["permissions"], 200);
        } else {
            $cursor = $this->apps->find(array("name" => $appName))->limit(1);
            $row = $cursor->getNext();
            return $app->json(array($row["defaultAppAccess"], $row["defaultWriteAccess"]), 200);
        }
    }

    public function set_permissionsAction($appName, $email, Application $app, Request $request) {
        $user = $app['session']->get('user');
        $superUser = getenv('SUPER_EMAIL') ? getenv('SUPER_EMAIL') : local_configs('SUPER_EMAIL');
        if ($superUser != $user['email']) {
            return $app->json("Unauthorized access!", 400);
        }
        $request_data = json_decode($request->getContent(), true);
        $permissions_request = $request_data[0] == false ? array(false, false) : $request_data;
        $cursor = $this->app_permissions->find(array("user_email" => $email, "app_name" => $appName))->limit(1);
        $row = $cursor->getNext();
        if (NULL == $row) {
            $this->app_permissions->insert(
                    array("user_email" => $email, "app_name" => $appName, "permissions" => $permissions_request));
            return $app->json($permissions_request, 201);
        } else {
            $this->app_permissions->update(
                    array("user_email" => $email, "app_name" => $appName)
                    , array('$set' => array("permissions" => $permissions_request))
            );
            return $app->json($permissions_request, 200);
        }
    }

}
