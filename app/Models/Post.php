<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const DATE_TYPE = ['s_n', 'wsl', 'end', 'none'];
    
    protected $casts = [
        'start' => 'timestamp',
        'end' => 'timestamp'
    ];
    
    public function postImg()
    {
        return $this->hasMany('App\Models\PostImg', 'posts_id');
    }
}
