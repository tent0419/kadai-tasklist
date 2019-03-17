<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    public function index()
    {
        $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasklists()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
        }

        return view('welcome', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // postでmessages/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:191',
        ]);

        $request->user()->tasklists()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);

        return redirect('/tasks');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでmessages/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        $task = Task::find($id);

        if (\Auth::id() === $task->user_id) {
            return view('tasks.show', [
                'task' => $task,
            ]);
        }
        return redirect('/tasks');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでmessages/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        $task = Task::find($id);

        if (\Auth::id() === $task->user_id) {

            return view('tasks.edit', [
                'task' => $task,
            ]);
        }
        return redirect('/tasks');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // putまたはpatchでmessages/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:191',
        ]);

        $task = Task::find($id);
        if (\Auth::id() === $task->user_id) {
            $task->status = $request->status;    // 追加
            $task->content = $request->content;
            $task->save();
        }
        return redirect('/tasks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // deleteでmessages/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        $task = Task::find($id);

        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }
        
        return redirect('/tasks');
    }
}

