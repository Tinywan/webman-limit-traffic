<?php

return [
    'enable' => true,
    'limit' => [
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
