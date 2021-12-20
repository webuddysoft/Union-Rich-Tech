<x-guest-layout>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-lg font-bold text-center">Welcome To {{ $location['location_name'] }}</h1>
            </div>
        </header>
        
        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg shadow-xl" x-data="pageData" x-init="initApp()" >
                    <div class="flex flex-col sm:flex-row justify-between border-b border-gray-200 p-6">
                        <div class="px-2">Shopper Limit: <span class="badge bg-secondary font-bold" x-text="shopperLimit"></span></div>
                        <div class="px-2">Active: <span class="text-green-400 font-bold" x-text="activeCount"></span></div>
                        <div class="px-2">Pending: <span class="text-blue-400 font-bold" x-text="pendingCount"></span></div>
                    </div>
                    
                    <div class="border-b border-gray-200 px-6 py-2">
                        <h2 class="font-bold text-lg text-left">Next Shoppers</h2>
                        <div class="flex flex-col sm:flex-row">
                            <template x-for="shopper in lastShoppers" :key="shopper.id">
                                <span x-text="shopper.first_name + ' ' +  shopper.last_name" class="mr-5"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex flex-col bg-gray-200 bg-opacity-25 p-6">
                        <h2 class="font-bold text-lg text-center mb-3">Check-in information</h2>
                        <form x-data="{ firstname: '', lastname: '', email: '' }" method="post" action="{{ route('public.checkin', ['location' => $location['uuid']]) }}">
                            @csrf
                            <div class="mb-4 grid grid-cols-1 sm:grid-cols-3 items-center">
                                <label class="block text-gray-700 text-sm font-bold mb-1 sm:mb-0" for="firstname">
                                    First Name
                                </label>
                                <div class="col-span-2">
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="firstname" name="first_name" type="text" required placeholder="First Name" x-model="firstname">
                                </div>
                            </div>
                            <div class="mb-4 grid grid-cols-1 sm:grid-cols-3 items-center">
                                <label class="block text-gray-700 text-sm font-bold mb-1 sm:mb-0" for="lastname">
                                    Last Name
                                </label>
                                <div class="col-span-2">
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="lastname" name="last_name" type="text" required placeholder="Last Name" x-model="lastname">
                                </div>
                            </div>
                            <div class="mb-4 grid grid-cols-1 sm:grid-cols-3 items-center">
                                <label class="block text-gray-700 text-sm font-bold mb-1 sm:mb-0" for="email">
                                    Email Address
                                </label>
                                <div class="col-span-2">
                                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" required placeholder="Email Address" x-model="email">
                                </div>
                            </div>
                            @if ($errors->any())
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-4">
                                <button class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition" type="submit">
                                    Enter Store
                                </button>
                                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" type="button" x-on:click=" firstname=''; lastname=''; email=''; ">
                                    Clear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pageData', () => ({
                shopperLimit: {{ $location["shopper_limit"] }},
                activeCount: {{ $activeCount }},
                pendingCount: {{ $pendingCount }},
                lastShoppers: {!! json_encode(array_values($lastShoppers->toArray())) !!},
                refreshInterval: 10,
                initApp() {
                    setInterval(() => {
                        this.reloadData()
                    }, this.refreshInterval * 1000);
                },
                reloadData() {
                    fetch('{{ route("public.location", ['location' => $location['uuid']]) }}', {
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
                    })
                }
            }))
        })
    </script>
</x-guest-layout>
