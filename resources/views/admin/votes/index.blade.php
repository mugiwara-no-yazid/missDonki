@extends('admin.layouts.app')
@section('title', 'Liste miss')
@section('page-title', 'Misses')

@section('content')

@foreach ($ranking as $candidate )
    {{ $candidate }}
@endforeach
@endsection

@push('scripts')

@endpush