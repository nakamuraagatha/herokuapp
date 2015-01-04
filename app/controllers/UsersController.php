<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UsersController
{

    private $user_model;
    private $app_list_model;
    private $app_permissions_model;

    function __construct()
    {
        $this->user_model = new UserModel();
        $this->app_list_model = new AppListModel();
        $this->app_permissions_model = new AppPermissionsModel();
    }



    public function indexAction(Application $app)
    {
        return $app['twig']->render('users.twig', array());
    }

    public function users_listAction(Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $users = $this->user_model->get_users();

        return $app->json($users, 200);
    }

    public function app_listAction(Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $appList = $this->app_list_model->getAppList();

        return $app->json($appList, 200);
    }

    public function get_permissionsAction($appName, $id, Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $row = $this->app_permissions_model->get_permission_for_app($id, $appName);

        if (isset($row["permissions"])) {
            return $app->json($row["permissions"], 200);
        } else {
            $row = $this->app_list_model->get_app($appName);
            return $app->json(array($row["defaultAppAccess"], $row["defaultWriteAccess"]), 200);
        }
    }

    public function set_permissionsAction($appName, $id, Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $request_data = json_decode($request->getContent(), true);
        $permissions_request = $request_data[0] == false ? array(false, false) : $request_data;

        $row = $this->app_permissions_model->get_permission_for_app($id, $appName);

        if (NULL == $row) {
            $permissions_request = $this->app_permissions_model->add_permission($id, $appName, $permissions_request);
            return $app->json($permissions_request, 201);
        } else {
            $permissions_request = $this->app_permissions_model->update_permission($id, $appName, $permissions_request);
            return $app->json($permissions_request, 200);
        }
    }

}
