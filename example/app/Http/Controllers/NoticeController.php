<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')
            ->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'notices' => Notice::query()
                ->when(Auth::check(), fn ($query) => $query->with('user'))
                ->paginate()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function show(Notice $notice)
    {
        if (Auth::check()) {
            $notice->load('user');
        }

        return response()->json([ 'notice' => $notice ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Notice::class);

        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $notice = Notice::make($request->only(['title', 'content']));
        Auth::user()->notices()->save($notice);

        return response()->json([
            'created' => true,
            'notice' => $notice
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notice $notice)
    {
        $this->authorize('update', $notice);

        $notice->update($request->only('title', 'content'));

        return response()->json([
            'notice' => $notice
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notice $notice)
    {
        $this->authorize('delete', $notice);

        $notice->delete();

        return response()->noContent();
    }
}
