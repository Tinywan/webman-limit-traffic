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

class RateLimit
{
    const LIMIT_TRAFFIC_SCRIPT_SHA = 'limit:traffic:script';

    const LIMIT_TRAFFIC_PRE = 'limit:traffic:pre:';

    /**
     * 校测
     * @return array|false
     */
    public static function check()
    {
        $config = config('plugin.tinywan.limit-traffic.app.traffic');
        $scriptSha = Redis::get(self::LIMIT_TRAFFIC_SCRIPT_SHA);
        if (!$scriptSha) {
            $script = <<<luascript
            local result = redis.call('SETNX',KEYS[1],1);
            if result == 1 then
                return redis.call('expire',KEYS[1],ARGV[2])
            else
                if tonumber(redis.call("GET", KEYS[1])) >= tonumber(ARGV[1]) then
                    return 0
                else
                    return redis.call("INCR", KEYS[1])
                end
            end
luascript;
            $scriptSha = Redis::script('LOAD', $script);
            Redis::set(self::LIMIT_TRAFFIC_SCRIPT_SHA, $scriptSha);
        }
        $limitKey = self::LIMIT_TRAFFIC_PRE . request()->getRealIp();
        $result = Redis::evalSha($scriptSha, [$limitKey, $config['limit'], $config['window_time']], 1);
        if ($result === 0) {
            return [
                'limit' => $config['limit'],
                'remaining' => $config['limit'] - Redis::get($limitKey),
                'reset' => Redis::ttl($limitKey)
            ];
        }
        return false;
    }
}