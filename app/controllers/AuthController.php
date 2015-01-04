<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AuthController
{

    private $user_model;
    private $app_permissions_model;
    private $app_list_model;

    function __construct()
    {
        $this->user_model = new UserModel();
        $this->app_permissions_model = new AppPermissionsModel();
        $this->app_list_model = new AppListModel();
    }


    public function indexAction(Application $app)
    {
        $token = $app['session']->get('token');
        $token = NULL == $token ? "empty" : $token;
        $app['session']->clear();
        return $app['twig']->render('home.twig', array('token' => $token));
    }

    public function get_my_appsAction(Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $name = $decoded->message->displayName;
        $permissions = $decoded->message->permissions;

        $appList = $this->app_list_model->getAppList();
        $app_list_filtered = array();
        foreach ($appList as $key => $val) {
            $app_name = $val["name"];
            $app_perm_arr = $permissions->$key->$app_name;
            if ($app_perm_arr[0] == true) {
                array_push($app_list_filtered, $val);
            }
        }

        return $app->json(array("name" => $name, "appList" => $app_list_filtered), 200);
    }

    public function authAction($provider, Application $app)
    {

        if (in_array(ucfirst($provider), array("Facebook", "Google"))) {
            if (!local_configs('MODE_PROD')) {
                $user = array('access_token' => 'DEccdXX223', 'displayName' => 'Klus Klax Klan',
                    'email' => 'abc@xyz.com');
            } else {
                $hybridauth = new Hybrid_Auth(auth_configs());
                $adapter = $hybridauth->authenticate(ucfirst($provider));
                $user = $this->getUserDetail($adapter);
            }

            $message = array("email" => $user["email"], "displayName" => $user["displayName"]);
            $message["_id"] = $this->user_model->get_user_id($user['email'], $user['displayName']);
            $message["permissions"] = $this->app_permissions_model->get_permissions_to_user($message["_id"]);
            $token = get_jwt($message);
            $app['session']->set('token', $token);

            return $app->redirect('/');
        }
        return $app->redirect('/login');
    }

    function getUserDetail($adapter)
    {
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


    public function loginAction(Application $app)
    {
        $app['session']->clear();
        return $app['twig']->render('login.twig', array());
    }

    public function logoutAction(Application $app)
    {
        return $app->redirect('/login');
    }

    public function user_detailsAction($appName, Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $name = $decoded->message->displayName;
        $permitted = $decoded->message->permissions;

        $perm_arr = array(false, false);
        foreach ($permitted as $key => $val) {
            $app_arr = (array)$val;
            if (isset($app_arr[$appName])) {
                $perm_arr = $app_arr[$appName];
            }
        }
        return $app->json(array('displayName' => $name, 'permissions' => $perm_arr), 200);
    }

}
