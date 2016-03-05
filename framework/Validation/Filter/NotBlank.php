<?php


namespace Framework\Validation\Filter;

/**
 * Фильтр валидации для проверки является ли стока заполненной
 * @package Framework\Validation\Filter
 */
class NotBlank extends AbstractValidationFilter
{
    /**
     * Возврашает true, если переданное значение не является пустім
     * @param string $value строка для проверки
     * @return bool результат
     */
    function isValid($value)
    {
        if (strlen(trim($value)) > 0) {
            return true;
        } else {
            $this->error = "Value should not be blank";
            return false;
        }
    }
}