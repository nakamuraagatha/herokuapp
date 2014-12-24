<?php

require('../vendor/autoload.php');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class QuoteController {

    private $collection = "quotes";
    private $quotes;
    private $app_name = "QuotesApp";

    function __construct() {
        $uri = getenv('MONGO_URI') ? getenv('MONGO_URI') : local_configs('MONGO_URI');
        $db = getenv('MONGO_DB') ? getenv('MONGO_DB') : local_configs('MONGO_DB');
        $options = array("connectTimeoutMS" => 30000);
        $connection = new MongoClient($uri, $options);
        $database = $connection->selectDB($db);
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $this->collection)) === null) {
            $this->quotes = $database->createCollection($this->collection);
        } else {
            $this->quotes = $database->selectCollection($this->collection);
        }
    }

    public function createAction(Application $app, Request $request) {
        $permitted = $app['session']->get('permissions');
        if (!is_api_authorized($permitted, $this->app_name, "write")) {
            return $app->json("You are not authorized!", 400);
        }
        try {
            $quote = json_decode($request->getContent(), true);
            $quote['saved_at'] = new MongoDate();
            $this->quotes->insert($quote);
            return $app->json($quote, 201);
        } catch (MongoConnectionException $e) {
            return $app->json('Error connecting to MongoDB server' . $e->getMessage(), 400);
        } catch (MongoException $e) {
            return $app->json('Mongo Error: ' . $e->getMessage(), 400);
        } catch (Exception $e) {
            return $app->json('Error: ' . $e->getMessage(), 400);
        }
    }

    public function readAction($ctg, Application $app) {
        $permitted = $app['session']->get('permissions');
        if (!is_api_authorized($permitted, $this->app_name, "read")) {
            return $app->json("You are not authorized!", 400);
        }
        try {
            $cursor = $this->quotes->find(array("ctg_id" => $ctg));
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
            $quote = json_decode($request->getContent(), true);
            $quote['saved_at'] = new MongoDate();
            $this->quotes->update(
                    array("_id" => new MongoId($id)), array('$set' => $quote)
            );
            return $app->json($quote, 201);
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
            $this->quotes->remove(array("_id" => new MongoId($id)));
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
