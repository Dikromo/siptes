@switch($type)
    @case('all')
        <a class="btn btn-success btn-sm" href="/{{ $links }}/{{ $data->username }}">
            <i class="fas fa-eye">
            </i>
            View
        </a>
        <a class="btn btn-info btn-sm" href="/{{ $links }}/{{ $data->username }}/edit">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
        </a>
        <a class="btn btn-danger btn-sm" href="#">
            <i class="fas fa-trash">
            </i>
            Delete
        </a>
    @break

    @case('sales')
        <a class="btn btn-success btn-sm" href="/{{ $links }}/{{ encrypt($data->id) }}">
            <i class="fas fa-headset">
            </i>
            Call
        </a>
    @break

    @default
@endswitch
