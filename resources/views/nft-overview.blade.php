@extends('layouts.app')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>
    <!-- End Heading -->

    <!-- Page content -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            NFT Total Overview
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tbl_tokens" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Token</th>
                            <th>15min</th>
                        </tr>
                    </thead>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page -->

    @push('after-scripts')
        <script>

            $(function() {

                $('#tbl_tokens').DataTable({
                    loadingIndicator: true,
                    pageLength: 25,
                    serverMethod: 'POST',
                    scrollCollapse: true,
                    ajax : {
                        url: "{{ route('dashboard.nft-overview-data') }}",
                        complete: function(res) {
                            $('#tokens_count').text(res.responseJSON.count);
                        }
                    },
                    columns: [
                        { data: 'index' },
                        { data: 'token' },
                        { data: 'result'},
                    ]
                });
            });
        </script>
    @endpush

@endsection
