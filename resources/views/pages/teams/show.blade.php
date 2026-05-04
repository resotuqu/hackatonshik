@extends('layouts.app')

@section('title', $team->title)

@section('slot')
    @include('pages.teams.show-inner')
@endsection
