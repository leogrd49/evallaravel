<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier une salle de réunion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('salles.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Retour à la liste des salles
                        </a>
                    </div>

                    <form method="POST" action="{{ route('salles.update', $salle) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom de la salle</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom', $salle->nom) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('nom')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="capacite" class="block text-sm font-medium text-gray-700">Capacité (nombre de personnes)</label>
                            <input type="number" name="capacite" id="capacite" value="{{ old('capacite', $salle->capacite) }}" required min="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('capacite')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="surface" class="block text-sm font-medium text-gray-700">Surface (m²)</label>
                            <input type="number" step="0.01" name="surface" id="surface" value="{{ old('surface', $salle->surface) }}" required min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @error('surface')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour la salle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
