<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CategoryController {

    private $collection = "ctgs";
    private $ctgs;
    private $app_name = "QuotesApp";

    function __construct() {
        $uri = getenv('MONGO_URI') ? getenv('MONGO_URI') : local_configs('MONGO_URI');
        $db = getenv('MONGO_DB') ? getenv('MONGO_DB') : local_configs('MONGO_DB');
        $options = array("connectTimeoutMS" => 30000);
        $connection = new MongoClient($uri, $options);
        $database = $connection->selectDB($db);
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection)) === null) {
            $this->ctgs = $database->createCollection($this->collection);
        } else {
            $this->ctgs = $database->selectCollection($this->collection);
        }
    }

    public function indexAction(Application $app) {
        $user = $app['session']->get('user');
        $permitted = $app['session']->get('permissions');
        if (is_api_authorized($permitted, $this->app_name, "read")) {
            return $app['twig']->render('quotes.twig', array('user' => $user));
        } else {
            return $app->redirect("/");
        }
    }

    public function createAction(Application $app, Request $request) {
        $permitted = $app['session']->get('permissions');
        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }
        try {
            $category = json_decode($request->getContent(), true);
            $category['saved_at'] = new MongoDate();
            $this->ctgs->insert($category);
            return $app->json($category, 201);
        } catch (MongoConnectionException $e) {
            return $app->json('Error connecting to MongoDB server' . $e->getMessage(), 400);
        } catch (MongoException $e) {
            return $app->json('Mongo Error: ' . $e->getMessage(), 400);
        } catch (Exception $e) {
            return $app->json('Error: ' . $e->getMessage(), 400);
        }
    }

    public function readAction(Application $app) {
        $permitted = $app['session']->get('permissions');
        if (!is_api_authorized($permitted, $this->app_name, "read")) {
            return $app->json("You are not authorized!", 400);
        }
        try {
            $cursor = $this->ctgs->find();
            return $app->json(iterator_to_array($cursor), 200);
        } catch (MongoConnectionException $e) {
            return $app->json('Error connecting to MongoDB server' . $e->getMessage(), 400);
        } catch (MongoException $e) {
            return $app->json('Mongo Error: ' . $e->getMessage(), 400);
        } catch (Exception $e) {
            return $app->json('Error: ' . $e->getMessage(), 400);
        }
    }

    public function updateAction($id, Application $app, Request $request) {
        $permitted = $app['session']->get('permissions');
        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }
        try {
            $category = json_decode($request->getContent(), true);
            $category['saved_at'] = new MongoDate();
            $this->ctgs->update(
                    array("_id" => new MongoId($id)), array('$set' => $category)
            );
            return $app->json($category, 200);
        } catch (MongoConnectionException $e) {
            return $app->json('Error connecting to MongoDB server' . $e->getMessage(), 400);
        } catch (MongoException $e) {
            return $app->json('Mongo Error: ' . $e->getMessage(), 400);
        } catch (Exception $e) {
            return $app->json('Error: ' . $e->getMessage(), 400);
        }
    }

    public function deleteAction($id, Application $app) {
        $permitted = $app['session']->get('permissions');
        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }
        try {
            $this->ctgs->remove(array("_id" => new MongoId($id)));
            return $app->json("Successsfully Deleted!", 200);
        } catch (MongoConnectionException $e) {
            return $app->json('Error connecting to MongoDB server' . $e->getMessage(), 400);
        } catch (MongoException $e) {
            return $app->json('Mongo Error: ' . $e->getMessage(), 400);
        } catch (Exception $e) {
            return $app->json('Error: ' . $e->getMessage(), 400);
        }
    }

}
