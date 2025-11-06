<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(private MediaStorageService $storage){}
    public function index(Request $request)
    {
        $query = Media::query();

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        $media = $query->latest()->paginate(15);

        return view('media.index', compact('media'));
    }

    public function create()
    {
        return view('media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video,document',
            'file' => 'required|file|max:51200',
            'category' => 'nullable|string|max:100',
        ]);

        $fileData = $this->storage->upload($request->file('file'), $request->type);

        Media::create([
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $fileData['path'],
            'file_name' => $fileData['name'],
            'file_size' => $fileData['size'],
            'file_url'  => $fileData['url'],
        ]);

        return redirect()->route('media.create')->with('success', 'Média ajouté avec succès!');
    }

    public function images()
    {
        $images = Media::where('type', 'image')
            ->latest()
            ->paginate(12);

        return view('media.images', compact('images'));
    }

    public function videosAndDocuments()
    {
        $videos = Media::where('type', 'video')
            ->latest()
            ->paginate(6);

        $documents = Media::where('type', 'document')
            ->latest()
            ->paginate(10);

        return view('media.videos-documents', compact('videos', 'documents'));
    }

    public function edit($id)
    {
        $media = Media::findOrFail($id);
        return view('media.edit', compact('media'));
    }

    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);

        $media->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
        ]);

        return back()->with('success', 'Média mis à jour avec succès!');
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);

        $this->storage->delete($media->file_path);

        $media->delete();

        return redirect()->route('media.index')->with('success', 'Média supprimé avec succès!');
    }
}
