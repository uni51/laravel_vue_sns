<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * ArticleController constructor.
     * Articleポリシーをリソースコントローラーに適用する
     */
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }

    /**
     * @param ArticleRequest $request
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();

        // $request->tagsの内容は、前パートでフォームリクエスト(ArticleFormRequest)に追加した
        // passedValidationメソッドによって、コレクションになっています。
        // そのため、コレクションメソッドであるeachメソッドを使うことができます。
        $request->tags->each(function ($tagName) use ($article) { // use ($article)とあるのは、クロージャの中の処理で変数$articleを使うためです
            // firstOrCreateメソッドは、引数として渡した「カラム名と値のペア」を持つレコードがテーブルに存在するかどうかを探し、
            // もし存在すればそのモデルを返します。
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            // 記事とタグの紐付け(article_tagテーブルへのレコードの保存)が行われます。
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    /**
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Article $article)
    {
        $tagNames = $article->tags->map(function ($tag) {
            // Vue Tags Inputでは、タグ名に対し、以下のようにtextというキーが付いている必要があります。
            return ['text' => $tag->name];
        });

        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    /**
     * @param ArticleRequest $request
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ArticleRequest $request, Article $article)
    {
        // モデルのfillメソッドの戻り値はそのモデル自身なので、そのままsaveメソッドを繋げて使うことができます。
        $article->fill($request->all())->save();

        // 更新対象の記事とタグの紐付けをいったん全削除
        $article->tags()->detach();
        // $request->tagsの内容は、前パートでフォームリクエスト(ArticleFormRequest)に追加した
        // passedValidationメソッドによって、コレクションになっています。
        // そのため、コレクションメソッドであるeachメソッドを使うことができます。
        $request->tags->each(function ($tagName) use ($article) { // use ($article)とあるのは、クロージャの中の処理で変数$articleを使うためです
            // firstOrCreateメソッドは、引数として渡した「カラム名と値のペア」を持つレコードがテーブルに存在するかどうかを探し、
            // もし存在すればそのモデルを返します。
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            // 記事とタグの紐付け(article_tagテーブルへのレコードの保存)が行われます。
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    /**
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    /**
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    /**
     * いいね機能
     * @param Request $request
     * @param Article $article
     * @return array
     */
    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        // JSON形式に変換してレスポンスされる
        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    /**
     * いいね解除機能
     * @param Request $request
     * @param Article $article
     * @return array
     */
    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
}
