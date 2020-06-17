<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * アクセサ
     * タグ名の先頭に、'#'を付けて返します。
     * @return string
     */
    public function getHashtagAttribute(): string
    {
        return '#' . $this->name;
    }
}
