<div
    class="py-4 px-6 max-w-screen-md bg-surface-0 rounded-lg shadow-lg flex flex-col justify-center items-center gap-2">
    <h1 class="text-2xl">Received a new order from user: {{ $order->user->username }}</h1>
    <div class="flex flex-col justify-center items-center">
        <span class="text-gray-600 text-sm">Order number: #{{ $order->order_number }}</span>
        <p class="text-gray-600 text-center">You have received a new order from a customer. Please check the
            order details
            and confirm the order.</p>
    </div>
    <div class="flex gap-4 mt-4">
        <a href="{{route('dashboard.orders.show', ['orderNumber' => $order->order_number])}}">
            <button
                class="outline-none px-4 py-2 rounded border-4 border-surface-800 bg-surface-800 text-white hover:text-surface-900 relative before:absolute before:inset-0 before:bg-surface-0 before:scale-x-0 before:origin-left before:transition-transform before:duration-300 before:ease-out hover:before:scale-x-100">
                <span class="relative">View Order</span>
            </button>
        </a>
    </div>
</div>
