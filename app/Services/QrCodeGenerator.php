<?php

namespace App\Services;

use Throwable;

class QrCodeGenerator
{
    private const ENDPOINT = 'https://api.qrserver.com/v1/create-qr-code/';
    private const PNG_SIGNATURE = "\x89PNG\r\n\x1a\n";

    public function generatePng(string $data): ?string
    {
        $url = self::ENDPOINT . '?' . http_build_query([
            'data'    => $data,
            'size'    => '1000x1000',
            'format'  => 'png',
            'ecc'     => 'M',
            'qzone'   => '4',
            'color'   => '0-0-0',
            'bgcolor' => '255-255-255',
        ], '', '&', PHP_QUERY_RFC3986);

        try {
            $response = \Config\Services::curlrequest([
                'timeout'         => 10,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ], null, null, false)->get($url);
        } catch (Throwable) {
            return null;
        }

        $body = (string) $response->getBody();

        if ($response->getStatusCode() !== 200 || ! str_starts_with($body, self::PNG_SIGNATURE)) {
            return null;
        }

        return $body;
    }
}
