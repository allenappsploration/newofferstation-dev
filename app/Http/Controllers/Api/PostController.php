<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Post;

class PostController extends Controller
{
    public function index() {
        
        $result = Post::with('postImg')->orderBy('created_at', 'desc')->paginate(50);
        
        return $result;
    }
}
