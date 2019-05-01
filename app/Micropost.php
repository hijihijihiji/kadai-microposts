<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function favorite_user()
    {
        return $this->belongsToMany(User::class, 'favorites', 'micropost_id', 'user_id')->withTimestamps();
    }
    //micropost_idのMicropostはuser_idのUserにお気に入りされている
    //$micropost->favorite_userでMicropostをお気に入りしているUserたちを取得
    
    // public function feed_favorites()
    // {
    //     $favorite_microposts_ids = $this->favorite_user()->pluck('micropost.id')->toArray();
    //     $favorite_microposts_ids[] = $this->id;
    //     return Micropost::whereIn('micropost_id', $favorite_microposts_ids);
        
    //     //お気に入りしている全てのマイクロポストIDを取得、配列に変換、
    //     //その配列に自マイクロポストのIDも追加
    //     //micropostsテーブルのmicropost_idカラムに入っている、
    //     //お気に入りしている全マイクロポストIDと自分のIDに関わるレコード？を取得
    //     //取得したレコードを返す
    // }
}
