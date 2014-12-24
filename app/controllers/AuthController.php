<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AuthController {

    private $collection_users = "users";
    private $collection_apps = "apps";
    private $collection_app_permissions = "app_permissions";
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

    public function indexAction(Application $app) {
        $app['monolog']->addDebug('logging output.');
        $user = $app['session']->get('user');
        $permissions = $this->getpermissions($user);
        $app['session']->set('permissions', $permissions);
        if (NULL == $user) {
            return $app->redirect('/login');
        }
        $appList = iterator_to_array($this->apps->find());
        $app_list_filtered = array();
        foreach ($appList as $key => $val) {
            if ($permissions[$key][$val["name"]][0] == true) {
                array_push($app_list_filtered, $val);
            }
        }
        return $app['twig']->render('home.twig', array('user' => $user,
                    'apps' => $app_list_filtered));
    }

    function getUserDetail($adapter) {
        $access_token_array = $adapter->getAccessToken();
        $user_profile = $adapter->getUserProfile();
        $user = array();
        $user['access_token'] = $access_token_array['access_token'];
        $user['email'] = $user_profile->email;
        $user['profileURL'] = $user_profile->profileURL;
        $user['displayName'] = $user_profile->displayName;
        $user['firstName'] = $user_profile->firstName;
        $user['lastName'] = $user_profile->lastName;
        return $user;
    }

    function getpermissions($user) {
        $cursor = $this->app_permissions->find(array("user_email" => $user["email"]));
        $permissions_set = array();
        foreach ($cursor as $document) {
            $permissions_set[$document["app_name"]] = $document["permissions"];
        }
        $app_list = iterator_to_array($this->apps->find());
        $map_func = function($app_row) use ($permissions_set) {
            if (isset($permissions_set[$app_row['name']])) {
                return array($app_row['name'] => $permissions_set[$app_row['name']]);
            } else {
                return array($app_row['name'] => array($app_row['defaultAppAccess'], $app_row['defaultWriteAccess']));
            }
        };
        $permissions = array_map($map_func, $app_list);
        return $permissions;
    }

    public function authAction($provider, Application $app) {

        if (in_array(ucfirst($provider), array("Facebook", "Google"))) {
            if (!local_configs('MODE_PROD')) {
                $user = array('access_token' => 'DEccdXX223', 'displayName' => 'Klus Klax Klan',
                    'email' => 'abcd@xyz.com');
            } else {
                $hybridauth = new Hybrid_Auth(auth_configs());
                $adapter = $hybridauth->authenticate(ucfirst($provider));
                $user = $this->getUserDetail($adapter);
            }
            $cursor = $this->users->find(array("email" => $user['email']));
            $row = $cursor->getNext();
            if (empty($row)) {
                $this->users->insert($user);
            }
            $app['session']->set('user', $user);
            return $app->redirect('/');
        }
        return $app->redirect('/login');
    }

    public function loginAction(Application $app) {
        return $app['twig']->render('login.twig', array());
    }

    public function logoutAction(Application $app) {
        $app['session']->clear();
        return $app->redirect('/login');
    }

    public function user_detailsAction($appName, Application $app) {
        $user = $app['session']->get('user');
        $app['session']->set('permissions', $this->getpermissions($user));
        if (NULL == $user) {
            $app->abort(401, "User not logged in.");
        } else {
            $perm_arr = array(false, false);
            $permitted = $app['session']->get('permissions');
            foreach ($permitted as $key => $val) {
                if (isset($val[$appName])) {
                    $perm_arr = $val[$appName];
                }
            }
            return $app->json(array('displayName' => $user['displayName']
                        , 'api_key' => $user['access_token']
                        , 'permissions' => $perm_arr), 200);
        }
    }

}
