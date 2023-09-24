@switch($type)
    @case('all')
        <?php
        if (auth()->user()->roleuser_id == '1' || auth()->user()->roleuser_id == '4') {
            switch ($links) {
                case 'user':
                    $view = true;
                    $edit = true;
                    $delete = true;
                    $aktif = true;
                    $slug = $data->username;
                    break;
        
                case 'jmosip':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
                case 'mutasi':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
                case 'campaign/group':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
        
                case 'statuscall':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
                case 'callhistory':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
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
                    $aktif = true;
                    $slug = $data->username;
                    break;
        
                case 'jmosip':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
                case 'mutasi':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
                case 'campaign/group':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
        
                case 'statuscall':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
                    $slug = encrypt($data->id);
                    break;
                case 'callhistory':
                    $view = false;
                    $edit = true;
                    $delete = false;
                    $aktif = false;
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
                <input type="hidden" name="tipe" value="delete">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin?')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        @endif
        @if ($aktif)
            <form action="/{{ $links }}" method="post" class="d-inline">
                @method('delete')
                @csrf
                <input type="hidden" name="id" value="{{ encrypt($data->id) }}">
                <input type="hidden" name="tipe" value="kehadiran">
                <input type="hidden" name="flag" value="{{ $data->flag_hadir == date('Y-m-d') ? '0' : '1' }}">
                <button type="submit"
                    class="btn {{ $data->flag_hadir == date('Y-m-d') ? 'btn-success' : 'btn-danger' }} btn-sm"
                    onclick="return confirm('Apakah anda yakin?')">
                    {!! $data->flag_hadir == date('Y-m-d')
                        ? '<i class="fas fa-user"></i> Hadir'
                        : '<i class="fas fa-user-slash"></i> Tidak Hadir' !!}
                </button>
            </form>
        @endif
    @break

    @case('onclick')
        <a class="btn btn-info btn-sm" onclick="{{ $links }}">
            <i class="fas fa-pencil-alt">
            </i>
            Edit
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
