<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouvelle réservation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="salle_id" class="block text-sm font-medium text-gray-700">Salle de réunion</label>
                            <select id="salle_id" name="salle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sélectionner une salle</option>
                                @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}" {{ old('salle_id') == $salle->id ? 'selected' : '' }}>
                                        {{ $salle->nom }} (Capacité: {{ $salle->capacite }} personnes, {{ $salle->surface }}m²)
                                    </option>
                                @endforeach
                            </select>
                            @error('salle_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="date" name="date" value="{{ old('date') }}" min="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="heure_debut" class="block text-sm font-medium text-gray-700">Heure de début</label>
                                <input type="time" id="heure_debut" name="heure_debut" value="{{ old('heure_debut') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('heure_debut')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="heure_fin" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                                <input type="time" id="heure_fin" name="heure_fin" value="{{ old('heure_fin') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('heure_fin')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Annuler</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Réserver
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
