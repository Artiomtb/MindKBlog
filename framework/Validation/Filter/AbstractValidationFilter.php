<?php

namespace Framework\Validation\Filter;

/**
 * Абстрактный класс для валидационного фильтра
 * @package framework\Validation\Filter
 */
abstract class AbstractValidationFilter
{
    /**
     * @var string текст ошибки, если таковая есть
     */
    protected $error;

    /**
     * Абстрактная функция, проверяющая валидность текущего значения
     * @param mixed $value значение, валидность котрого необходимо проверить
     * @return bool является ли значение валидным
     */
    abstract function isValid($value);

    /**
     * Возвращает текст ошибки
     * @return string текст ошибки
     */
    public function getError()
    {
        return $this->error;
    }
}