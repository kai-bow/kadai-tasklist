<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     // getでTasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        
        // タスク一覧を取得
        if(\Auth::check()){
            $user = \Auth::user();
            $tasks = $user->tasks()->get();
    
            // タスク一覧ビューでそれを表示
            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
        }else{
            $tasks=[];
            return view('tasks.index', [
                    'tasks' => $tasks
                ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        if(\Auth::check()){
            $task = new Task;
    
            // タスク作成ビューを表示
            return view('tasks.create', [
                'task' => $task,
            ]);
        }else{
            return redirect('/');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     // postでtasks/にアクセスされた場合の「新規登録処理
    public function store(Request $request)
    {
        
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        // タスクを作成
        $task = new Task;
        $task->user_id = \Auth::id();
        $task->content = $request->content;
        $task->status = $request->status;    // 追加
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function show($id)
    {
        if(\Auth::check()){
             // idの値でタスクを検索して取得
            $task = Task::findOrFail($id);
    
            // タスク編集ビューでそれを表示
            if (\Auth::id() === $task->user_id){
                return view('tasks.show', [
                    'task' => $task,
                ]);
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        if(\Auth::check()){
             // idの値でタスクを検索して取得
            $task = Task::findOrFail($id);
    
            // タスク編集ビューでそれを表示
            if (\Auth::id() === $task->user_id){
                return view('tasks.edit', [
                    'task' => $task,
                ]);
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        if(\Auth::check()){
            // バリデーション
            $request->validate([
                'content' => 'required|max:255',
                'status' => 'required|max:10',
            ]);
            
             // idの値でタスクを検索して取得
            $task = Task::findOrFail($id);
            // タスクを更新
            if (\Auth::id() === $task->user_id){
                $task->status = $request->status;    // 追加
                $task->content = $request->content;
                $task->save();
            }else{
                return redirect('/');
            }
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        if(\Auth::check()){
            // idの値でタスクを検索して取得
            $task = Task::findOrFail($id);
           // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
            if (\Auth::id() === $task->user_id) {
                $task->delete();
            }else{
                return redirect('/');
            }
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
