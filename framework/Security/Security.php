<?php


namespace Framework\Security;

use Framework\DI\Service;
use Framework\Security\Model\UserInterface;

/**
 * Класс для обеспечения безопасности доступа
 * @package Framework\Security
 */
class Security
{
    /**
     * Возвращает true, если текущий пользователь авторизован
     * @return bool
     */
    public function isAuthenticated()
    {
        return !is_null($this->getUser());
    }

    /**
     * Возвращает текущего пользователя или null, если пользователь не авторизован
     * @return UserInterface|null
     */
    public function getUser()
    {
        return Service::get("session")->get("user");
    }

    /**
     * Устанавливает текщего юзера
     * @param $user
     */
    public function setUser($user)
    {
        Service::get("session")->set("user", $user);
    }

    /**
     * Удаляет юзера из сессии
     */
    public function clear()
    {
        Service::get("session")->remove("user");
    }

    /**
     * Создает, возвращает и сохраняет CSRF токен в сессию
     * @return string CSRF токен
     */
    public function createCSRFToken()
    {
        $token = md5(uniqid(mt_rand(), true));
        Service::get("session")->set("csrf_token", $token);
        return $token;
    }

    /**
     * Проверяет, является ли CSRF токен, пришедший в POST запросе, верным
     * @return bool
     */
    public function validateCSRFToken()
    {
        $valid_token = Service::get("session")->getAndRemove("csrf_token");
        if (isset($_POST["csrf_token"]) && $_POST["csrf_token"] === $valid_token) {
            return true;
        } else {
            return false;
        }
    }

}