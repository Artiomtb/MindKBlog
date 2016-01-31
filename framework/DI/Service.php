<?php

namespace Framework\DI;

class Service
{
    private static $services = array();

    /**
     * Метод для получения зарегистрированного сервиса по имени
     * @param $serviceName имя сервиса
     * @return object|null экземпляр сервиса, или null, если не зарегистрировано сервиса с таким именем
     */
    public static function get($serviceName)
    {
        return array_key_exists($serviceName, self::$services) ? self::$services[$serviceName] : null;
    }

    /**
     * Метод для регистрации сервиса по имени
     * @param $serviceName имя сервиса
     * @param $serviceClass класс сервиса
     */
    public static function set($serviceName, $serviceClass)
    {
        self::$services[$serviceName] = $serviceClass;
    }

    /**
     * Метод для множественной регистрации сервисов по имени
     * @param array $services ассоциативный массив в формате имя сервиса => класс сервиса (с namespace)
     */
    public static function setAll(array $services)
    {
        foreach ($services as $serviceName => $serviceClass) {
            self::set($serviceName, $serviceClass);
        }
    }

    /**
     * Метод для получения всех имен зарегистрированных сервисов
     * @return array из имен сервисов
     */
    public static function getAllServiceNames()
    {
        return array_keys(self::$services);
    }
}