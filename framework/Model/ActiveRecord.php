<?php


namespace Framework\Model;

use Framework\DI\Service;
use PDO;

/**
 * Class ActiveRecord абстрактный класс разрешающий доступ в БД
 * @package Framework\Model
 */
abstract class ActiveRecord
{

    /**
     * Возвращает объект или массив объектов определенного класса.
     * @param string|int $param если задан all - вернется массив объектов, если число - объект с id = $param
     * @return array|object объект (поиск по $param) или массив объектов (если $param = all)
     */
    public static function find($param)
    {
        $result = null;
        if ("all" == $param) {
            $result = self::findAll();
        } elseif (is_numeric($param)) {
            $result = self::findByParam("id", $param);
        }
        return $result;
    }

    /**
     * Добавляет строку в БД для соответствующей сущности
     */
    public function save()
    {
        $logger = Service::get("logger");
        $table_name = $this->getTable();
        $pdo = Service::get("pdo");
        $object_fields = get_object_vars($this);

        $query = "INSERT INTO " . $table_name . " (" .
            join(", ", array_keys($object_fields)) . ") VALUES(" .
            join(", ", array_map(function ($field) {
                return ":" . $field;
            }, array_keys($object_fields))) . ")";

        $pdo->beginTransaction();
        $stmt = $pdo->prepare($query);
        foreach ($object_fields as $field => $value) {
            $stmt->bindValue(":" . $field, $value);
        }
        $logger->debug("Executing query $query");
        $stmt->execute();
        $pdo->commit();

        $logger->info(print_r($this, true) . " was saved to table $table_name");
    }

    /**
     * Реализует возможность поиска по любому свойству объекта через имя метода в формате findBy<field_name>
     * @param $name string имя вызываемого метода
     * @param array $arguments аргументы метода
     * @return null|object найденный объект или null
     */
    public static function __callStatic($name, $arguments)
    {
        $result = null;
        if (stripos($name, "findby") === 0 && count($arguments) > 0) {
            $param_name = substr($name, 6);
            $param_value = $arguments[0];
            $result = self::findByParam($param_name, $param_value);
        }
        return $result;
    }

    /**
     * Находит параметр в БД по имени свойста и его значению
     * @param string $name имя свойства
     * @param mixed $value значение
     * @return null|object
     */
    private static function findByParam($name, $value)
    {
        $pdo = Service::get("pdo");
        $result = null;
        $object_class_name = static::class;
        $query = "select * from " . static::getTable() . " where $name = :$name";
        $stmt = $pdo->prepare($query);
        $arr = array($name => $value);
        $stmt->execute($arr);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result = self::createObjectFromArray($object_class_name, $row);
        }
        return $result;
    }

    private static function findAll()
    {
        $pdo = Service::get("pdo");
        $object_class_name = static::class;
        $query = "select * from " . static::getTable();
        $result = array();
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = self::createObjectFromArray($object_class_name, $row);
        }
        return $result;
    }

    public static function getTable()
    {
    }

    /**
     * Возвращает объект по имени его класса и ассоциативному массиву в формате имя параметра => значение
     * @param string $object_class_name имя класса объекта
     * @param array $array ассоциативный массив-строка из БД
     * @return object экземпляр заданного класса с заданными параметрами
     */
    private static function createObjectFromArray($object_class_name, $array)
    {
        $object = new $object_class_name;
        foreach ($array as $index => $value) {
            $object->$index = $value;
        }
        return $object;
    }
}