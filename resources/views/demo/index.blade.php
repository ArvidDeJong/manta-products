<div class="p-6 space-y-4">
  <h1 class="text-2xl font-bold">Manta Products – Demo</h1>
  <p class="text-sm text-gray-600">Kies een product om slots te genereren (7 dagen, op basis van opening hours & exceptions).</p>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($products as $p)
      <div class="rounded-xl border p-4">
        <div class="font-semibold">{{ $p->title }}</div>
        <div class="text-xs text-gray-500">Type: {{ $p->product_type }} · Unit: {{ $p->unit_type }} · Block: {{ $p->block_size }} {{ $p->time_unit }}</div>
        <a href="{{ route('manta-products.demo.slots', $p) }}" class="inline-block mt-3 px-3 py-2 rounded-lg border hover:bg-gray-50">Bekijk slots</a>
      </div>
    @endforeach
  </div>
</div>
