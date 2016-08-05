<?php namespace App\Http\Controllers\SocialHub;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Libraries\SocialHub\SocialhubFeedLib;
use \App\Models\SocialhubItems;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $socialHubFeedLib = new SocialhubFeedLib();
        $getSocialHubFeed = $socialHubFeedLib->get();
        
        $socialHubFeedArray = json_decode($getSocialHubFeed['body'], true);
        
        $socialHubFeed = $socialHubFeedArray['items'];
        
        if (count($socialHubFeed) > 0) {
            $tags = "";
            $language = "";
            
            for ($i = 0; $i < count($socialHubFeed); $i++) {
                $socialHubItems = new SocialhubItems;
                $socialHubItems->nid = $socialHubFeed[$i]['nid'];
                $socialHubItems->tid = $socialHubFeed[$i]['brand']['tid'];
                $socialHubItems->name = $socialHubFeed[$i]['brand']['name'];
                $socialHubItems->author_name = $socialHubFeed[$i]['author']['name'];
                $socialHubItems->raw_body = strip_tags($socialHubFeed[$i]['body']); // TODO: This should be change to raw body once SH ready
                $socialHubItems->social_channel = $socialHubFeed[$i]['social_channel'];
                $socialHubItems->post_type = $socialHubFeed[$i]['post_type'];
                $socialHubItems->url = $socialHubFeed[$i]['url'];
                $socialHubItems->status = $socialHubFeed[$i]['status'];
                $socialHubItems->created = date("Y-m-d H:i", $socialHubFeed[$i]['created']);
                
                if (isset($socialHubFeed[$i]['images'][0]['original'])) {
                    $socialHubItems->img = (string)$socialHubFeed[$i]['images'][0]['original']['url'];
                    $socialHubItems->img_width = $socialHubFeed[$i]['images'][0]['original']['width'];
                    $socialHubItems->img_height = $socialHubFeed[$i]['images'][0]['original']['height'];
                }
                $socialHubItems->extra_data = json_encode($socialHubFeed[$i]['data']);
                
                if (count($socialHubFeed[$i]['tags']) > 0) {
                    for ($j = 0; $j < count($socialHubFeed[$i]['tags']); $j++) {
                        $tags .= (string)$socialHubFeed[$i]['tags'][$j]['name'] . ", ";
                    }
                }
                
                $socialHubItems->tag = rtrim($tags, ", ");
                
                $socialHubItems->post_language = (string)$socialHubFeed[$i]['post_language'];
                
                if (!$socialHubItems->save()) {
                    return false;
                }
            }
        }
    }
}

