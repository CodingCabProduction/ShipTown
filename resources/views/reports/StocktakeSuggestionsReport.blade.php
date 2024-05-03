<div class="row col">
    <table class="table table-borderless">
        <thead>
        <tr>
            <th scope="col">{{ __($meta['report_name']) }}  </th>
            <th scope="col" class="text-right"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $record)
            <tr>
                <td>
                    <a href="{{ route('stocktaking', ['filter[warehouse_code]' => data_get($record, $meta['field_links'][0]['name'])]) }}">
                        {{ data_get($record, $meta['field_links'][0]['name']) }}
                    </a>
                </td>
                <td class="text-right">{{ data_get($record, $meta['field_links'][1]['name']) === 0 ? '-' : data_get($record, $meta['field_links'][1]['name'])}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
