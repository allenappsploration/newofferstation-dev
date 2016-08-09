<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Libraries\SocialHub\SocialhubFeedLib;
use App\Models\SocialhubItemsPg;
use App\Models\SocialhubItems;

class CheckSHItemsNextPage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->checkSHItemsNextPage();
    }
    
    private function checkSHItemsNextPage()
    {
        $socialhubPgCollection = SocialhubItemsPg::where('is_processed', 0);
        $socialhubPagination = $socialhubPgCollection->get();

        if (!count($socialhubPagination)) {
            return "No next page found.";
        }
        
        $URL = $socialhubPgCollection->first()->next_url;

        $socialHubFeedLib = new SocialhubFeedLib();
        $getSocialHubFeed = $socialHubFeedLib->getNextPage($URL);

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

        $socialhubPgCollection->update(['is_processed' => 1]);

        if (isset($socialHubFeedArray['paging'])) {

            $socialHubNextPage = $socialHubFeedArray['paging'];

            if (isset($socialHubNextPage['next'])) {
                $socialhubPagination = new SocialhubItemsPg();
                $socialhubPagination->next_url = $socialHubNextPage['next'];
                $socialhubPagination->save();
            }
        }

        $this->triggerToCheckSHItemsNextPage();
    }

    private function triggerToCheckSHItemsNextPage()
    {
        $socialhubPagination = SocialhubItemsPg::where('is_processed', 0)->get();

        if (!count($socialhubPagination)) {
            return "No next page found.";
        } else {
            $this->handle();
        }
    }
}
