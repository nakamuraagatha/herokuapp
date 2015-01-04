<?php

/**
 * Created by PhpStorm.
 * User: Mystique
 * Date: 1/4/2015
 * Time: 3:29 PM
 */
class QuotesModel
{
    private $quotes;

    function __construct()
    {
        $db_instance = new DBHelper();
        $this->quotes = $db_instance->quotes;
    }

    public function create_quote($quote)
    {
        $quote['saved_at'] = new MongoDate();
        $this->quotes->insert($quote);
        return $quote;
    }

    public function get_quotes($ctg)
    {
        $cursor = $this->quotes->find(array("ctg_id" => $ctg));
        return iterator_to_array($cursor);
    }

    public function update_quote($quote, $id)
    {
        $quote['saved_at'] = new MongoDate();
        $this->quotes->update(
            array("_id" => new MongoId($id)), array('$set' => $quote)
        );
        return $quote;
    }

    public function delete_quote($id)
    {
        return $this->quotes->remove(array("_id" => new MongoId($id)));
    }

}