<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideosManageController extends Controller
{
    /**
     * Llista de videos
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->can('videosManager')) {
            $videos = Video::all();
            return view('videos.manage.index', compact('videos'));
        }

        // Si l'usuari no te permisos, dona l'error 403
        abort(403);
    }

    /**
     * Mostrar un video en concret
     */
    public function show($id)
    {
        $video = Video::findOrFail($id);
        return view('videos.show', compact('video'));
    }

    /**
     * Guardar un video
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'series_id' => 'nullable|exists:series,id',
        ]);

        $video = new Video([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'user_id' => Auth::id(),
            'series_id' => $request->input('series_id'),
        ]);

        if ($video->save()) {
            return redirect()->route('videos.manage.index')->with('success', 'Video created successfully.');
        } else {
            return redirect()->route('videos.manage.create')->with('error', 'Failed to create video.');
        }
    }

    /**
     * Editar un video
     */
    public function edit($id)
    {
        $video = Video::findOrFail($id);
        $series = Series::all();
        return view('videos.manage.edit', compact('video', 'series'));
    }

    /**
     * Actualitzar el video
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'required|url',
            'series_id' => 'nullable|exists:series,id',
        ]);

        $video = Video::findOrFail($id);
        $video->update($request->all());

        return redirect()->route('videos.manage.index');
    }

    /**
     * Eliminar un video
     */
    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return redirect()->route('videos.manage.index');
    }

    /**
     * Show the form for creating a new video.
     */
    public function create()
    {
        $series = Series::all();
        return view('videos.manage.create', compact('series'));
    }

    public function testedBy($id)
    {
        $video = Video::findOrFail($id);
        $users = $video->testedByUsers;
        return response()->json($users);
    }
}
