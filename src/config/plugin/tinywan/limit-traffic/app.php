<?php

return [
    'enable' => true,
    'limit' => [
        'limit_key' => 'none',  // none:以ip地址为限流键，'get.id','header.X-Token','post.user_id',也可以自定义限流键
        'limit_method' => ['GET'],  // 只限 GET
        'limit' => 10, // 请求次数
        'window_time' => 60, // 窗口时间，单位：秒
        'status' => 429,  // HTTP 状态码
        'body' => [  // 响应信息
            'code' => 0,
            'msg' => '请求太多，请稍后重试！',
            'data' => null
        ]
    ]
];
