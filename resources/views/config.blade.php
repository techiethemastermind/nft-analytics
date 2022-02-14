@extends('layouts.app')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Setting</h1>
    </div>
    <!-- End Heading -->

    <!-- Page content -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            Setting
        </div>
        <div class="card-body">
            <form id="frm_setting" method="post" action="{{ route('config.store') }}">
                <div class="form-group">
                    <label class="form-label">TT value</label>
                    <select name="tt__value" id="" class="form-control">
                        <option value="15m">15 min</option>
                        <option value="30m">30 min</option>
                        <option value="1h">1h</option>
                        <option value="4h">4h</option>
                        <option value="1d">1d</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">TF value</label>
                    <select name="tf__value" id="" class="form-control">
                        <option value="15m">15 min</option>
                        <option value="30m">30 min</option>
                        <option value="1h">1h</option>
                        <option value="4h">4h</option>
                        <option value="1d">1d</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Page -->

    @push('after-scripts')
        <script>

            $(function() {

                $('#frm_setting').submit(function(e) {
                    e.preventDefault();

                    console.log('dfdfdf');

                    $(this).ajaxSubmit({
                        success: function(res) {

                            if (res.success) {
                                swal("Success!", "Successfully updated", "success");
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
