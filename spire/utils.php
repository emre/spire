<?php

namespace Spire\Utils;

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class JsonResponse extends Response {

    public function __construct($content = '', $status = 200, $headers = array(), $flush_headers=FALSE)
    {
        if(!$flush_headers) {
            $headers = array_merge(['Content-Type' => 'application/json'], $headers);
        }
        parent::__construct($content, $status, $headers);
    }

}