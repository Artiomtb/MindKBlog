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
        $object_class_name = get_called_class();
        $table_name = call_user_func(array($object_class_name, "getTable"));
        $query = "select * from $table_name ";

        if ("all" == $param) {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = self::createObjectFromRow($object_class_name, $row);
            }
        } else {
            $query = $query . "where id=:id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array("id" => $param));
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result = self::createObjectFromRow($object_class_name, $row);
            }
        }
        return $result;
    }

//    public function save()
//    {
//        $pdo = Service::get("pdo");
//        $table_name = $this->getTable();
//        $id = $this->id;
//        echo "Table $table_name, id $id";
//        if ($this->isObjectExists($pdo, $table_name, $id)) {
//            echo "Update";
//            $this->updateObject($pdo, $table_name, $this);
//        } else {
//            echo "Insert";
//        }
//    }

    public static function getTable()
    {
    }

    /**
     * Возвращает объект по имени его класса и ассоциативному массиву в формате имя параметра => значение
     * @param string $object_class_name имя класса объекта
     * @param array $row ассоциативный массив-строка из БД
     * @return object экземпляр заданного класса с заданными параметрами
     */
    private static function createObjectFromRow($object_class_name, $row)
    {
        $object = new $object_class_name;
        foreach ($row as $index => $value) {
            $object->$index = $value;
        }
        return $object;
    }

//    private function isObjectExists($pdo, $table_name, $id)
//    {
//        $stmt = $pdo->prepare("SELECT id FROM " . $table_name . " WHERE id=:id");
//        $stmt->execute(array("id" => $id));
//        return !empty($stmt->fetch(PDO::FETCH_ASSOC));
//    }
//
//    private function updateObject($pdo, $table_name, $object)
//    {
//        print_r($object);
//        $query = "UPDATE $" . $table_name . " SET ";
//
//        $query = $query . " WHERE id=:id";
//    }
}