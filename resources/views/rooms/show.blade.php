<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de la salle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('rooms.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Retour à la liste des salles
                        </a>
                    </div>

                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $room->name }}
                                @if($room->is_active)
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disponible
                                    </span>
                                @else
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Indisponible
                                    </span>
                                @endif
                            </h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Capacité
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $room->capacity }} personnes
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Équipements
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $room->equipment ?? 'Aucun équipement spécifié' }}
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Description
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $room->description ?? 'Aucune description disponible' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div class="mt-6 flex space-x-3">
                            <a href="{{ route('rooms.edit', $room) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Modifier
                            </a>
                            
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Prochaines réservations -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Prochaines réservations</h3>
                        
                        @if($room->reservations && $room->reservations->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Heure de début</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Heure de fin</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Réservé par</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($room->reservations->sortBy('start_time') as $reservation)
                                            <tr>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($reservation->start_time)->format('d/m/Y') }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $reservation->user->prenom }} {{ $reservation->user->nom }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 italic">Aucune réservation pour cette salle.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
