<?php

namespace App\Http\Controllers;

use App\Article;
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
        return view('articles.create');
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
        return redirect()->route('articles.index');
    }

    /**
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Article $article)
    {
        return view('articles.edit', ['article' => $article]);
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
}
