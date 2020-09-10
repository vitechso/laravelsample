@extends('admin.layouts.admin')

@section('title', __('views.admin.products.index.title', ['name' => $product->title]))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>
            

            <tr>
                <th>{{ __('views.admin.products.index.table_header_0') }}</th>
                <td>{{ $product->title }}</td>
            </tr>

            <tr>
                <th>{{ __('views.admin.products.index.table_header_1') }}</th>
                <td>
                    
                        {{ $product->description }}
                    
                </td>
            </tr>
            <tr>
                <th>{{ __('views.admin.products.index.table_header_2') }}</th>
                        
                <td>{{ $product->price }}</td>
            </tr>
            <tr>
                <th>{{ __('views.admin.products.index.table_header_3') }}</th>
                        
                <td>{{ $product->artistname }}</td>
            </tr>
            

            
            </tbody>
        </table>
    </div>
@endsection