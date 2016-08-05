<?php namespace App\Http\Controllers\SocialHub;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Libraries\SocialHub\SocialhubFeedLib;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $socialHubFeedLib = new SocialhubFeedLib();
        $getSocialHubFeed = $socialHubFeedLib->get();
        
        $socialHubFeedArray = json_decode($getSocialHubFeed['body'], true);
        
        return $socialHubFeedArray;
    }
}

