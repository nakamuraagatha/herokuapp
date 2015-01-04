<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CategoryController
{

    private $categories_model;
    private $app_name = "QuotesApp";

    function __construct()
    {
        $this->categories_model = new CategoryModel();
    }

    public function indexAction(Application $app)
    {
        return $app['twig']->render('quotes.twig', array());
    }

    public function createAction(Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }

        $category = json_decode($request->getContent(), true);

        $category = $this->categories_model->create_category($category);
        return $app->json($category, 201);
    }

    public function readAction(Application $app, Request $request)
    {

        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "read")) {
            return $app->json("You are not authorized!", 400);
        }

        $categories = $this->categories_model->get_categories();
        return $app->json($categories, 200);
    }

    public function updateAction($id, Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }

        $category = json_decode($request->getContent(), true);

        $category = $this->categories_model->update_category($category, $id);

        return $app->json($category, 200);
    }

    public function deleteAction($id, Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }

        $category = $this->categories_model->delete_category($id);

        return $app->json("Successsfully Deleted!", 200);
    }

}
