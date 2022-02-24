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
            <form id="frm_setting" method="post" action="{{ route('dashboard.config.store') }}">

                @if(!empty($ttValues))

                    @php $arrayValues = json_decode($ttValues) @endphp

                    @foreach($arrayValues as $key => $value)
                    <div class="form-group">
                        <label class="form-label">Time {{ $loop->iteration }}</label>
                        <select name="tt__values[{{ $loop->iteration }}]" class="form-control">
                            <option value="">Select Time</option>
                            <option value="15" @if($key == $loop->iteration && $value == '15') Selected @endif>15m</option>
                            <option value="20" @if($key == $loop->iteration && $value == '20') Selected @endif>20m</option>
                            <option value="30" @if($key == $loop->iteration && $value == '30') Selected @endif>30m</option>
                            <option value="60" @if($key == $loop->iteration && $value == '60') Selected @endif>1h</option>
                            <option value="240" @if($key == $loop->iteration && $value == '240') Selected @endif>4h</option>
                            <option value="1440" @if($key == $loop->iteration && $value == '1440') Selected @endif>1d</option>
                            <option value="10080" @if($key == $loop->iteration && $value == '10080') Selected @endif>1w</option>
                        </select>
                    </div>
                    @endforeach

                @else

                    @for($i = 1; $i < 11; $i++)
                    <div class="form-group">
                        <label class="form-label">Time {{ $i }}</label>
                        <select name="tt__values[{{$i}}]" class="form-control">
                            <option value="">Select Time</option>
                            <option value="15">15m</option>
                            <option value="20">20m</option>
                            <option value="30">30m</option>
                            <option value="60">1h</option>
                            <option value="240">4h</option>
                            <option value="1440">1d</option>
                            <option value="10080">1w</option>
                        </select>
                    </div>
                    @endfor
                @endif

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
