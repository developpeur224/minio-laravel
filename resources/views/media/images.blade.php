{{-- resources/views/media/images.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Galerie d'images</h1>
        <a href="{{ route('media.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Ajouter une image
        </a>
    </div>

    @if($images->isEmpty())
        <div class="bg-gray-100 border border-gray-300 rounded-lg p-8 text-center">
            <p class="text-gray-600 text-lg">Aucune image disponible pour le moment.</p>
            <a href="{{ route('media.create') }}" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                Ajouter votre première image
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($images as $image)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="aspect-square bg-gray-200 overflow-hidden">
                        <img 
                            src="{{ Storage::disk('s3')->temporaryUrl($image->file_path, now()->addMinutes(10)) }}" 
                            alt="{{ $image->title }}"
                            class="w-full h-full object-cover hover:scale-110 transition-transform duration-300 cursor-pointer"
                            onclick="openModal('{{ Storage::disk('s3')->temporaryUrl($image->file_path, now()->addMinutes(10)) }}', '{{ $image->title }}')">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2 truncate">{{ $image->title }}</h3>
                        @if($image->description)
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $image->description }}</p>
                        @endif
                        @if($image->category)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                {{ $image->category }}
                            </span>
                        @endif
                        <div class="mt-3 text-xs text-gray-500">
                            {{ $image->created_at->format('d/m/Y') }}
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            <a href="{{ route('media.edit', $image->id) }}" class="text-blue-500 hover:text-blue-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('media.destroy', $image->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette image?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $images->links() }}
        </div>
    @endif
</div>

<!-- Modal pour afficher l'image en grand -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeModal()">
    <div class="max-w-7xl max-h-full">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-screen object-contain">
        <p id="modalTitle" class="text-white text-center mt-4 text-lg"></p>
    </div>
</div>

<script>
function openModal(src, title) {
    document.getElementById('modalImage').src = src;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>
@endsection