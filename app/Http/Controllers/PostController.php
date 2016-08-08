<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class PostController extends Controller
{
    const DATE_FORMAT = 'Y-m-d';
    const LIMIT_RECORD = 10;

    public function creation() {
        $job = (new \App\Jobs\ConvertSHItemsToPosts())->delay(30);
        $this->dispatch($job);
    }
}
