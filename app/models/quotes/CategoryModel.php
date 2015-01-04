<?php

/**
 * Created by PhpStorm.
 * User: Mystique
 * Date: 1/4/2015
 * Time: 3:28 PM
 */
class CategoryModel
{

    private $ctgs;

    function __construct()
    {
        $db_instance = new DBHelper();
        $this->ctgs = $db_instance->ctgs;
    }

    public function get_categories()
    {
        $cursor = $this->ctgs->find();
        return iterator_to_array($cursor);
    }

    public function create_category($category)
    {
        $category['saved_at'] = new MongoDate();
        $this->ctgs->insert($category);
        return $category;
    }

    public function update_category($category, $id)
    {
        $category['saved_at'] = new MongoDate();
        $this->ctgs->update(
            array("_id" => new MongoId($id)), array('$set' => $category)
        );
        return $category;
    }

    public function delete_category($id)
    {
        return $this->ctgs->remove(array("_id" => new MongoId($id)));
    }
}