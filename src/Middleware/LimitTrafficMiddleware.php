<?php
/**
 * @desc LimitTrafficMiddleware
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/30 23:21
 */
declare(strict_types=1);

namespace Tinywan\LimitTraffic\Middleware;


use Tinywan\LimitTraffic\RateLimit;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class LimitTrafficMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function process(Request $request, callable $next): Response
    {
        if ($result = RateLimit::check(30,60)) {
            return new Response(200, [
                'Content-Type' => 'application/json',
                'X-Rate-Limit-Limit' => $result['limit'],
                'X-Rate-Limit-Remaining' => $result['remaining'],
                'X-Rate-Limit-Reset' => $result['reset']
            ], 'Too Many Requests');
        }
        return $next($request);
    }
}