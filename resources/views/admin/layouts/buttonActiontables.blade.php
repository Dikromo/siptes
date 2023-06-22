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
        <form action="/{{ $links }}" method="post" class="d-inline">
            @method('delete')
            @csrf
            <input type="hidden" name="id" value="{{ encrypt($data->id) }}">
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
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
