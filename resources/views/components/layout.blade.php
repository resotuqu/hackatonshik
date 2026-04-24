@props(['title' => config('app.name')])

@extends('layouts.app', ['title' => $title])

@section('slot')
    {{ $slot }}
@endsection
