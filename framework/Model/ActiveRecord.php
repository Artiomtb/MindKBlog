<?php


namespace Framework\Model;


abstract class ActiveRecord
{

    public static function find($param)
    {
        return true;
    }

    public function save()
    {
    }

    public static function getTable()
    {
    }
}