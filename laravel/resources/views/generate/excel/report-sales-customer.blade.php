<table>
    <thead style="font-weight: bold">
    <tr>
        <td style="width: 170px; font-weight: bold" rowspan="2" id="menu">Customer</td>
        <td colspan="{{ count($dates) }}" id="periode" style="font-weight: bold">
            Periode
            {{ date("d M y", strtotime($dates[0])) }}
            {{ date("d M y", strtotime($dates[count($dates) - 1])) }}
        </td>
        <td style="width: 70px; font-weight: bold;" rowspan="2" id="total">Total</td>
    </tr>
    <tr>
        @foreach($dates as $date)
            <td style="font-weight: bold">
                {{date('d', strtotime($date))}}
            </td>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($data as $customer)
        <tr>
            <td style="font-weight: bold">
                {{$customer['customer_name']}}
            </td>
            @foreach($customer['transaksi'] as $transactions)
                <td>
                    {{ $transactions['total_sales'] > 0 ? 'Rp ' . number_format($transactions['total_sales']) : '-' }}
                </td>
            @endforeach
            <td style="font-weight: bold">
                Rp {{number_format($customer['customer_total'])}}
            </td>
        </tr>
    @endforeach
    <tr>
        <td style="font-weight: bold">Grand Total</td>
        @foreach($total_per_date as $total)
            <td>
                {{ $total > 0 ? 'Rp ' . number_format($total) : '-' }}
            </td>
        @endforeach
        <td style="font-weight: bold">
            Rp {{number_format($grand_total)}}
        </td>
    </tr>
    </tbody>
</table>