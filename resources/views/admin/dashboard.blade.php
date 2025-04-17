<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord administratif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Taux d'occupation des salles</h3>

                <!-- Sélecteur de période -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="weekType" class="block text-sm font-medium text-gray-700 mb-1">Semaine:</label>
                                <select id="weekType" name="weekType" class="w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                                    <option value="current" {{ request('weekType', 'current') == 'current' ? 'selected' : '' }}>Semaine courante</option>
                                    <option value="previous" {{ request('weekType') == 'previous' ? 'selected' : '' }}>Semaine précédente</option>
                                    <option value="next" {{ request('weekType') == 'next' ? 'selected' : '' }}>Semaine suivante</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <span class="text-sm text-gray-600">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>



                <!-- Tableau récapitulatif -->
                <div class="mb-6">
                    <h4 class="text-md font-medium mb-2">Taux d'occupation par salle</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salle</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de réservations</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures réservées</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux d'occupation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="statsTableBody">
                                @foreach($roomStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['reservationCount'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['hoursReserved'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['occupancyRate'] }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top utilisateurs -->
                <div>
                    <h4 class="text-md font-medium mb-2">Top 5 utilisateurs</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de réservations</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures totales</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="topUsersTableBody">
                                @foreach($topUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user['reservationCount'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user['totalHours'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
