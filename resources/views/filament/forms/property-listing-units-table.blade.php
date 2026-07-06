@php
    $rows = $rows ?? [];
@endphp

@if(count($rows) === 0)
    <p class="text-sm text-gray-500 dark:text-gray-400">Add configuration rows above to store data in the database table.</p>
@else
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-3 py-2.5 font-semibold">Configuration</th>
                    <th class="px-3 py-2.5 font-semibold">Size</th>
                    <th class="px-3 py-2.5 font-semibold">Total Units</th>
                    <th class="px-3 py-2.5 font-semibold">Available</th>
                    <th class="px-3 py-2.5 font-semibold">Card / Overview text</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                @foreach($rows as $row)
                    <tr>
                        <td class="px-3 py-2.5 font-medium text-gray-950 dark:text-white whitespace-nowrap">{{ $row['configuration'] }}</td>
                        <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $row['size'] }}</td>
                        <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ $row['total_units'] !== null ? number_format((int) $row['total_units']) : '—' }}
                        </td>
                        <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ $row['available_units'] !== null ? number_format((int) $row['available_units']) : '—' }}
                        </td>
                        <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $row['website_configuration'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
        Saved to database table <code class="font-mono">property_listing_units</code> when you save the property.
    </p>
@endif
