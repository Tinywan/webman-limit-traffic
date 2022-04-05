<?php
/**
 * @desc LimitTrafficMiddleware
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/30 23:21
 */
declare(strict_types=1);

namespace Tinywan\LimitTraffic\Middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Tinywan\LimitTraffic\RateLimiter;
use Webman\MiddlewareInterface;

class LimitTrafficMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request $request, callable $handler): Response
    {
        if ($result = RateLimiter::traffic()) {
            return new Response($result['status'], [
                'Content-Type' => 'application/json',
                'X-Rate-Limit-Limit' => $result['limit'],
                'X-Rate-Limit-Remaining' => $result['remaining'],
                'X-Rate-Limit-Reset' => $result['reset']
            ], json_encode($result['body']));
        }
        return $handler($request);
    }
}
