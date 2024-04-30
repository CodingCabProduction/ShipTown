<table class="table table-borderless">
    <thead>
    <tr>
        <th scope="col">Orders - Packed Today</th>
        <th scope="col" class="text-right">{{ $total_count }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($count_per_user as $count)
        <tr>
            <td>
                <a href="{{ route('orders', [
                    'sort' => '-packed_at',
                    'packed_between' => $config['starting_date']->toDateTimeString() . ',' .$config['ending_date']->toDateTimeString(),
                    'packer_user_id' =>  $count['packer_user_id'],
                    ]) }}">
                    {{ $count['name'] }}
                </a>
            </td>
            <td class="text-right">{{ $count['total'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
