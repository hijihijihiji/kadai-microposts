<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
        //user_idのUserはfollow_idのUserをフォローしている
        //$user->followingsで$userがフォローしているUserたちを取得
    }
        //favoriteの場合は
        //user_idのUserはmicropost_idのMicropostをお気に入りしている
        //$user->fovaritesで$userがお気に入りしているMicropostたちを取得
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
        //フォローしている全てのユーザIDを取得、配列に変換、
        //その配列に自分のIDも追加
        //micropostsテーブルのuser_idカラムに入っている、
        //フォローしている全ユーザIDと自分のIDに関わるレコード？を取得
        //取得したレコードを返す
        
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    //user_idのUserはmicropost_idのMicropostをお気に入りしている
    //$user->fovaritesで$userがお気に入りしているMicropostたちを取得
    
    public function favorite($micropostId)
    {
        $exist = $this->now_favorite($micropostId);
        //今回お気に入りしたポストが既にお気に入り登録済みなら
        //$exist = ture,
        //まだなら
        //$exist = false
        //自分の投稿したmicropostかどうかの判断は不要のはず
        
        if ($exist) {
            return false;
        }else {
            $this->favorites()->attach($micropostId);
            return true;
        }
        //$existにわざわざ代入しなくても、
        //if ($this->fovarite()->where('micropost_id', $micropostId)->exists())
        //で良いのでは？長すぎ？
    }
    
    public function unfavorite($micropostId)
    {
        $exist = $this->now_favorite($micropostId);
        
        if ($exist) {
            $this->favorites()->detach($micropostId);
            return true;
        }else {
            return false;
        }
    }
    
    public function now_favorite($micropostId)
    {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
        //$userが既にお気に入り登録しているMicropostレコードにおいて、
        //カラムmicropost_idが、今回お気に入りしようとしているMicropostの
        //micropostIdと同じであればtrue
    }
    //テーブルmicropostsの中に、カラムmicropost_idがないからおかしい？
    //でもfollowの時のuserテーブルにはfollow_idなかったし多分大丈夫
    
    //     public function feed_favorites()
    // {
    //     $favorite_user_ids = $this->favorites()->pluck('users.id')->toArray();
    //     $favorite_microposts_ids[] = $this->id;
    //     return Micropost::whereIn('user_id', $follow_user_ids);
        
    //     //お気に入りしている全てのユーザIDを取得、配列に変換、
    //     //その配列に自分のIDも追加
    //     //micropostsテーブルのuser_idカラムに入っている、
    //     //お気に入りしている全マイクロポストIDと自分のIDに関わるレコード？を取得
    //     //取得したレコードを返す
        
        //フォローしている全てのユーザIDを取得、配列に変換、
        //その配列に自分のIDも追加
        //micropostsテーブルのuser_idカラムに入っている、
        //フォローしている全ユーザIDと自分のIDに関わるレコード？を取得
        //取得したレコードを返す
        
    }
    