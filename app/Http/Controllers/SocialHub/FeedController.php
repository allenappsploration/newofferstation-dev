<?php namespace App\Http\Controllers\SocialHub;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Libraries\SocialHub\SocialhubFeedLib;
use \App\Models\SocialhubItems;
use App\Models\SocialhubItemsPg;

class FeedController extends Controller
{
    public function index()
    {
        $this->storeItemsIntoDB();

        $socialhubPagination = SocialhubItemsPg::where('is_processed', 0)->get();

        if (count($socialhubPagination)) {
            $nextPageJob = (new \App\Jobs\CheckSHItemsNextPage());
            $this->dispatch($nextPageJob);
        }

        $job = (new \App\Jobs\ConvertSHItemsToPosts())->delay(30);
        $this->dispatch($job);
    }

    private function storeItemsIntoDB()
    {
        $socialHubFeedLib = new SocialhubFeedLib();
        $getSocialHubFeed = $socialHubFeedLib->get();

        if (is_null($getSocialHubFeed)) {
            return "Something has gone wrong in the database.";
        }

        $socialHubFeedArray = json_decode($getSocialHubFeed['body'], true);

        $socialHubFeed = $socialHubFeedArray['items'];

        if (count($socialHubFeed) > 0) {
            $tags = "";

            for ($i = 0; $i < count($socialHubFeed); $i++) {
                $socialHubItems = new SocialhubItems();
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

                if (isset($socialHubFeed[$i]['tags'])) {
                    for ($j = 0; $j < count($socialHubFeed[$i]['tags']); $j++) {
                        $tags .= (string)$socialHubFeed[$i]['tags'][$j]['name'] . ", ";
                    }
                }

                $socialHubItems->tag = rtrim($tags, ", ");

                $socialHubItems->post_language = (string)$socialHubFeed[$i]['post_language'];

                if (!$socialHubItems->save()) {
                    return "Something has gone wrong while storing into database.";
                }
            }
        }

        if (isset($socialHubFeedArray['paging'])) {
            
            $socialHubNextPage = $socialHubFeedArray['paging'];

            if (isset($socialHubNextPage['next'])) {
                $socialhubPagination = new SocialhubItemsPg();
                $socialhubPagination->next_url = $socialHubNextPage['next'];
                $socialhubPagination->save();
            }
        }
    }

    private function str_replace_first($from, $to, $subject)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $subject, 1);
    }
}

