@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Modifier le média</h1>
            <a href="{{ route('media.index') }}" class="text-blue-500 hover:text-blue-700">
                ← Retour à la liste
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8">
                <h2 class="text-xl font-semibold mb-4">Aperçu du média</h2>
                
                <div class="mb-4">
                    @if($media->type == 'image')
                        <img src="{{ Storage::disk('s3')->temporaryUrl($media->file_path, now()->addMinutes(10)) }}" alt="{{ $media->title }}" class="w-full rounded-lg shadow-md">
                    @elseif($media->type == 'video')
                        <video controls class="w-full rounded-lg shadow-md">
                            <source src="{{ Storage::disk('s3')->temporaryUrl($media->file_path, now()->addMinutes(10)) }}" type="video/mp4">
                            Votre navigateur ne supporte pas la balise vidéo.
                        </video>
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 text-center">
                            <svg class="w-24 h-24 mx-auto text-red-500 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-gray-600 font-semibold">{{ $media->file_name }}</p>
                            <p class="text-gray-500 text-sm mt-2">Taille: {{ $media->formatted_size }}</p>
                            <a href="{{ Storage::disk('s3')->temporaryUrl($media->file_path, now()->addMinutes(10)) }}" target="_blank" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Télécharger le document
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 rounded p-4">
                    <h3 class="font-semibold mb-2">Informations</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">
                                @if($media->type == 'image')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Image</span>
                                @elseif($media->type == 'video')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded">Vidéo</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded">Document</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nom du fichier:</span>
                            <span class="font-medium">{{ $media->file_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Taille:</span>
                            <span class="font-medium">{{ $media->formatted_size }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ajouté le:</span>
                            <span class="font-medium">{{ $media->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Modifié le:</span>
                            <span class="font-medium">{{ $media->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8">
                <h2 class="text-xl font-semibold mb-4">Modifier les informations</h2>
                
                <form action="{{ route('media.update', $media->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                            Titre *
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title"
                            value="{{ old('title', $media->title) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                            required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            rows="5"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $media->description) }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                            Catégorie
                        </label>
                        <input 
                            type="text" 
                            name="category" 
                            id="category"
                            value="{{ old('category', $media->category) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Ex: Tutoriel, Présentation, etc.">
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Note: Vous ne pouvez pas modifier le fichier lui-même. Pour changer le fichier, veuillez supprimer ce média et en créer un nouveau.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button 
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('media.index') }}" class="text-gray-500 hover:text-gray-800">
                            Annuler
                        </a>
                    </div>
                </form>

                <hr class="my-8">

                <div class="bg-red-50 border border-red-200 rounded p-4">
                    <h3 class="text-red-800 font-semibold mb-2">Zone de danger</h3>
                    <p class="text-red-600 text-sm mb-4">La suppression est irréversible. Le fichier sera définitivement supprimé du serveur.</p>
                    <form action="{{ route('media.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer ce média? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection