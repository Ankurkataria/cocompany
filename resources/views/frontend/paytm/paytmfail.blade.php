@extends('frontend.layouts.app')

@section('content')
    <div class="container p-4">
        <div class="card">
            <div class="card-header alert alert-success alert-dismissible">
                <strong>Your Payment Has been failed</strong>
            </div>
            <div class="card-body" style="color: red">
                {{ $response['RESPMSG'] ?? 'something went wrong!' }}
            </div>
        </div>
    </div>
@endsection
