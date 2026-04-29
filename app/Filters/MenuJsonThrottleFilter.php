<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MenuJsonThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $identifier = (string) $request->getUri()->getSegment(1);

        if (strtolower($identifier) === 'code') {
            $identifier = (string) $request->getUri()->getSegment(2);
        }

        $rawKey = sprintf('menu-json-%s-%s', strtolower($identifier), $request->getIPAddress());
        $key = preg_replace('/[^a-z0-9_-]/i', '-', $rawKey) ?? 'menu-json';

        if (service('throttler')->check($key, 120, MINUTE)) {
            return null;
        }

        return service('response')
            ->setStatusCode(429)
            ->setJSON([
                'error' => 'Too many requests. Please retry shortly.',
            ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
