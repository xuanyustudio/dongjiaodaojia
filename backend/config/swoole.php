<?php
return [
    'http'       => [
        'enable'     => true,
        'host'       => '0.0.0.0',
        'port'       => 9501,
        'worker_num' => swoole_cpu_num(),
        'options'   => [
            'pid_file'              => runtime_path() . 'swoole.pid',
            'log_file'              => runtime_path() . 'swoole.log',
            'daemonize'             => true,
            // Normally this value should be 1~4 times larger according to your cpu cores.
            'reactor_num'           => swoole_cpu_num(),
            'worker_num'            => swoole_cpu_num(),
            'task_worker_num'       => swoole_cpu_num(),
            'task_enable_coroutine' => true,
            'task_max_request'      => 3000,
            'enable_static_handler' => true,
            'document_root'         => root_path('public'),
            'package_max_length'    => 20 * 1024 * 1024,
            'buffer_output_size'    => 10 * 1024 * 1024,
            'socket_buffer_size'    => 128 * 1024 * 1024,
            'max_request'           => 3000,
            'send_yield'            => true,
        ],
    ],
    'websocket'  => [
        'enable'        => false,
        'handler'       => Handler::class,
        'ping_interval' => 25000,
        'ping_timeout'  => 60000,
        'room'          => [
            'type'  => 'table',
            'table' => [
                'room_rows'   => 4096,
                'room_size'   => 2048,
                'client_rows' => 8192,
                'client_size' => 2048,
            ],
            'redis' => [
                'host'          => '127.0.0.1',
                'port'          => 6379,
                'max_active'    => 3,
                'max_wait_time' => 5,
            ],
        ],
        'listen'        => [],
        'subscribe'     => [],
    ],
    'rpc'        => [
        'server' => [
            'enable'     => false,
            'host'       => '0.0.0.0',
            'port'       => 9000,
            'worker_num' => swoole_cpu_num(),
            'services'   => [],
        ],
        'client' => [],
    ],
    //队列
    'queue'      => [
        'enable'  => false,
        'workers' => [],
    ],
    'hot_update' => [
        'enable'  => true,
        'name'    => ['*.php'],
        'include' => [app_path()],
        'exclude' => [],
    ],
    'pool'       => [
        'db'    => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        'cache' => [
            'enable'        => true,
            'max_active'    => 3,
            'max_wait_time' => 5,
        ],
        //自定义连接池
    ],
    'coroutine'=>[
        'enable' => true,
        'flags' => 'SWOOLE_HOOK_ALL'
    ],
    //连接池
    'tables'     => [],
    //每个worker里需要预加载以共用的实例
    'concretes'  => [],
    //重置器
    'resetters'  => [],
    //每次请求前需要清空的实例
    'instances'  => [],
    //每次请求前需要重新执行的服务
    'services'   => [],
];
