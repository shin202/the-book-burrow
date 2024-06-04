<div
    class="py-4 px-6 max-w-screen-md bg-surface-0 rounded-lg shadow-lg flex flex-col justify-center items-center gap-2">
    <h1 class="text-2xl">Data Export Completed</h1>
    <p class="text-gray-600 text-center">
        Your data export request has been completed. You can now download the file from the link below.
        <span class="block text-sm text-gray-500 mt-2">
            (Note: The link will expire in 5 minutes)
        </span>
    </p>
    <div class="flex gap-4 mt-4">
        <a href="{{$downloadUrl}}" target="_blank">
            <button
                class="outline-none px-4 py-2 rounded border-4 border-surface-800 bg-surface-800 text-white hover:text-surface-900 relative before:absolute before:inset-0 before:bg-surface-0 before:scale-x-0 before:origin-left before:transition-transform before:duration-300 before:ease-out hover:before:scale-x-100">
                <span class="relative">Download now</span>
            </button>
        </a>
    </div>
</div>
