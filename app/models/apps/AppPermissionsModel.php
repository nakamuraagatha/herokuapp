<?php

class AppPermissionsModel
{

    private $app_permissions;
    private $apps;

    function __construct()
    {
        $db_instance = new DBHelper();
        $this->app_permissions = $db_instance->app_permissions;
        $this->apps = $db_instance->apps;
    }

    public function remove_app_from_collection($app_name)
    {
        $this->app_permissions->remove(array("app_name" => $app_name));
    }

    public function get_permissions_to_user($user_id)
    {
        $cursor = $this->app_permissions->find(array("user_id" => $user_id));
        $permissions_set = array();
        foreach ($cursor as $document) {
            $permissions_set[$document["app_name"]] = $document["permissions"];
        }
        $app_list = iterator_to_array($this->apps->find());
        $map_func = function ($app_row) use ($permissions_set) {
            if (isset($permissions_set[$app_row['name']])) {
                return array($app_row['name'] => $permissions_set[$app_row['name']]);
            } else {
                return array($app_row['name'] => array($app_row['defaultAppAccess'], $app_row['defaultWriteAccess']));
            }
        };
        $permissions = array_map($map_func, $app_list);
        return $permissions;
    }

    public function get_permission_for_app($id, $appName)
    {
        $cursor = $this->app_permissions->find(array("user_id" => new MongoId($id), "app_name" => $appName))->limit(1);
        return $cursor->getNext();
    }

    public function add_permission($id, $appName, $permissions_request)
    {
        $this->app_permissions->insert(
            array("user_id" => new MongoId($id), "app_name" => $appName, "permissions" => $permissions_request));
        return $permissions_request;
    }

    public function update_permission($id, $appName, $permissions_request)
    {
        $search = array("user_id" => new MongoId($id), "app_name" => $appName);
        $new_update = array("permissions" => $permissions_request);
        $this->app_permissions->update($search, array('$set' => $new_update));
        return $permissions_request;
    }
}