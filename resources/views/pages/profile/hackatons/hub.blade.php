@extends('layouts.app')

@section('title', 'Мой хакатон: '.$hackaton->title)

@section('slot')
    @include('pages.profile.hackatons.hub-inner')
@endsection
