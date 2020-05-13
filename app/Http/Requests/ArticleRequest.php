<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストの権限を持っているかを判断する
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:500',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
        ];
    }
}
