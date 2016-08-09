<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Post;
use App\Models\PostImg;
use App\Models\SocialhubItems;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConvertSHItemsToPosts extends Job implements ShouldQueue
{
    const DATE_FORMAT = 'Y-m-d';
    const LIMIT_RECORD = 50;

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
        $this->convertToPosts();
    }

    private function convertToPosts() {
        $lastRecord = Post::orderBy('created_at', 'desc')->first();

        if ($lastRecord !== null) {
            $nextItems = SocialhubItems::where('id', '>', $lastRecord->sh_items_id)->limit(ConvertSHItemsToPosts::LIMIT_RECORD)->get();
        }
        else {
            $nextItems = SocialhubItems::orderBy('created_at')->limit(ConvertSHItemsToPosts::LIMIT_RECORD)->get();
        }

        for ($i = 0; $i < $nextItems->count(); $i++) {
            $curItem = $nextItems[$i];

            $newPost = new Post();
            $newPost->post_types_id = mt_rand(1, 5);
            $newPost->sh_items_id = $curItem->id;
            $newPost->brand = $curItem->name;
            $newPost->title = substr($curItem->raw_body, 0, 64);

            $newPost->desc = $curItem->raw_body;
            $newPost->date_type = Post::DATE_TYPE[mt_rand(0, 3)];
            $newPost->start = date(ConvertSHItemsToPosts::DATE_FORMAT, strtotime('2016-08-01'));
            $newPost->end = date(ConvertSHItemsToPosts::DATE_FORMAT, strtotime('+4'));
            $newPost->publish_start = date(ConvertSHItemsToPosts::DATE_FORMAT, strtotime('2016-08-01'));
            $newPost->publish_end = date(ConvertSHItemsToPosts::DATE_FORMAT, strtotime('+5'));
            $newPost->product_url = $curItem->url;
            $newPost->keywords = '';
            $newPost->approval_status = 'publish';
            $newPost->save();

            if (isset($curItem->img)) {
                $newImg = new PostImg();
                $newImg->posts_id = $newPost->id;
                $newImg->ratio = $curItem->img_height / $curItem->img_width;
                $newImg->path = $curItem->img;
                $newImg->save();
            }
        }
    }
}
