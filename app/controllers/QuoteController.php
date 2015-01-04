<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class QuoteController
{

    private $quotes_model;
    private $app_name = "QuotesApp";

    function __construct()
    {
        $this->quotes_model = new QuotesModel();
    }

    public function createAction(Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }

        $quote = json_decode($request->getContent(), true);

        $quote = $this->quotes_model->create_quote($quote);

        return $app->json($quote, 201);
    }

    public function readAction($ctg, Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "read")) {
            return $app->json("You are not authorized!", 400);
        }

        $quotes = $this->quotes_model->get_quotes($ctg);

        return $app->json($quotes, 200);
    }

    public function updateAction($id, Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }

        $quote = json_decode($request->getContent(), true);

        $quote = $this->quotes_model->update_quote($quote, $id);

        return $app->json($quote, 200);
    }

    public function deleteAction($id, Application $app, Request $request)
    {
        $decoded = decode_jwt_from_request($request, $app);
        $permitted = $decoded->message->permissions;

        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }

        $result = $this->quotes_model->delete_quote($id);

        return $app->json("Successsfully Deleted!", 200);
    }

}
