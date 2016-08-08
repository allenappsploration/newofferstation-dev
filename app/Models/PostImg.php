<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImg extends Model
{
    protected $table = "post_imgs";
    
    public function posts()
    {
        return $this->belongsTo('App\Models\Post', 'posts_id');
    }
}
