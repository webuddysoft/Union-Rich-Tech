<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if( isset($location['location_name']))
                {{ __( $location['location_name'] . ' Shoppers') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" x-data="pageData" x-init="initApp()">
                    <div class="flex flex-col sm:flex-row justify-between border-b border-gray-200 p-6 item-center">
                        <div class="flex flex-col sm:flex-row">
                            <div class="px-2">Shopper Limit: <span class="badge bg-secondary font-bold" x-text="shopperLimit"></span></div>
                            <div class="px-2">Active: <span class="text-green-400 font-bold" x-text="activeCount"></span></div>
                            <div class="px-2">Pending: <span class="text-blue-400 font-bold" x-text="pendingCount"></span></div>
                            <div class="px-2">Completed: <span class="text-gray-400 font-bold" x-text="completedCount"></span></div>
                        </div>
                        <div class="">
                            Last updated: <span x-text="refreshTimeDiff"></span> seconds ago
                        </div>
                    </div>

                    <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap mt-6">
                        <thead>
                            <tr>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Shopper Name
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Check-In
                                </th>
                                <th>
                                    Check-Out
                                </th>
                            </tr>
                            <tr>
                                <th class="py-3 px-2">
                                    <select id="filter_status" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="filters.status">
                                        <option value="all">All</option>
                                        @foreach ($statuses as $status)
                                        <option value="{{ strtolower($status->name) }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="py-3 px-2">
                                    <input id="filter_name" type="text"  class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="filters.name"/>
                                </th>
                                <th class="py-3 px-2">
                                    <input id="filter_email" type="text"  class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="filters.email" />
                                </th>
                                <th class="py-3 px-2">
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        {{-- <template x-for="shoppers in getFilteredShoppers" :key="shoppers"> --}}
                            <template x-for="shopper in getFilteredShoppers" :key="shopper.id">
                                <tr class="text-center">
                                    <td class="border px-6 py-4">
                                        <template x-if="shopper.status.name == 'Active'">
                                            <div class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase transition">
                                                Active
                                            </div>
                                        </template>

                                        <template x-if="shopper.status.name == 'Pending'">
                                            <div class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase transition">
                                                Pending
                                            </div>
                                        </template>

                                        <template x-if="shopper.status.name == 'Completed'">
                                            <div class="inline-flex items-center px-4 py-2 bg-yellow-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase transition">
                                                Completed
                                            </div>
                                        </template>
                                    </td>
                                    <td class="border px-6 py-4" x-text="shopper.first_name + ' ' + shopper.last_name"></td>
                                    <td class="border px-6 py-4" x-text="shopper.email"></td>
                                    <td class="border px-6 py-4" x-text="shopper.check_in"></td>
                                    <td class="border px-6 py-4">
                                        <template x-if="shopper.status.name == 'Active'">
                                            <button x-bind:shopper="shopper.id" x-on:click="checkoutShopper(shopper.id)" 
                                            class="checkout-btn inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" x-bind:disabled="processing.indexOf(shopper.id) > -1">
                                                <template x-if="processing.indexOf(shopper.id) > -1">
                                                    <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </template>
                                                Check-out
                                            </button>
                                        </template>
                                        <template x-if="shopper.status.name != 'Active'">
                                            <span  x-text="shopper.check_out"></span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pageData', () => ({
                shopperLimit: {{ $location["shopper_limit"] }},
                activeCount: {{ isset($shoppers['active']) ? count($shoppers['active']) : 0 }},
                pendingCount: {{ isset($shoppers['pending']) ? count($shoppers['pending']) : 0 }},
                completedCount: {{ isset($shoppers['completed']) ? count($shoppers['completed']) : 0 }},
                filters: {
                    status: 'all',
                    name: '',
                    email: ''
                },
                processing: [],
                shoppers: {
                    active: {!! json_encode(array_values($shoppers['active'])) !!},
                    pending: {!! json_encode(array_values($shoppers['pending'])) !!},
                    completed: {!! json_encode(array_values($shoppers['completed'])) !!}
                },
                initApp() {
                    setInterval(() => {
                        if (!this.isRefreshing) {
                            this.calcTimeDiff()
                        }
                        if (this.refreshTimeDiff > 0 && this.refreshTimeDiff % this.refreshInterval == 0) {
                            this.reloadData()
                        } 
                    }, 1000);
                },
                getFilteredShoppers() {
                    let arr = []
                    if (this.filters.status == "all") {
                        arr = this.shoppers.active.concat(this.shoppers.pending, this.shoppers.completed)
                    } else {
                        arr = this.shoppers[this.filters.status]
                    }
                    if (this.filters.name) {
                        arr = arr.filter(item => {
                            return (item.first_name + ' ' + item.last_name).toLowerCase().indexOf(this.filters.name.toLowerCase()) > -1
                        })
                    }
                    if (this.filters.email) {
                        arr = arr.filter(item => {
                            return item.email.toLowerCase().indexOf(this.filters.email.toLowerCase()) > -1
                        })
                    }
                    return arr
                },
                checkoutShopper(shopperId) {
                    this.processing.push(shopperId)
                    fetch('{{ route('store.location.checkout', ['storeUuid' => $storeUuid, 'locationUuid' => $location['uuid']]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _token: '{{ csrf_token() }}',
                            shopper_id: shopperId
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            this.shoppers = res.shoppers
                            this.activeCount = res.activeCount
                            this.pendingCount = res.pendingCount
                            this.completedCount = res.completedCount

                            this.refreshTime = new Date()
                        }
                    })
                    .catch(e => {
                        
                    })
                    .finally(() => {
                        const _idx = this.processing.indexOf(shopperId)
                        if (_idx > -1) {
                            this.processing.splice(_idx, 1)
                        }
                    })
                },
                isRefreshing: false,
                refreshInterval: 10,
                refreshTime: new Date(),
                refreshTimeDiff: 0,
                reloadData() {
                    if (!this.isRefreshing) {
                        this.isRefreshing = true
                        fetch('{{ route("store.location.queue", ['storeUuid' => $storeUuid, 'locationUuid' => $location['uuid']]) }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') {
                                this.refreshTime = new Date()
                                this.shoppers = res.shoppers
                                this.activeCount = res.activeCount
                                this.pendingCount = res.pendingCount
                                this.completedCount = res.completedCount
                            }
                        })
                        .catch(e => {
                           
                        })
                        .finally(() => {
                            this.isRefreshing = false
                        })
                    }
                },
                calcTimeDiff() {
                    const diff = new Date().getTime() - this.refreshTime.getTime()
                    this.refreshTimeDiff = Math.round(diff / 1000)
                }
            }))
        })
    </script>
</x-app-layout>
