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
        $pdo = Service::get("pdo");
        $object_class_name = static::class;
        $table_name = static::getTable();
        $query = "select * from $table_name";

        $result = null;
        if ("all" == $param) {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = self::createObjectFromArray($object_class_name, $row);
            }
        } elseif (is_numeric($param)) {
            $query = $query . " where id=:id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array("id" => $param));
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result = self::createObjectFromArray($object_class_name, $row);
            }
        }
        return $result;
    }

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