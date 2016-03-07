<?php


namespace Framework\Validation\Filter;

/**
 * Фильтр валидации для проверки длинны строки
 * @package Framework\Validation\Filter
 */
class Length extends AbstractValidationFilter
{
    private $from;
    private $to;

    /**
     * Конструктор фильтра, принимает параметры длины от и до(необязательный)
     * @param int $from минимальное допустимое значение длины
     * @param int|null $to максимальное допустимое значение длины (если не указана, то проверка этого параметра игнорируется)
     */
    public function __construct($from, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Возвращает true, если длина строки соответствует указанным в конструкторе фильтра параметрам
     * @param string $value строка для проверки
     * @return bool результат
     */
    function isValid($value)
    {
        $value_length = strlen(trim($value));
        if ($value_length >= $this->from && (($this->to) ? $value_length <= $this->to : true)) {
            return true;
        } else {
            $this->error = "String length should be greater than $this->from" .
                (($this->to) ? " and lower than $this->to" : "");
            return false;
        }
    }
}