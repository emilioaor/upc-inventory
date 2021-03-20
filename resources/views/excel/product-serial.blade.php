<table>
    <thead>
        <tr>
            <th><strong>{{ date('m/d/Y') }}</strong></th>
        </tr>
        <tr>
            <th><strong>{{ $productSerialGroup->wholesaler }}</strong></th>
        </tr>
        <tr>
            <th><strong>{{ $productSerialGroup->invoice_number }}</strong></th>
        </tr>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th><strong>Product</strong></th>
            @foreach($productSerialsByProduct as $product)
                <th><strong>{{ $product['name'] }}</strong></th>
            @endforeach
        </tr>
        <tr>
            <th style="background-color: #c6c8ca"><strong>UPC</strong></th>
            @foreach($productSerialsByProduct as $product)
                <th style="background-color: #c6c8ca">{{ $product['upc'] ?? $product['sku'] }}</th>
            @endforeach
        </tr>
        @for($y = 0; $y < count($productSerialGroup->productSerials); $y++)
            <tr>
                @for($x = 0; $x < count($productSerialsByProduct); $x++)
                    @if($x === 0)
                        <td>{{ $y + 1 }}</td>
                    @endif
                    <td>{{ $productSerialsByProduct[$x]['productSerials'][$y]['serial'] ?? '' }}</td>
                @endfor
            </tr>
        @endfor
    </thead>
</table>