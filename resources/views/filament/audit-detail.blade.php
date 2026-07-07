<div class="space-y-4 p-2">
    <table class="w-full text-sm">
        <tbody>
            @foreach ($data as $label => $value)
                @if (is_string($value) && in_array($label, ['Valores anteriores', 'Valores nuevos']))
                    <tr class="border-t">
                        <td class="py-2 pr-4 font-medium text-gray-600 align-top whitespace-nowrap">{{ $label }}</td>
                        <td class="py-2">
                            <pre class="bg-gray-100 rounded p-2 text-xs overflow-auto max-h-64">{{ $value }}</pre>
                        </td>
                    </tr>
                @else
                    <tr class="border-t">
                        <td class="py-2 pr-4 font-medium text-gray-600 whitespace-nowrap">{{ $label }}</td>
                        <td class="py-2 text-gray-900">{{ $value ?? '-' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
