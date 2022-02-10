@extends('layouts.app')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Token List</h1>
    </div>
    <!-- End Heading -->

    <!-- Page content -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex">
            <div class="col-6">
                Token List
            </div>
            <div class="col-6 text-right">
                <button id="btn_update" class="btn btn-primary">Update Database</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tbl_tokens" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Token</th>
                            <th>Contract</th>
                            <th>Action</th>
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
                        url: "{{ route('list.token') }}",
                        complete: function(res) {
                            console.log(res);
                        }
                    },
                    columns: [
                        { data: 'index' },
                        { data: 'token' },
                        { data: 'address'},
                        { data: 'action' }
                    ],
                    columnDefs: [
                        { width: 100, targets: 0 },
                        { width: 200, targets: 1 },
                        { width: 250, targets: 2 },
                        { width: 150, targets: 3 }
                    ]
                });

                $('#tbl_tokens').on('click', 'button', function() {
                    var tokenId = $(this).attr('data-id');

                    $.ajax({
                        method: 'POST',
                        url: "{{ route('list.token.update') }}",
                        data: {
                            token_id: tokenId
                        },
                        success: function(res) {
                            console.log(res);
                        }
                    })
                });

                $('#btn_update').on('click', () => {

                    console.log('works')
                    $.ajax({
                        method: 'POST',
                        url: "{{ route('update.database') }}",
                        success: function(res) {
                            console.log(res);
                        }
                    }) 
                });
            });
        </script>
    @endpush

@endsection
