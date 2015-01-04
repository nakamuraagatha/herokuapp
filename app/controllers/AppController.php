<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AppController
{
    private $app_list_object_model;
    private $app_permissions_model;

    function __construct()
    {
        $this->app_list_object_model = new AppListModel();
        $this->app_permissions_model = new AppPermissionsModel();
    }

    public function indexAction(Application $app)
    {
        return $app['twig']->render('app-list.twig', array());
    }

    public function app_listAction(Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $appList = $this->app_list_object_model->getAppList();

        return $app->json($appList, 200);
    }

    public function app_createAction(Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $app_data = json_decode($request->getContent(), true);

        $inserted_data = $this->app_list_object_model->createApp($app_data);

        return $app->json($inserted_data, 200);
    }

    public function app_deleteAction(Application $app, Request $request)
    {
        if (!is_super_user($request, $app)) {
            return $app->json("Unauthorized access!", 400);
        }

        $delete_data = json_decode($request->getContent(), true);

        $result = $this->app_list_object_model->removeApp($delete_data['_id']);
        $this->app_permissions_model->remove_app_from_collection($delete_data['name']);


        if ($result['n'] == 1) {
            return $app->json('Delete success!', 200);
        } else {
            return $app->json('Error!', 400);
        }
    }

}
