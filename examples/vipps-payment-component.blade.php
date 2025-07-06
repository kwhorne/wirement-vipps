<div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Vipps Payment</h2>
        
        @if($error)
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $error }}
            </div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Amount</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">{{ config('vipps.currency', 'NOK') }}</span>
                    </div>
                    <input 
                        type="number" 
                        wire:model="amount" 
                        class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        min="1"
                        step="0.01"
                    >
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <input 
                    type="text" 
                    wire:model="description" 
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Payment description"
                >
            </div>

            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Order ID: {{ $orderId }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($status === 'pending') bg-gray-100 text-gray-800
                    @elseif($status === 'created') bg-blue-100 text-blue-800
                    @elseif($status === 'authorized') bg-yellow-100 text-yellow-800
                    @elseif($status === 'captured') bg-green-100 text-green-800
                    @elseif($status === 'error') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($status) }}
                </span>
            </div>

            <div class="flex space-x-3">
                <button 
                    wire:click="createPayment" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    wire:loading.attr="disabled"
                    wire:target="createPayment"
                >
                    <span wire:loading.remove wire:target="createPayment">Pay with Vipps</span>
                    <span wire:loading wire:target="createPayment">Creating payment...</span>
                </button>

                @if($status !== 'pending')
                    <button 
                        wire:click="checkPaymentStatus" 
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        wire:loading.attr="disabled"
                        wire:target="checkPaymentStatus"
                    >
                        <span wire:loading.remove wire:target="checkPaymentStatus">Check Status</span>
                        <span wire:loading wire:target="checkPaymentStatus">Checking...</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
