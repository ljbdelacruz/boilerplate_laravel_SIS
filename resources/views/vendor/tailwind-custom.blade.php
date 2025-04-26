<nav class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 space-y-2 sm:space-y-0 text-sm">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-1 text-gray-400 rounded bg-gray-100 w-full sm:w-auto text-center">&larr;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="pagination-link px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-100 ease-in w-full sm:w-auto text-center">&larr;</a>
    @endif

    {{-- Page Numbers --}}
    <span class="px-3 py-1 w-full sm:w-auto text-center">{{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="pagination-link px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-100 ease-in w-full sm:w-auto text-center">&rarr;</a>
    @else
        <span class="px-3 py-1 text-gray-400 rounded bg-gray-100 w-full sm:w-auto text-center">&rarr;</span>
    @endif
</nav>