<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

<script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>
<script>
    @if (Session::has('success'))
        // toastr.error('Gagal Login, Username dan password tidak sesuai!')
        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'Berhasil',
            body: '{{ Session::get('success') }}'
        })
    @endif
</script>
