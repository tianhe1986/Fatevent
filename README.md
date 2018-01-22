# Fatevent
Fatevent是一个轻量级的PHP事件库，使用非常容易。

# 安装
首先确保PHP版本在7.0以上。

推荐通过[Composer](https://getcomposer.org/)进行安装

Composer的安装请参考官方配置。

安装好Composer后，在你的项目中创建一个 composer.json 文件：
```
{
    "require": {
        "tianhe1986/fatevent": "*"
    }
}
```

然后在项目文件夹下执行:
```bash
$ composer install
```

之后添加autoloader:
```php
<?php
require 'vendor/autoload.php';
```

# 使用
事件的处理示例如下，首先，需要监听某个事件，附加处理函数：
```php
use Fatevent\Fatevent;

Fatevent::on('test.test',
    function($eventData, $defaultData){
        //do something
    },
    'default data');
```

以上代码监听了`test.test`事件。接下来，在需要的地方触发对应的事件：
```php
use Fatevent\Fatevent;
Fatevent::trigger('test.test', 'event data');
```

此时，之前附加的处理函数便会执行。在接下来的几个小节中，会详细的介绍使用的场景及函数。

## 增加监听
```php
function on($name, $handler, $data = null)
```
`$name`为事件名。

`$handler`为处理函数，函数定义如下：
```
function ($eventData, $defaultData)
```
`$eventData`为触发事件时附带的数据，`$defaultData`为监听时附带的数据。

`$data`为监听时附带的数据。

一个事件可以被多次监听，触发时多个处理函数会按监听的顺序依次调用。示例如下：
```php

use Fatevent\Fatevent;

class Test
{
    public function deal($eventData, $defaultData)
    {
        echo $eventData . " " . $defaultData . "\n";
    }
}

function globalDeal($eventData, $defaultData)
{
    echo "Hello, ". $defaultData . " " . $eventData . "\n";
}

Fatevent::on("test.test",
    function($eventData, $defaultData){
        echo "First\n";
    },
    "default data1");

Fatevent::on("test.test", "globalDeal", "default data2");

$test = new Test();
Fatevent::on("test.test", [$test, "deal"], "default data3");

Fatevent::trigger("test.test", "event data");
```

以上代码执行将输出
```bash
First
Hello, default data2 event data
event data default data3
```

## 增加一次性监听
```php
function once($name, $handler, $data = null)
```
参数与`on`一样，只是此处理函数在一次之后会被移除，下次再触发同一事件时不会再被调用。示例如下：
```php
use Fatevent\Fatevent;

Fatevent::on("test.test",
    function($eventData, $defaultData){
        echo "First\n";
    },
    "default data1");

Fatevent::once("test.test",
    function($eventData, $defaultData){
        echo "Second\n";
    },
    "default data2");

Fatevent::trigger("test.test", "event data");
Fatevent::trigger("test.test", "event data");
```

以上代码将输出
```bash
First
Second
First
```

## 事件停止处理
有时候，你希望停止事件被继续处理，你可以通过从处理函数中返回 false 来实现。如下所示：
```php
use Fatevent\Fatevent;

Fatevent::on("test.test",
    function($data, $defaultData){
        echo "First\n";
        return false;
    },
    "default data");

Fatevent::on("test.test",
    function($data, $defaultData){
        echo "Second\n";
    },
    "default data");

Fatevent::trigger("test.test", "event data");
```

以上代码将输出
```bash
First
```

注意，如果某个一次性处理函数还未被调用，事件就已经停止了，则此处理函数不会被移除。只有被调用过的一次性处理函数才会被移除。

## 取消监听
```php
function off($name, $handler)
```
可以取消某个处理函数，使用示例如下：
```php
use Fatevent\Fatevent;

// 全局函数
Fatevent::off("test.test", "function_name");

// 对象方法
Fatevent::off("test.test", [$object, "methodName"]);

// 静态类方法
Fatevent::off("test.test", ["\Bar", "methodName"]);
```

注意，匿名函数无法被取消，如果需要取消，请使用某个变量储存它。

如果`$handler`为 null，则该事件上所有的处理函数都会被取消。

### 触发事件
```php
trigger($name, $data = null)
```
会触发某项事件，其中`$data`为触发事件时附加的数据：
```php
use Fatevent\Fatevent;

Fatevent::on("test.test",
    function($eventData, $defaultData){
        echo $defaultData . " " . $eventData;
    },
    "Hello");

Fatevent::trigger("test.test", "World");
```

以上代码将输出
```bash
Hello World
```