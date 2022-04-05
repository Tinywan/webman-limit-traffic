<?php
/**
 * @desc RateLimit
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/30 23:26
 */
declare(strict_types=1);

namespace Tinywan\LimitTraffic;

use support\Redis;

class RateLimiter
{
    public const LIMIT_TRAFFIC_SCRIPT_SHA = 'limit:traffic:script';

    public const LIMIT_TRAFFIC_PRE = 'limit:traffic:pre:';

    /**
     * 校测
     * @return array|false
     */
    public static function traffic()
    {
        $config = config('plugin.tinywan.limit-traffic.app.limit');
        $scriptSha = Redis::get(self::LIMIT_TRAFFIC_SCRIPT_SHA);
        if (!$scriptSha) {
            $script = <<<luascript
            local result = redis.call('SETNX', KEYS[1], 1);
            if result == 1 then
                return redis.call('expire', KEYS[1], ARGV[2])
            else
                if tonumber(redis.call("GET", KEYS[1])) >= tonumber(ARGV[1]) then
                    return 0
                else
                    return redis.call("INCR", KEYS[1])
                end
            end
luascript;
            $scriptSha = Redis::script('load', $script);
            Redis::set(self::LIMIT_TRAFFIC_SCRIPT_SHA, $scriptSha);
        }
        $limitKey = self::LIMIT_TRAFFIC_PRE . request()->getRealIp();
        $result = Redis::rawCommand('evalsha', $scriptSha, 1, $limitKey, $config['limit'], $config['window_time']);
        if ($result === 0) {
            return [
                'limit' => $config['limit'],
                'remaining' => $config['limit'] - Redis::get($limitKey),
                'reset' => Redis::ttl($limitKey),
                'status' => $config['status'],
                'body' => $config['body'],
            ];
        }
        return false;
    }

    /**
     * @desc: 返回允许的请求的最大数目及时间，例如，[100, 600] 表示在 600 秒内最多 100 次的 API 调
     * @author Tinywan(ShaoBo Wan)
     */
    public static function getRateLimit(): array
    {
        $config = config('plugin.tinywan.limit-traffic.app.limit');
        return [$config['limit'], $config['window_time']];
    }
}
