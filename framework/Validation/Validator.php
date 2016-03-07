<?php


namespace Framework\Validation;

use Framework\DI\Service;

/**
 * Класс для валидации сущности
 * @package Framework\Validation
 */
class Validator
{
    private $entity;
    private $rules;
    private $errors;
    private $logger;

    /**
     * Конструктор валидатора
     * @param object $entity экземпляр сущности для проверки
     */
    public function __construct($entity)
    {
        $this->logger = Service::get("logger");
        $this->entity = $entity;
        $this->rules = $entity->getRules();
    }

    /**
     * Функция проверяет, является ли сущность валидной
     * @return bool результат
     */
    public function isValid()
    {
        $result = true;
        $this->logger->info("Validation for entity " . get_class($this->entity));
        foreach ($this->rules as $field => $rules) {
            $field_value = $this->entity->$field;
            $this->logger->debug("Checking validity of field \"$field\" with value \"$field_value\"");
            foreach ($rules as $rule) {
                if (!$rule->isValid($field_value)) {
                    $result = false;
                    $error = $rule->getError();
                    $this->errors[$field] = $error;
                    $this->logger->warn("Validation error for field \"$field\" with value \"$field_value\": $error");
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает массив из ошибок валидации
     * @return array массив строк
     */
    public function getErrors()
    {
        return $this->errors;
    }
}