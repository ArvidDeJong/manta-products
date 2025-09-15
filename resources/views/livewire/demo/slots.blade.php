<div class="space-y-4 p-6">
    <a href="{{ route('manta-product.demo.index') }}" class="text-sm underline">← Terug</a>
    <h1 class="text-2xl font-bold">Slots voor {{ $product->title }}</h1>
    <p class="text-sm text-gray-600">Periode: {{ $from->toDateString() }} — {{ $to->toDateString() }}</p>

    <div class="overflow-x-auto">
        <table class="min-w-full border text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Start</th>
                    <th class="px-3 py-2 text-left">Einde</th>
                    <th class="px-3 py-2 text-left">Used</th>
                    <th class="px-3 py-2 text-left">Capacity</th>
                    <th class="px-3 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slots as $s)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $s['start'] }}</td>
                        <td class="px-3 py-2">{{ $s['end'] }}</td>
                        <td class="px-3 py-2">{{ $s['used'] }}</td>
                        <td class="px-3 py-2">{{ $s['capacity'] }}</td>
                        <td class="px-3 py-2">
                            @if ($s['available'])
                                <span
                                    class="inline-block rounded bg-green-100 px-2 py-1 text-green-700">Beschikbaar</span>
                            @else
                                <span class="inline-block rounded bg-red-100 px-2 py-1 text-red-700">Vol</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">Geen slots in deze periode</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
