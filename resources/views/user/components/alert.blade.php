@if(session('success'))
    <div class="alert alert-success d-flex justify-content-between align-items-center fade show" role="alert" style="border-radius:8px;">
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger d-flex justify-content-between align-items-center fade show" role="alert" style="border-radius:8px;">
        <span>{{ session('error') }}</span>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger d-flex justify-content-between align-items-center fade show" role="alert" style="border-radius:8px;">
        <span>
            @foreach($errors->all() as $err)
                {{ $err }}<br>
            @endforeach
        </span>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
