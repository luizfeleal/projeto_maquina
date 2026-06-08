{{--
    Global session alerts via SweetAlert2.
    Include once in layouts, after @yield('scriptTable') and after SweetAlert2 CDN.
--}}
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: @json(session('success')),
        confirmButtonColor: '#2C9BA5',
        confirmButtonText: 'OK',
        timer: 6000,
        timerProgressBar: true,
    });
});
</script>
@elseif(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: @json(session('error')),
        confirmButtonColor: '#2C9BA5',
        confirmButtonText: 'OK',
    });
});
</script>
@elseif(session('warning'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'warning',
        title: 'Atenção!',
        text: @json(session('warning')),
        confirmButtonColor: '#2C9BA5',
        confirmButtonText: 'OK',
    });
});
</script>
@elseif(session('info'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'info',
        title: 'Informação',
        text: @json(session('info')),
        confirmButtonColor: '#2C9BA5',
        confirmButtonText: 'OK',
    });
});
</script>
@endif
