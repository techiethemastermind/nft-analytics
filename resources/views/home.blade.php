@extends('layouts.app')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button id="btn_tokens" class="btn btn-primary">Start Grab Tokens</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <button id="btn_transactions" class="btn btn-primary">Start Grab Transactions</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <button id="btn_transactions_update" class="btn btn-danger">Update Transactions</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <button id="btn_transaction_items" class="btn btn-primary">Start Grab Transaction Items</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <button id="btn_transaction_items_update" class="btn btn-danger">Update Transaction Items</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <button id="btn_stop" class="btn btn-secondary">Stop Grabing</button>
        </div>
    </div>

@push('after-scripts')
<script>

    $(function() {

        $('#btn_tokens').on('click', function() {

            $.ajax({
                url: "{{ route('dashboard.tokens') }}",
                method: "get",
                success: function(res) {
                    console.log(res);
                },
                error: function(err) {
                    console.log(err);
                }
            })
        });

        $('#btn_transactions').on('click', function() {

            $.ajax({
                url: "{{ route('dashboard.transactions') }}",
                method: "get",
                success: function(res) {
                    console.log(res);
                },
                error: function(err) {
                    console.log(err);
                }
            })
        });

        $('#btn_transactions_update').on('click', function() {

            $.ajax({
                url: "{{ route('dashboard.transactions.update') }}",
                method: "get",
                success: function(res) {
                    console.log(res);
                },
                error: function(err) {
                    console.log(err);
                }
            })
        });

        $('#btn_transaction_items').on('click', function() {

            $.ajax({
                url: "{{ route('dashboard.transactions.items') }}",
                method: "get",
                success: function(res) {
                    console.log(res);
                },
                error: function(err) {
                    console.log(err);
                }
            })
        });

        $('#btn_transaction_items_update').on('click', function() {

            $.ajax({
                url: "{{ route('dashboard.transactions.items.update') }}",
                method: "get",
                success: function(res) {
                    console.log(res);
                },
                error: function(err) {
                    console.log(err);
                }
            })
        });

        $('#btn_stop').on('click', function() {

            $.ajax({
                url: "{{ route('dashboard.stop') }}",
                method: "get",
                success: function(res) {
                    console.log(res);
                },
                error: function(err) {
                    console.log(err);
                }
            })
        });


    });
</script>
@endpush

@endsection
