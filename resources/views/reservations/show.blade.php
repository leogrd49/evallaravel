<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la réservation') }}
            </h2>
            <div>
                @if(Auth::id() === $reservation->user_id || Auth::user()->role === 'administrateur')
                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                            Annuler la réservation
                        </button>
                    </form>
                @endif
                <a href="{{ url()->previous() }}" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Informations sur la réservation</h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Salle</p>
                                    <p class="mt-1">{{ $reservation->salle->nom }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Réservé par</p>
                                    <p class="mt-1">{{ $reservation->user->prenom }} {{ $reservation->user->nom }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Capacité de la salle</p>
                                    <p class="mt-1">{{ $reservation->salle->capacite }} personnes</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Surface</p>
                                    <p class="mt-1">{{ $reservation->salle->surface }} m²</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date</p>
                                    <p class="mt-1">{{ $reservation->heure_debut->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Heure de début</p>
                                    <p class="mt-1">{{ $reservation->heure_debut->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Heure de fin</p>
                                    <p class="mt-1">{{ $reservation->heure_fin->format('H:i') }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Durée</p>
                                <p class="mt-1">{{ $reservation->heure_debut->diff($reservation->heure_fin)->format('%H:%I') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Réservation effectuée le</p>
                                <p class="mt-1">{{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
