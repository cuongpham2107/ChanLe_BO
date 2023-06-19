@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))
@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop
@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
    </h1>
   
    @can('add', app($dataType->model_name))
    <a id="btn_add_new" href="javascript:;" class="btn btn-success btn-add-new btn_add_new">
        <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
    </a>
    @endcan
    @can('delete', app($dataType->model_name))
    @include('voyager::partials.bulk-delete')
    @endcan
    @can('edit', app($dataType->model_name))
    @if(!empty($dataType->order_column) && !empty($dataType->order_display_column))
    <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
        <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
    </a>
    @endif
    @endcan
    @can('delete', app($dataType->model_name))
    @if($usesSoftDeletes)
    <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle"
        data-on="{{ __('voyager::bread.soft_deletes_off') }}" data-off="{{ __('voyager::bread.soft_deletes_on') }}">
    @endif
    @endcan
    @foreach($actions as $action)
    @if (method_exists($action, 'massAction'))
    @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
    @endif
    @endforeach
    @include('voyager::multilingual.language-selector')
   
</div>
@stop

@section('content')
<div class="page-content browse container-fluid">
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    @if ($isServerSide)
                    <form method="get" class="form-search">
                        <div id="search-input">
                            <div class="col-4">
                                <select id="search_key" name="key">
                                    @foreach($searchNames as $key => $name)
                                    <option value="{{ $key }}" @if($search->key == $key || (empty($search->key) && $key
                                        == $defaultSearchKey)) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="filter" value="contains">
                            {{-- <div class="col-2">
                                <select id="filter" name="filter">
                                    <option value="contains" @if($search->filter == "contains") selected @endif>Giống</option>
                                    <option value="equals" @if($search->filter == "equals") selected @endif>=</option>
                                </select>
                            </div> --}}
                            <div class="input-group col-md-12">
                                <input type="text" class="form-control"
                                    placeholder="{{ __('voyager::generic.search') }}" name="s"
                                    value="{{ $search->value }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-info btn-lg" type="submit">
                                        <i class="voyager-search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        @if (Request::has('sort_order') && Request::has('order_by'))
                        <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                        <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                        @endif
                    </form>
                    @endif
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-hover">
                            <thead>
                                <tr>
                                    @if($showCheckboxColumn)
                                    <th class="dt-not-orderable">
                                        <input type="checkbox" class="select_all">
                                    </th>
                                    @endif
                                    @foreach($dataType->browseRows as $row)
                                    <th>
                                        @if ($isServerSide && in_array($row->field, $sortableColumns))
                                        <a style="color:black;font-weight: 600" href="{{ $row->sortByUrl($orderBy, $sortOrder) }}">
                                            @endif
                                            {{ $row->getTranslatedAttribute('display_name') }}
                                            @if ($isServerSide)
                                            @if ($row->isCurrentSortField($orderBy))
                                            @if ($sortOrder == 'asc')
                                            <i class="voyager-angle-up pull-right"></i>
                                            @else
                                            <i class="voyager-angle-down pull-right"></i>
                                            @endif
                                            @endif
                                        </a>
                                        @endif
                                    </th>
                                    @endforeach
                                    {{-- <th class="actions text-right dt-not-orderable">{{
                                        __('voyager::generic.actions') }}</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataTypeContent as $data)
                                <tr>
                                    @if($showCheckboxColumn)
                                    <td>
                                        <input type="checkbox" name="row_id" id="checkbox_{{ $data->getKey() }}"
                                            value="{{ $data->getKey() }}">
                                    </td>
                                    @endif
                                    @foreach($dataType->browseRows as $row)
                                    @php
                                    if ($data->{$row->field.'_browse'}) {
                                    $data->{$row->field} = $data->{$row->field.'_browse'};
                                    }
                                    @endphp
                                    <td>
                                        @if (isset($row->details->view_browse))
                                        @include($row->details->view_browse, ['row' => $row, 'dataType' => $dataType,
                                        'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field}, 'view'
                                        => 'browse', 'options' => $row->details])
                                        @elseif (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType,
                                        'dataTypeContent' => $dataTypeContent, 'content' => $data->{$row->field},
                                        'action' => 'browse', 'view' => 'browse', 'options' => $row->details])
                                        @elseif($row->type == 'image')
                                        <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif"
                                            style="width:100px">
                                        @elseif($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['view' => 'browse','options' =>
                                        $row->details])
                                        @elseif($row->type == 'select_multiple')
                                        @if(property_exists($row->details, 'relationship'))

                                        @foreach($data->{$row->field} as $item)
                                        {{ $item->{$row->field} }}
                                        @endforeach

                                        @elseif(property_exists($row->details, 'options'))
                                        @if (!empty(json_decode($data->{$row->field})))
                                        @foreach(json_decode($data->{$row->field}) as $item)
                                        @if (@$row->details->options->{$item})
                                        {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                        @endif
                                        @endforeach
                                        @else
                                        {{ __('voyager::generic.none') }}
                                        @endif
                                        @endif

                                        @elseif($row->type == 'multiple_checkbox' && property_exists($row->details,
                                        'options'))
                                        @if (@count(json_decode($data->{$row->field}, true)) > 0)
                                        @foreach(json_decode($data->{$row->field}) as $item)
                                        @if (@$row->details->options->{$item})
                                        {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                        @endif
                                        @endforeach
                                        @else
                                        {{ __('voyager::generic.none') }}
                                        @endif

                                        @elseif($row->type == 'select_dropdown')
                                        @include('voyager::multilingual.input-hidden-bread-browse')

                                        <style>
                                            .custom_select_option {
                                                border-radius: 15px;
                                                padding: 0 10px;
                                                margin-right: 5px;
                                                color: white;
                                                font-weight: 500;
                                            }
                                        </style>

                                        {{-- <select class="custom_select_option" data-id="{{$data->getKey()}}"
                                            @if($data->{$row->field} == 'up')
                                            style=" background-color: #22c55e;"
                                            @else
                                            style=" background-color: #f87171;"
                                            @endif
                                            id="cars">
                                            @if($data->{$row->field} == 'up')
                                            <option selected value="up">Lên</option>
                                            <option value="down">Xuống</option>
                                            @else
                                            <option value="up">Lên</option>
                                            <option selected value="down">Xuống</option>
                                            @endif
                                        </select> --}}
                                        <form action="" method="post" id="radio_form-{{$data->getKey()}}">  
                                            {{ method_field('POST') }}
                                            {{ csrf_field() }}
                                            <div class="" style="display:flex;">
                                                <div class="" style="margin-right:10px">
                                                    <input value="up" class="custom_select_radio"
                                                        id="up-{{$data->getKey()}}}}" data-id="{{$data->getKey()}}"
                                                        type="radio" @if($data->{$row->field} == 'up') checked @endif
                                                    name="command_bet" @if($data->created_at < \Carbon\Carbon::now()) disabled @endif >
                                                    <label for="up-{{$data->getKey()}}}}"
                                                        style="background-color: #22c55e; border-radius:15px;padding:0 10px; color:white;font-weight: 600"
                                                        for="">Lên</label>
                                                </div>
                                                <div class="">
                                                    <input value="down" class="custom_select_radio"
                                                        id="down-{{$data->getKey()}}}}" data-id="{{$data->getKey()}}"
                                                        type="radio" @if($data->{$row->field} == 'down') checked @endif
                                                    name="command_bet" @if($data->created_at < \Carbon\Carbon::now()) disabled @endif>
                                                    <label for="down-{{$data->getKey()}}}}"
                                                        style="background-color: #f87171;border-radius:15px;padding:0 10px; color:white;font-weight: 600"
                                                        for="">Xuống</label>
                                                </div>
                                                {{-- <input type="hidden" name="id" value="{{ $data->getKey() }}"> --}}
                                            </div>
                                        </form>
                                        


                                        @elseif(($row->type == 'select_dropdown' || $row->type == 'radio_btn') &&
                                        property_exists($row->details, 'options'))

                                        {!! $row->details->options->{$data->{$row->field}} ?? '' !!}

                                        @elseif($row->type == 'date' || $row->type == 'timestamp')
                                        @if ( property_exists($row->details, 'format') && !is_null($data->{$row->field})
                                        )
                                        {{
                                        \Carbon\Carbon::parse($data->{$row->field})->formatLocalized($row->details->format)
                                        }}
                                        @else
                                        {{ $data->{$row->field} }}
                                        @endif
                                        @elseif($row->type == 'checkbox')
                                        @if(property_exists($row->details, 'on') && property_exists($row->details,
                                        'off'))
                                        @if($data->{$row->field})
                                        <span class="label label-info">{{ $row->details->on }}</span>
                                        @else
                                        <span class="label label-primary">{{ $row->details->off }}</span>
                                        @endif
                                        @else
                                        {{ $data->{$row->field} }}
                                        @endif
                                        @elseif($row->type == 'color')
                                        <span class="badge badge-lg"
                                            style="background-color: {{ $data->{$row->field} }}">{{ $data->{$row->field}
                                            }}</span>
                                        @elseif($row->type == 'text')
                                        @include('voyager::multilingual.input-hidden-bread-browse')
                                        <div>{{ mb_strlen( $data->{$row->field} ) > 200 ?
                                            mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}
                                        </div>
                                        @elseif($row->type == 'text_area')
                                        @include('voyager::multilingual.input-hidden-bread-browse')
                                        <div>{{ mb_strlen( $data->{$row->field} ) > 200 ?
                                            mb_substr($data->{$row->field}, 0, 200) . ' ...' : $data->{$row->field} }}
                                        </div>
                                        @elseif($row->type == 'file' && !empty($data->{$row->field}) )
                                        @include('voyager::multilingual.input-hidden-bread-browse')
                                        @if(json_decode($data->{$row->field}) !== null)
                                        @foreach(json_decode($data->{$row->field}) as $file)
                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}"
                                            target="_blank">
                                            {{ $file->original_name ?: '' }}
                                        </a>
                                        <br />
                                        @endforeach
                                        @else
                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($data->{$row->field}) }}"
                                            target="_blank">
                                            {{ __('voyager::generic.download') }}
                                        </a>
                                        @endif
                                        @elseif($row->type == 'rich_text_box')
                                        @include('voyager::multilingual.input-hidden-bread-browse')
                                        <div>{{ mb_strlen( strip_tags($data->{$row->field}, '<b><i><u>') ) > 200 ?
                                                        mb_substr(strip_tags($data->{$row->field}, '<b><i><u>'), 0, 200)
                                                                    . ' ...' : strip_tags($data->{$row->field},
                                                                    '<b><i><u>') }}</div>
                                        @elseif($row->type == 'coordinates')
                                        @include('voyager::partials.coordinates-static-image')
                                        @elseif($row->type == 'multiple_images')
                                        @php $images = json_decode($data->{$row->field}); @endphp
                                        @if($images)
                                        @php $images = array_slice($images, 0, 3); @endphp
                                        @foreach($images as $image)
                                        <img src="@if( !filter_var($image, FILTER_VALIDATE_URL)){{ Voyager::image( $image ) }}@else{{ $image }}@endif"
                                            style="width:50px">
                                        @endforeach
                                        @endif
                                        @elseif($row->type == 'media_picker')
                                        @php
                                        if (is_array($data->{$row->field})) {
                                        $files = $data->{$row->field};
                                        } else {
                                        $files = json_decode($data->{$row->field});
                                        }
                                        @endphp
                                        @if ($files)
                                        @if (property_exists($row->details, 'show_as_images') &&
                                        $row->details->show_as_images)
                                        @foreach (array_slice($files, 0, 3) as $file)
                                        <img src="@if( !filter_var($file, FILTER_VALIDATE_URL)){{ Voyager::image( $file ) }}@else{{ $file }}@endif"
                                            style="width:50px">
                                        @endforeach
                                        @else
                                        <ul>
                                            @foreach (array_slice($files, 0, 3) as $file)
                                            <li>{{ $file }}</li>
                                            @endforeach
                                        </ul>
                                        @endif
                                        @if (count($files) > 3)
                                        {{ __('voyager::media.files_more', ['count' => (count($files) - 3)]) }}
                                        @endif
                                        @elseif (is_array($files) && count($files) == 0)
                                        {{ trans_choice('voyager::media.files', 0) }}
                                        @elseif ($data->{$row->field} != '')
                                        @if (property_exists($row->details, 'show_as_images') &&
                                        $row->details->show_as_images)
                                        <img src="@if( !filter_var($data->{$row->field}, FILTER_VALIDATE_URL)){{ Voyager::image( $data->{$row->field} ) }}@else{{ $data->{$row->field} }}@endif"
                                            style="width:50px">
                                        @else
                                        {{ $data->{$row->field} }}
                                        @endif
                                        @else
                                        {{ trans_choice('voyager::media.files', 0) }}
                                        @endif

                                        @else
                                        @include('voyager::multilingual.input-hidden-bread-browse')
                                        <span>{{ $data->{$row->field} }}</span>
                                        @endif

                                    </td>
                                    @endforeach
                                    {{-- <td class="no-sort no-click bread-actions">
                                        @foreach($actions as $action)
                                        @if (!method_exists($action, 'massAction'))

                                        @include('voyager::bread.partials.actions', ['action' => $action])

                                        @endif
                                        @endforeach
                                    </td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($isServerSide)
                    <div class="pull-left">
                        <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                            'voyager::generic.showing_entries', $dataTypeContent->total(), [
                            'from' => $dataTypeContent->firstItem(),
                            'to' => $dataTypeContent->lastItem(),
                            'all' => $dataTypeContent->total()
                            ]) }}</div>
                    </div>
                    <div class="pull-right">
                        {{ $dataTypeContent->appends([
                        's' => $search->value,
                        'filter' => $search->filter,
                        'key' => $search->key,
                        'order_by' => $orderBy,
                        'sort_order' => $sortOrder,
                        'showSoftDeleted' => $showSoftDeleted,
                        ])->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  
    </script>
{{-- Single delete modal --}}
<div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{
                    strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
            </div>
            <div class="modal-footer">
                <form action="#" id="delete_form" method="POST">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}
                    <input type="submit" class="btn btn-danger pull-right delete-confirm"
                        value="{{ __('voyager::generic.delete_confirm') }}">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{
                    __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-danger fade" tabindex="-1" id="command_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#2ecc71">
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-edit"></i>Bạn có muốn chuyển trạng thái của lệnh này không?
                </h4>
            </div>
            <div class="modal-footer">
                <form action="#" id="command_form" method="POST">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}
                    <input type="hidden" name="command_bet" value="" id="command_bet">
                    <input type="submit" class="btn btn-sm btn-primary pull-right edit" value="Chắc chắn, Có">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{
                    __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal modal-danger fade" tabindex="-1" id="add_control" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#2ecc71">
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-edit"></i> Tạo mới Điều khiển </h4>
            </div>
            <div class="modal-footer">
                <form action="#" id="add_control_form" method="POST">
                    {{ method_field('POST') }}
                    {{ csrf_field() }}
                    
                    <div class="form-group col-md-12">
                        <label class="control-label" style="float:left" for="name">Số lệnh cần tạo</label>
                        <select style="float: left; margin-bottom:20px" class="form-control" name="command_number">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <input type="submit" class="btn btn-sm btn-primary pull-right edit" value="Lưu">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{
                    __('voyager::generic.cancel') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('css')
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
<link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
<!-- DataTables -->
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
<script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
@endif
<script>
    $(document).ready(function () {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => $orderColumn,
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [
                            ['targets' => 'dt-not-orderable', 'searchable' =>  false, 'orderable' => false],
                        ],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function(){
                    $('.side-body').data('multilingual').init();
                })
            @endif
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                    }else{
                        $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
        $('input[name="row_id"]').on('change', function () {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });

        $('.custom_select_radio').on('change', function() {
            var id =  $(this).data('id')
            console.log(id,`radio_form-${id}`);
            var form = document.getElementById(`radio_form-${id}`);
            form.action = '{{ route('voyager.'.$dataType->slug.'.update_cammand', '__id') }}'.replace('__id', $(this).data('id'));
            form.submit();
           
            // var command =  this.value;
            // // alert(command);
            //  $('#command_modal').modal('show');
            //  $('#command_bet')[0].value = command;
            //  $('#command_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.update_cammand', '__id') }}'.replace('__id', $(this).data('id'));
            
        });
       
       
       $('#btn_add_new').on('click', function (e) {
             $('#add_control').modal('show');
             $('#add_control_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.add_command') }}';
            
        });

        
        
</script>
@stop