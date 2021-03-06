@extends('core::layout.backend')

@section('page_specific_head')
    <link href="<?php echo asset_path(); ?>/plugins/DataTables/css/data-table.css" rel="stylesheet"/>
    <link href="<?php echo asset_path(); ?>/plugins/switchery/switchery.min.css" rel="stylesheet"/>
    <link href="<?php echo asset_path(); ?>/plugins/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
@stop


@section('content')




    <!-- begin page-header -->
    <h1 class="page-header">{{$title}}</h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">

        <!-- begin col-10 -->
        <div class="col-md-12">


            <!-- #modal-dialog -->
            <div class="modal fade" id="modal-dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form" id="demo-form" data-parsley-validate>

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Details </h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                 <input type="text" class="form-control" placeholder="Permission Name" id="name" name="name" required >
                                 <input type="hidden" name="id">

                            </div>

                        </div>
                        <div class="modal-footer">
                            <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
                            <button type="button" id="permission_submit" data-href="<?php echo URL::route('permissionStore'); ?>"  class="btn btn-sm btn-success">Submit</button>
                        </div>
                        <!-- {{ Form::close() }} -->
                        </form>

                    </div>
                </div>
            </div>
            <!-- #end of modal-dialog -->


            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default"
                           data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                           data-click="panel-remove"><i class="fa fa-times"></i></a>

                    </div>
                    <h4 class="panel-title">Permission List</h4>
                </div>


                <div class="panel-body">

                    {{ Form::open(array('route' => 'bulkAction', 'class' =>'form', 'method' =>'POST')) }}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <div class="btn-group">

                                    @if(!Input::has('trash'))

                                        @if(Permission::check('allow-to-add-permission'))
                                            <a class="btn btn-sm btn-info" href="#modal-dialog" data-toggle="modal">
                                                <i class="fa fa-plus"></i> Add</a>
                                        @endif

                                        @if(Permission::check('allow-bulk-active'))
                                            <button type="submit" name="action" value="active"
                                                    class="btn btn-sm btn-success"><i class="fa fa-check"></i> Activate
                                            </button>
                                        @endif

                                        @if(Permission::check('allow-bulk-deactive'))
                                            <button type="submit" name="action" value="deactive"
                                                    class="btn btn-sm btn-warning"><i class="fa fa-ban"></i> Deactive
                                            </button>
                                        @endif

                                        @if(Permission::check('allow-bulk-delete'))
                                            <button type="submit" name="action" value="delete"
                                                    class="btn btn-sm btn-danger"><i class="fa fa-times"></i> Delete
                                            </button>
                                        @endif

                                        @if(Permission::check('allow-to-view-trash'))
                                            <a href="{{URL::full().'?trash=1' }}" class="btn btn-sm btn-inverse"><i
                                                        class="fa fa-trash-o"></i> Trash (<?php echo $data['count'];?>)</a>
                                        @endif

                                    @else


                                        <a class="btn btn-sm btn-info" href="{{URL::route('permissions')}}"><i
                                                    class="fa fa-angle-double-left"></i> Back</a>

                                        @if(Permission::check('allow-permanent-delete'))
                                            <button type="submit" name="action" value="forcedelete"
                                                    class="btn btn-sm btn-danger"><i class="fa fa-times"></i> Permanent
                                                Delete
                                            </button>
                                        @endif

                                        @if(Permission::check('allow-to-restore'))
                                            <button type="submit" name="action" value="restore"
                                                    class="btn btn-sm btn-success"><i class="fa fa-share-square-o"></i>
                                                Restore
                                            </button>
                                        @endif

                                    @endif
                                </div>

                            </div>
                        </div>

                    </div>


                    <br/>

                    <div class="row">


                        <div class="table-responsive">
                            <table id="data-table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    @if(Permission::check('allow-activedeactive'))
                                        <th>Active</th>
                                    @endif
                                    <th>Created</th>
                                    <th>Updated</th>

                                    @if(Permission::check('allow-soft-delete'))
                                        @if(!Input::has('trash'))
                                            <th>Actions</th>
                                        @endif
                                    @endif
                                    <th><input id="selectall" type="checkbox"/></th>
                                </tr>
                                </thead>


                                <tbody>


                                @if(is_object($data['list']))
                                    @foreach($data['list'] as $item)
                                        <tr class="" id="{{$item->id}}">

                                            <td>{{$item->id}}</td>
                                            <td>
                                                @if(!Input::has('trash') && Permission::check('allow-ajax-edit'))
                                                    <a class="editable" data-pk="{{$item->id}}"
                                                       data-name="{{get_table_name()}}" id="edit-{{$item->id}}"
                                                       href="#"  data-editlink="<?php echo URL::route('ajax_edit'); ?>">{{$item->name}}</a>
                                                @else
                                                    {{$item->name}}
                                                @endif

                                            </td>
                                            <td>{{$item->slug}}</td>

                                            @if(Permission::check('allow-activedeactive'))

                                                <td>
                                                    <?php $exception = false;

                                                    if (in_array($item->id, core_settings('permissions')['exceptions'])) {
                                                        $exception = true;
                                                    }

                                                    ?>

                                                    @if($exception)
                                                        <input type="checkbox" data-render="switchery" class="BSswitch"
                                                               data-theme="black" checked="checked"
                                                               data-switchery="true" name="active"
                                                               data-exception="{{$exception}}" value="{{$item->id}}"
                                                               data-href="<?php echo URL::route('ajax_toggle_status'); ?>"
                                                               style="display: none;">


                                                        <!-- switch button for non-admin it will be green -->
                                                    @else
                                                            @if($item->active == 1)

                                                                <input type="checkbox" data-render="switchery" class="BSswitch"
                                                                       data-theme="green" checked="checked" data-switchery="true"
                                                                       data-pk="{{$item->id}}"
                                                                       data-href="{{URL::route('ajax_update_col')}}?name=permissions|active"
                                                                       style="display: none;">
                                                            @else

                                                                <input type="checkbox" data-render="switchery" class="BSswitch"
                                                                       data-theme="green"  data-switchery="true"
                                                                       data-pk="{{$item->id}}"
                                                                       data-href="{{URL::route('ajax_update_col')}}?name=permissions|active"
                                                                       style="display: none;">

                                                            @endif
                                                    @endif
                                                </td>
                                            @endif

                                            <td>{{Dates::showTimeAgo($item->created_at)}}</td>
                                            <td>{{Dates::showTimeAgo($item->updated_at)}}</td>

                                            @if(Permission::check('allow-soft-delete'))
                                                @if(!Input::has('trash'))
                                                    <td>

                                                        <a class="btn btn-sm btn-icon btn-circle btn-danger"
                                                           id="delete_{{$item->id}}" data-exception="{{$exception}}"
                                                           data-toggle="tooltip" data-placement="top"
                                                           data-href="<?php echo URL::route('ajax_delete'); ?>"
                                                           data-original-title="Delete" title=""><i
                                                                    class="fa fa-minus"></i></a>

                                                    </td>
                                                @endif
                                            @endif
                                            <td><input type="checkbox" class="idCheckbox" name="id[]"
                                                       value="{{$item->id}}">
                                                @if(Permission::check('allow-permanent-delete'))
                                                    @if(Input::has('trash'))
                                                        <input type="hidden" name="selctedname[]"
                                                               value="{{$item->name}}">
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                </tbody>


                            </table>
                        </div>
                    </div>

                    {{Form::hidden('table', get_table_name(), array('id' => 'table')) }}
                    {{Form::close()}}


                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-10 -->
    </div>
    <!-- end row -->




@stop

@section('page_specific_foot')

    @include('core::elements.datatable-switchery')


    <script src="<?php echo asset_path(); ?>/plugins/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="<?php echo asset_path(); ?>/permission.js"></script>

     {{ View::make('core::layout.javascript')->with('block_name', 'row_edit'); }}

@stop