<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tweet;
use Auth;
use App\Models\User;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tweets = Tweet::getAllOrderByUpdated_at()
            ->where('parent_id', 0); // parent_id=0のもののみ取得
        return response()->view('tweet.index',compact('tweets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('tweet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'tweet' => 'required | max:191',
            'description' => 'required',
        ]);
        // バリデーション:エラー
        if ($validator->fails()) {
            return redirect()
              ->route('tweet.create')
              ->withInput()
              ->withErrors($validator);
        }
        // create()は最初から用意されている関数
        // 戻り値は挿入されたレコードの情報
        $parent_id = $request->input('parent_id', 0);
        $data = $request->merge(['user_id' => Auth::user()->id, 'parent_id' => $parent_id])->all();
        $result = Tweet::create($data);
        // ルーティング「todo.index」にリクエスト送信（一覧ページに移動）
        return redirect()->route('tweet.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tweet = Tweet::find($id);
        $tweets = Tweet::getAllOrderByUpdated_at()
            ->where('parent_id', $id);
        return response()->view('tweet.show', compact('tweet', 'tweets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tweet = Tweet::find($id);
        return response()->view('tweet.edit', compact('tweet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //バリデーション
        $validator = Validator::make($request->all(), [
            'tweet' => 'required | max:191',
            'description' => 'required',
        ]);
        //バリデーション:エラー
        if ($validator->fails()) {
            return redirect()
                ->route('tweet.edit', $id)
                ->withInput()
                ->withErrors($validator);
        }
        //データ更新処理
        $result = Tweet::find($id)->update($request->all());
        return redirect()->route('tweet.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = Tweet::find($id)->delete();
        return redirect()->route('tweet.index');
    }

    public function mydata()
    {
        // Userモデルに定義したリレーションを使用してデータを取得する．
        $tweets = User::query()
            ->find(Auth::user()->id)
            ->userTweets()
            ->orderBy('created_at','desc')
            ->get();
        return response()->view('tweet.index', compact('tweets'));
    } 

    public function timeline()
    {
        // フォローしているユーザを取得する
        $followings = User::find(Auth::id())->followings->pluck('id')->all();
        // 自分とフォローしている人が投稿したツイートを取得する
        $tweets = Tweet::query()
            ->where('user_id', Auth::id())
            ->orWhereIn('user_id', $followings)
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('tweet.index', compact('tweets'));
    }
}
