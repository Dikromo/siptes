@switch($type)
    @case('all')
        <?php
        if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4') {
            switch ($links) {
                case 'user':
                    $view = true;
                    $edit = true;
                    $delete = true;
        
                    $slug = $data->username;
                    break;
        
                case 'jmosip':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $slug = encrypt($data->id);
                    break;
        
                case 'statuscall':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $slug = encrypt($data->id);
                    break;
        
                default:
                    $slug = '';
                    break;
            }
        } else {
            switch ($links) {
                case 'user':
                    $view = false;
                    $edit = true;
                    $delete = false;
        
                    $slug = $data->username;
                    break;
        
                case 'jmosip':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $slug = encrypt($data->id);
                    break;
        
                case 'statuscall':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $slug = encrypt($data->id);
                    break;
        
                default:
                    $slug = '';
                    break;
            }
        }
        ?>
        @if ($view)
            <a class="btn btn-success btn-sm" href="/{{ $links }}/{{ $slug }}">
                <i class="fas fa-eye">
                </i>
                View
            </a>
        @endif
        @if ($edit)
            <a class="btn btn-info btn-sm" href="/{{ $links }}/{{ $slug }}/edit">
                <i class="fas fa-pencil-alt">
                </i>
                Edit
            </a>
        @endif
        @if ($delete)
            <form action="/{{ $links }}" method="post" class="d-inline">
                @method('delete')
                @csrf
                <input type="hidden" name="id" value="{{ encrypt($data->id) }}">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin?')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        @endif
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
