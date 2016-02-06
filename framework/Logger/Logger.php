<?php

namespace Framework\Logger;

/**
 * Class Logger Класс синглтон дл логгирования в файл
 * @package Framework\Logger
 */
class Logger
{

    const DEBUG_LEVEL = "DEBUG";
    const INFO_LEVEL = "INFO";
    const WARN_LEVEL = "WARN";
    const ERROR_LEVEL = "ERROR";

    private static $default_params = array("path" => __DIR__ . "/../../log.log",
        "date_format" => "Y.m.d H:i:s.u",
        "error_level" => self::INFO_LEVEL,
        "backtrace_enabled" => true);

    private static $instance;

    private static $path;
    private static $date_format;
    private static $error_level;
    private static $error_level_formatted;
    private static $backtrace_enabled;

    /**
     * Приватный конструктор логгера
     * @param $params
     */
    private function __construct($params)
    {
        self::$path = $params["path"];
        self::$date_format = $params["date_format"];

        $error_level = $params["error_level"];
        if (strcasecmp($error_level, self::DEBUG_LEVEL) === 0) {
            self::$error_level_formatted = self::DEBUG_LEVEL;
            self::$error_level = 1;
        } elseif (strcasecmp($error_level, self::INFO_LEVEL) === 0) {
            self::$error_level_formatted = self::INFO_LEVEL;
            self::$error_level = 2;
        } elseif (strcasecmp($error_level, self::WARN_LEVEL) === 0) {
            self::$error_level_formatted = self::WARN_LEVEL;
            self::$error_level = 3;
        } else {
            self::$error_level_formatted = self::ERROR_LEVEL;
            self::$error_level = 4;
        }

        self::$backtrace_enabled = $params["backtrace_enabled"];

        file_put_contents(self::$path, "\n", FILE_APPEND);
    }

    /**
     * Статический метод создания логгера синглтона.
     * @param array $params параметры логгера: path(путь в файлу с логом), date_format(формат даты), error_level[debug|info|warn|error], backtrace_enabled[true|false](добавлять ли в лог информацию о файле/класе/методе вызова). При недостатке какого-то будут взяты значения по умолчанию.
     * @return Logger синглтон логгера
     */
    public static function getLogger($params = array())
    {
        if (!isset(self::$instance)) {
            self::$instance = new Logger(array_merge(self::$default_params, $params));
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    /**
     * Пишет в лог файл в уровнем DEBUG (если таковой разрешен)
     * @param string $message сообщение
     */
    public function debug($message)
    {
        if ($this->isDebugEnabled()) {
            $this->log($message, self::DEBUG_LEVEL);
        }
    }

    /**
     * Пишет в лог файл в уровнем INFO (если таковой разрешен)
     * @param string $message сообщение
     */
    public function info($message)
    {
        if ($this->isInfoEnabled()) {
            $this->log($message, self::INFO_LEVEL);
        }
    }

    /**
     * Пишет в лог файл в уровнем WARN (если таковой разрешен)
     * @param string $message сообщение
     */
    public function warn($message)
    {
        if ($this->isWarningEnabled()) {
            $this->log($message, self::WARN_LEVEL);
        }
    }

    /**
     * Пишет в лог файл в уровнем ERROR
     * @param string $message сообщение
     */
    public function error($message)
    {
        $this->log($message, self::ERROR_LEVEL);
    }

    /**
     * Возвращает разрешены ли записи в лог уровня DEBUG
     * @return bool true, если разрешены записи в лог уровня DEBUG
     */
    public function isDebugEnabled()
    {
        return (self::$error_level <= 1);
    }

    /**
     * Возвращает разрешены ли записи в лог уровня INFO
     * @return bool true, если разрешены записи в лог уровня INFO
     */
    public function isInfoEnabled()
    {
        return (self::$error_level <= 2);
    }

    /**
     * Возвращает разрешены ли записи в лог уровня WARN
     * @return bool true, если разрешены записи в лог уровня WARN
     */
    public function isWarningEnabled()
    {
        return (self::$error_level <= 3);
    }

    /**
     * Возвращает разрешены ли записи в лог уровня ERROR
     * @return bool записи в лог уровня ERROR разрешены всегда - true
     */
    public function isErrorEnabled()
    {
        return true;
    }

    /**
     * Пишет в файл сообщение с опредеоленный уровнем
     * @param string $message сообщение
     * @param string $error_level уровень
     */
    private function log($message, $error_level)
    {
        $backtrace_formatted = (self::$backtrace_enabled) ? $this->getBacktrace() : "";
        file_put_contents(self::$path, $this->formatDate(self::$date_format) . " [" . str_pad($error_level, 5, " ") . "] $backtrace_formatted: $message\n", FILE_APPEND);
    }

    /**
     * Возвращает текущую дату в заданном формате (включая милисекунды)
     * @param string $format формат даты
     * @return string текущая дата
     */
    private function formatDate($format)
    {
        $utimestamp = microtime(true);
        $timestamp = floor(microtime(true));
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);
        return date(preg_replace('`(?<!\\\\)u`', str_pad($milliseconds, 6, "0"), $format), $timestamp);
    }

    /**
     * Возвращает форматированную строку с именем файла, номером строки в нём, класса и метода, где была вызвана функция логгирования
     * @return string отформатированная информация о вызове логгирования
     */
    private function getBacktrace()
    {
        $backtrace = debug_backtrace();
        $class_name = "<none>";
        $func_name = "<none>";
        $second_backtrace = $backtrace[2];
        $file_name = $second_backtrace["file"];
        $line_num = $second_backtrace["line"];
        if (array_key_exists(3, $backtrace)) {
            $third_backtrace = $backtrace[3];
            if (array_key_exists("class", $third_backtrace)) {
                $class_name = $third_backtrace["class"];
            }
            if (array_key_exists("function", $third_backtrace)) {
                $func_name = $third_backtrace["function"];
            }
        }
        return "$file_name:$line_num ( $class_name -> $func_name ) ";
    }
}