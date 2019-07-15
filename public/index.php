<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');
define('TX_COMM', __DIR__.'/../app/common/common/');
define('TX_HOME_COMM', __DIR__.'/../app/home/common/');
define('TX_ADMIN_COMM', __DIR__.'/../app/admin/common/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
