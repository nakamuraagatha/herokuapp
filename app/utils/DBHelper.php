<?php

class DBHelper
{
    private $collection_users = "users";
    private $collection_apps = "apps";
    private $collection_app_permissions = "app_permissions";
    private $collection_ctgs = "ctgs";
    private $collection_quotes = "quotes";
    public $users;
    public $apps;
    public $app_permissions;
    public $ctgs;
    public $quotes;

    function __construct()
    {
        $uri = getenv('MONGO_URI') ? getenv('MONGO_URI') : local_configs('MONGO_URI');
        $db = getenv('MONGO_DB') ? getenv('MONGO_DB') : local_configs('MONGO_DB');
        $options = array("connectTimeoutMS" => 30000);
        $connection = new MongoClient($uri, $options);
        $database = $connection->selectDB($db);
        $this->users = $this->registerCollection($db, $database, $this->collection_users);
        $this->apps = $this->registerCollection($db, $database, $this->collection_apps);
        $this->app_permissions = $this->registerCollection($db, $database, $this->collection_app_permissions);
        $this->ctgs = $this->registerCollection($db, $database, $this->collection_ctgs);
        $this->quotes = $this->registerCollection($db, $database, $this->collection_quotes);
    }

    private function registerCollection($db, $database, $collection_name)
    {
        if ($database->system->namespaces->findOne(array('name' => $db . "." . $collection_name)) === null) {
            return $database->createCollection($collection_name);
        } else {
            return $database->selectCollection($collection_name);
        }
    }
}