<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function isLikedBy(?User $user): bool
    {
        return $user
            // idは、usersテーブルのid
            ? (bool)$this->likes->where('id', $user->id)->count()
            : false;
    }

    /**
     * アクセサ
     * $article->count_likesといった呼び出し方ができる
     *
     * @return int
     */
    public function getCountLikesAttribute(): int
    {
        // $this->likesにより、記事モデルからlikesテーブル経由で紐付いているユーザーモデルが、コレクション(配列を拡張したもの)で返ります。
        // コレクションではcountメソッドを使うことができるので、countメソッドを使ってコレクションの要素数
        //（この記事にいいねをしたユーザーの総数）を数えます
        return $this->likes->count();
    }
}
