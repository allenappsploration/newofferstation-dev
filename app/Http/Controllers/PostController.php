<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\SocialhubItems;
use App\Http\Requests;
use App\Models\PostImg;

class PostController extends Controller
{
    const DATE_FORMAT = 'Y-m-d';
    const LIMIT_RECORD = 10;

    public function creation() {
        $this->convertToPosts();

        return;
    }

    private function convertToPosts() {
        $lastRecord = Post::orderBy('created_at', 'desc')->first();
        if ($lastRecord !== null) {
            $nextItems = SocialhubItems::where('id', '>', $lastRecord->sh_items_id)->limit(PostController::LIMIT_RECORD)->get();
        }
        else {
            $nextItems = SocialhubItems::orderBy('created_at')->limit(PostController::LIMIT_RECORD)->get();
        }

        for ($i = 0; $i < $nextItems->count(); $i++) {
            $curItem = $nextItems[$i];

            $newPost = new Post();
            $newPost->post_types_id = mt_rand(1, 5);
            $newPost->sh_items_id = $curItem->id;
            $newPost->brand = $curItem->name;
            $newPost->title = substr($curItem->raw_body, 0, 64);
            $newPost->desc = $curItem->raw_body;
            $newPost->date_type = POST::DATE_TYPE[mt_rand(0, 3)];
            $newPost->start = date(PostController::DATE_FORMAT, strtotime('2016-08-01'));
            $newPost->end = date(PostController::DATE_FORMAT, strtotime('+4'));
            $newPost->publish_start = date(PostController::DATE_FORMAT, strtotime('2016-08-01'));
            $newPost->publish_end = date(PostController::DATE_FORMAT, strtotime('+5'));
            $newPost->product_url = $curItem->url;
            $newPost->keywords = '';
            $newPost->approval_status = 'publish';
            $newPost->save();
            
            $newImg = new PostImg();
            $newImg->posts_id = $newPost->id;
            $newImg->ratio = $curItem->img_height / $curItem->img_width;
            $newImg->path = $curItem->img;
            $newImg->save();
        }
    }

    protected function d($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}
