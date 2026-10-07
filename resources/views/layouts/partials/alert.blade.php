@foreach (['success' => 'success', 'error' => 'danger'] as $key => $kelas)
    @if (session($key))
        <div class="alert alert-{{ $kelas }} alert-dismissible fade show">
            {{ session($key) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif