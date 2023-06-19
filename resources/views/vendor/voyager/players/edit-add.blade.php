@php
    use App\History;
    use App\Deposit;
    use App\Withdraw;
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
    $history = History::where('player_id',$dataTypeContent->getKey())->orderBy('created_at',"DESC")->limit(20)->get();
    $deposit = Deposit::where('player_id',$dataTypeContent->getKey())->orderBy('created_at',"DESC")->limit(20)->get();
    $withdraw = Withdraw::where('player_id',$dataTypeContent->getKey())->orderBy('created_at',"DESC")->limit(20)->get();
    
@endphp

@extends('voyager::master')

@section('css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            width:100%
        }
        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }
        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }
        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }
        .styled-table tbody tr.active-row {
            font-weight: bold;
            color: #009879;
        }
        
        body {
            color: #333;
            font-family: 'Helvetica', arial;
        }
        .wrap {
            padding: 40px;
            text-align: center;
        }
        hr {
            clear: both;
            margin-top: 40px;
            margin-bottom: 40px;
            border: 0;
            border-top: 1px solid #aaa;
        }
        h1 {
            font-size: 30px;
            margin-bottom: 40px;
        }
        p {
            margin-bottom: 20px;
        }
        .btn {
            background: #428bca;
            border: #357ebd solid 1px;
            border-radius: 3px;
            color: #fff;
            display: inline-block;
            font-size: 14px;
            padding: 8px 15px;
            text-decoration: none;
            text-align: center;
            min-width: 60px;
            position: relative;
            transition: color 0.1s ease;
        }
        .btn:hover {
            background: #357ebd;
        }
        .btn.btn-big {
            font-size: 18px;
            padding: 15px 20px;
            min-width: 100px;
        }
        .btn-close {
            color: #aaa;
            font-size: 30px;
            text-decoration: none;
            position: absolute;
            right: 5px;
            top: 0;
        }
        .btn-close:hover {
            color: #919191;
        }
        .modal1:before {
            content: "";
            display: none;
            
            background: rgba(0, 0, 0, 0);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }
        .modal1:target:before {
            -webkit-transition: -webkit-transform 0.9s ease-out;
            -moz-transition: -moz-transform 0.9s ease-out;
            -o-transition: -o-transform 0.9s ease-out;
            transition: transform 0.9s ease-out;
            z-index: 10;
            background: rgba(0, 0, 0, .6);
            /*display: block;
            */
        }
        .modal1:target + .modal1-dialog {
            -webkit-transform: translate(0, 0);
            -ms-transform: translate(0, 0);
            transform: translate(0, 0);
            top: 20%;
        }
        .modal1-dialog {
            background: #fefefe;
            border: #333 solid 1px;
            border-radius: 5px;
            margin-left: -200px;
            position: fixed;
            left: 50%;
            top: -100%;
            z-index: 11;
            width: 360px;
            -webkit-transform: translate(0, -500%);
            -ms-transform: translate(0, -500%);
            transform: translate(0, -500%);
            -webkit-transition: -webkit-transform 0.3s ease-out;
            -moz-transition: -moz-transform 0.3s ease-out;
            -o-transition: -o-transform 0.3s ease-out;
            transition: transform 0.3s ease-out;
        }
        .modal1-body {
            padding: 20px;
        }
        .modal1-header, .modal1-footer {
            padding: 10px 20px;
        }
        .modal1-header {
            border-bottom: #eee solid 1px;
        }
        .modal1-header h2 {
            font-size: 20px;
        }
        .modal1-footer {
            border-top: #eee solid 1px;
            text-align: right;
        }
        
        
    </style>
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <div style="display:flex; justify-content:space-between;align-items: center;">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i>
            {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
        </h1>
        <a href="#modal-one" class="btn btn-sm btn-primary pull-right edit">Tăng, Trừ tiền</a>
    </div>
   
    
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row" x-data="popup">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if ($add && isset($row->details->view_add))
                                        @include($row->details->view_add, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'add', 'options' => $row->details])
                                    @elseif ($edit && isset($row->details->view_edit))
                                        @include($row->details->view_edit, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'view' => 'edit', 'options' => $row->details])
                                    @elseif (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @elseif($row->type == 'number')
                                        <input  type="text" class="form-control" placeholder="$" :value="money" disabled> 
                                        <input type="hidden" class="form-control" name="money"   :value="money">
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                            
                        </div>
                    </form>
                    
                        <!-- Modal -->
                        <a href="#" class="modal1" id="modal-one" aria-hidden="true">
                        </a>
                        <div class="modal1-dialog">
                            <div class="modal1-header">
                                <h2>Nhập số tiền</h2>
                                <a href="#" class="btn-close" aria-hidden="true">×</a>
                            </div>
                            <div class="modal1-body">
                                <input style="width:100%" type="number" x-model="number">
                            </div>
                            <div class="modal1-footer">
                                <a x-on:click="tangtien" aria-hidden="true" href="#" class="btn">Tăng tiền !</a>
                                <a x-on:click="trutien" aria-hidden="true" style="background-color:red" href="#" class="btn">Trừ tiền</a>
                            </div>
                        </div>

                        <!-- /Modal -->
                        
                    <div style="display:none">
                        <input type="hidden" id="upload_url" value="{{ route('voyager.upload') }}">
                        <input type="hidden" id="upload_type_slug" value="{{ $dataType->slug }}">
                    </div>
                </div>
            </div>
            
        </div>
       <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('popup', () => ({
                    money:{{$dataTypeContent->money ?? 0}}, 
                    number : 0,
                    tangtien() {
                        this.money = parseInt(this.money) +parseInt(this.number)
                        alert("Cộng tiền thành công ! ");
                        
                    },
                    trutien(){
                       this.money = parseInt(this.money) - parseInt(this.number)
                        alert("Trừ tiền thành công ! ");
                    }
                }))
            })
        </script>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding:25px">
                    <div class="" style="display: flex;align-items: center;">
                        <i style="font-size: 25px;margin-right:5px" class="voyager-documentation"></i>
                        <h3 style="color: green;font-weight: 600;">Lịch sử giao dịch</h2>
                    </div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Thời gian đặt lệnh</th>
                                <th>Tổng chơi</th>
                                <th>Tổng (Thắng/Thua)</th>
                                <th>Lệnh đặt</th>
                                <th>Kì chơi</th>
                                <th>Kết quả</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $key => $value)
                                <tr @if($key%2 == 0) class="" @else class="active-row"@endif >
                                    <td>{{$value->created_at}}</td>
                                    <td>{{$value->price}}</td>
                                    <td>{{$value->total_price}}</td>
                                    @if($value->command_bet == 'up')
                                        <td>Lên</td>
                                    @else
                                        <td>Xuống</td>
                                    @endif
                                    <td>{{$value->period}}</td>
                                    @if($value->status == 'win')
                                        <td>Thắng</td>
                                    @elseif($value->status == 'loss')
                                        <td>Thua</td>
                                    @elseif($value->status == 'success')
                                        <td>Đặt lệnh thành công</td>
                                    @else
                                        <td>Trả lại</td>
                                    @endif
                                </tr>
                            @endforeach
                            <!-- and so on... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding:25px">
                    <div class="" style="display: flex;align-items: center;">
                        <i style="font-size: 25px;margin-right:5px" class="voyager-plus"></i>
                        <h3 style="color: green;font-weight: 600;">Lịch sử nạp tiền</h2>
                    </div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Thời gian </th>
                                <th>Số tiền nạp</th>
                                <th>Trạngt thái</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                            @foreach($deposit as $key => $value)
                               <tr @if($key%2 == 0) class="" @else class="active-row"@endif >
                                    <td>{{$value->created_at}}</td>
                                    <td>{{$value->deposit}}</td>
                                 
                                    @if($value->status == 'waiting')
                                        <td>Chờ xét duyệt</td>
                                    @elseif($value->status == 'success')
                                        <td>Thành công</td>
                                    @elseif($value->status == 'fail')
                                        <td>Thất bại</td>
                                    @endif
                                     <td>{{ $value->desc}}</td>
                                </tr>
                            @endforeach
                            <!-- and so on... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding:25px">
                    <div class="" style="display: flex;align-items: center;">
                        <i style="font-size: 25px;margin-right:5px" class="voyager-forward"></i>
                        <h3 style="color: green;font-weight: 600;">Lịch sử rút tiền</h2>
                    </div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Thời gian </th>
                                <th>Số tiền rút</th>
                                <th>Trạngt thái</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdraw as $key => $value)
                                <tr @if($key%2 == 0) class="" @else class="active-row"@endif >
                                    <td>{{$value->created_at}}</td>
                                    <td>{{$value->withdraw}}</td>
                                 
                                    @if($value->status == 'waiting')
                                        <td>Chờ xét duyệt</td>
                                    @elseif($value->status == 'success')
                                        <td>Thành công</td>
                                    @elseif($value->status == 'fail')
                                        <td>Thất bại</td>
                                    @endif
                                     <td>{{ $value->desc}}</td>
                                </tr>
                            @endforeach
                            <!-- and so on... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-default" id="no-delete-modal">{{ __('voyager::generic.cancel') }}</button> --}}
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
            
          return function() {
            $file = $(this).siblings(tag);
             console.log($file )
            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete').modal('show');
          };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));
           
            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
