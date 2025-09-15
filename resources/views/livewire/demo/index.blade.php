<div class="space-y-4 p-6">
    <h1 class="text-2xl font-bold">Manta Products – Demo</h1>
    <p class="text-sm text-gray-600">Kies een product om slots te genereren (7 dagen, op basis van opening hours &
        exceptions).</p>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach ($products as $p)
            <div class="rounded-xl border p-4">
                <div class="font-semibold">{{ $p->title }}</div>
                <div class="text-xs text-gray-500">Type: {{ $p->product_type }} · Unit: {{ $p->unit_type }} · Block:
                    {{ $p->block_size }} {{ $p->time_unit }}</div>
                <a href="{{ route('manta-product.demo.slots', $p) }}"
                    class="mt-3 inline-block rounded-lg border px-3 py-2 hover:bg-gray-50">Bekijk slots</a>
            </div>
        @endforeach
    </div>
</div>
