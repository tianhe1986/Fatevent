<?php

namespace Fatevent;

class Fatevent
{
    /**
     * 全局事件数组
     *
     * @var array 
     */
    protected static $events = [];

    /**
     * 
     * @return array
     */
    public static function getEvents()
    {
        return self::$events;
    }

    /**
     * 增加事件处理器
     * 
     * @param string $name 事件名
     * @param callable $handler 处理函数
     * @param mixed $data 附加的数据
     */
    public static function on($name, $handler, $data = null)
    {
        self::$events[$name][] = [$handler, $data, false];
    }

    /**
     * 增加一次性事件处理器
     * 
     * @param string $name 事件名
     * @param callable $handler 处理函数
     * @param mixed $data 附加的数据
     */
    public static function once($name, $handler, $data = null)
    {
        self::$events[$name][] = [$handler, $data, true];
    }

    /**
     * 移除事件上的处理器
     * 
     * @param string $name 事件名
     * @param callable|null $handler 处理函数, 如果为null，则移除该事件上所有处理器
     * 
     * @return bool 是否成功移除对应处理器
     */
    public static function off($name, $handler = null)
    {
        if (empty(self::$events[$name])) {
            return false;
        }

        if ($handler === null) {
            unset(self::$events[$name]);
            return true;
        }

        $removed = false;
        foreach (self::$events[$name] as $i => $event) {
            if ($event[0] === $handler) {
                unset(self::$events[$name][$i]);
                $removed = true;
            }
        }

        if ($removed) {
            self::$events[$name] = array_values(self::$events[$name]);
        }

        return $removed;
    }

    /**
     * 清空事件处理器
     */
    public static function offAll()
    {
        self::$events = [];
    }

    /**
     * 触发事件
     * 
     * @param string $name 事件名称
     * @param mixed $data 附加的额外数据
     */
    public static function trigger($name, $data = null)
    {
        if (! empty(self::$events[$name])) {
            $removed = false;
            foreach (self::$events[$name] as $i => $event) {
                $result = $event[0]($data, $event[1]);
                if ($event[2]) {
                    unset(self::$events[$name][$i]);
                    $removed = true;
                }
                if ($result === false) {
                    break;
                }
            }

            if ($removed) {
                self::$events[$name] = array_values(self::$events[$name]);
            }
        }
    }
}
