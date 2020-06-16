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
            // 半角スペースが含まれないのと、/がないことをチェックする
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u',
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
            'tags' => 'タグ',
        ];
    }


    /**
     * passedValidationメソッドは、
     * フォームリクエストのバリデーションが成功した後に自動的に呼ばれるメソッドです。
     */
    public function passedValidation()
    {
        // json_decodeで連想配列形式に変換したものを、collect関数を使ってコレクションに変換している
        $this->tags = collect(json_decode($this->tags))
            ->slice(0, 5)
            ->map(function ($requestTag) {
                return $requestTag->text;
            });
    }
}
