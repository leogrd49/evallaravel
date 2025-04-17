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
                <div class="mb-6">
                    <form id="periodSelectorForm" class="flex items-center space-x-4">
                        <div>
                            <label for="periodType" class="block text-sm font-medium text-gray-700">Afficher par:</label>
                            <select id="periodType" name="periodType" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="week" {{ $periodType == 'week' ? 'selected' : '' }}>Semaine</option>
                                <option value="month" {{ $periodType == 'month' ? 'selected' : '' }}>Mois</option>
                            </select>
                        </div>
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700">Date de début:</label>
                            <input type="date" id="startDate" name="startDate" value="{{ $startDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="pt-5">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Actualiser
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
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacité</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de réservations</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heures réservées</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux d'occupation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="statsTableBody">
                                @foreach($roomStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['capacity'] }}</td>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser le graphique avec les données pré-chargées
            const chartData = @json($chartData);
            updateChart(chartData);

            // Ajouter l'événement de soumission du formulaire
            document.getElementById('periodSelectorForm').addEventListener('submit', function(e) {
                e.preventDefault();
                loadData();
            });
        });

        function loadData() {
            const periodType = document.getElementById('periodType').value;
            const startDate = document.getElementById('startDate').value;

            // Appel AJAX pour récupérer les données
            fetch(`/admin/stats?periodType=${periodType}&startDate=${startDate}`)
                .then(response => response.json())
                .then(data => {
                    updateChart(data.chartData);
                    updateStatsTable(data.roomStats);
                    updateTopUsersTable(data.topUsers);
                })
                .catch(error => console.error('Erreur lors du chargement des données:', error));
        }

        let occupationChart;

        function updateChart(chartData) {
            const ctx = document.getElementById('occupationChart').getContext('2d');

            // Détruire le graphique existant s'il existe
            if (occupationChart) {
                occupationChart.destroy();
            }

            // Créer un nouveau graphique
            occupationChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Taux d\'occupation (%)',
                        data: chartData.data,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateStatsTable(roomStats) {
            const tableBody = document.getElementById('statsTableBody');
            tableBody.innerHTML = '';

            roomStats.forEach(stat => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${stat.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${stat.capacity}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${stat.reservationCount}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${stat.hoursReserved}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${stat.occupancyRate}%</td>
                `;
                tableBody.appendChild(row);
            });
        }

        function updateTopUsersTable(topUsers) {
            const tableBody = document.getElementById('topUsersTableBody');
            tableBody.innerHTML = '';

            topUsers.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.reservationCount}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.totalHours}</td>
                `;
                tableBody.appendChild(row);
            });
        }
    </script>
    @endpush
</x-app-layout>
