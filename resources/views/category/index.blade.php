@extends('admin.layouts.admin')

@section('title', __('views.admin.category.index.title'))

@section('content')
    <div class="row">
        <a href="{{ route('admin.categories.add') }}" class="btn btn-primary" style="float: right;">Add Category</a>
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>@sortablelink('email', __('views.admin.category.index.table_header_01'))</th>
                <th>@sortablelink('email', __('views.admin.category.index.table_header_0'))</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $k => $category)

                <tr>
                    <td>{{ ($k+1) }}</td>
                    <td>{{ $category->cat_name }}</td>
                    
                    
                    
                    <td>
                        <!-- <a class="btn btn-xs btn-primary" href="{{ route('admin.products.show', [$category->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.product.index.show') }}"> 
                            <i class="fa fa-eye"></i>
                        </a>-->
                        <a class="btn btn-xs btn-info" href="{{ route('admin.category.edit', [$category->id]) }}" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.category.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>
                       
                            <a href="{{ route('admin.category.destroy', [$category->id]) }}" class="btn btn-xs btn-danger user_destroy" data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.category.index.delete') }}">
                                <i class="fa fa-trash"></i>
                            </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
           
        </div>
    </div>
@endsection