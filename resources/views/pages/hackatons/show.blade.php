@extends('layouts.app')

@section('title', $hackaton->title)

@section('slot')
    @include('pages.hackatons.show-inner')
@endsection
