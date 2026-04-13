@extends('admin.layouts.app')
@section('title', 'Liste miss')
@section('page-title', 'Misses')

@section('content')

@foreach ($candidates as $candidate )
    {{ $candidate['name'] }}
@endforeach
@endsection

@push('scripts')

@endpush