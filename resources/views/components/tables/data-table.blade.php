<div class="card">
    @if(isset($title))
    <div class="card-header">
        <h5>{{ $title }}</h5>
        @if(isset($actions))
        <div class="card-tools">
            {{ $actions }}
        </div>
        @endif
    </div>
    @endif

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="{{ $id ?? 'dataTable' }}">
                <thead>
                    <tr>
                        {{ $headers }}
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($footer))
    <div class="card-footer">
        {{ $footer }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#{{ $id ?? "dataTable" }}').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            pageLength: {
                {
                    $perPage ?? 10
                }
            },
            order: [
                [{
                    {
                        $orderColumn ?? 0
                    }
                }, '{{ $orderDirection ?? "desc" }}']
            ]
        });
    });
</script>
@endpush