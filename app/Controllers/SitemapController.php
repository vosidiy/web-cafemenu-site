<?php

namespace App\Controllers;

class SitemapController extends BaseController
{
    public function index()
    {
        $urls = [
            [
                'loc' => base_url('/'),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => site_url('ru'),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
        ];

        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setContentType('application/xml')
            ->setBody(view('sitemap_xml', ['urls' => $urls]));
    }
}
