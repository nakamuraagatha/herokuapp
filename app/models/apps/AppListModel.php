<?php

class AppListModel
{
    private $apps;

    function __construct()
    {
        $db_instance = new DBHelper();
        $this->apps = $db_instance->apps;
    }

    public function getAppList()
    {
        return iterator_to_array($this->apps->find());
    }

    public function createApp($app_data)
    {
        $this->apps->insert($app_data);
        return $app_data;
    }

    public function removeApp($id)
    {
        return $this->apps->remove(array("_id" => new MongoId($id)));
    }

    public function get_app($appName)
    {
        $cursor = $this->apps->find(array("name" => $appName))->limit(1);
        return $cursor->getNext();
    }

}