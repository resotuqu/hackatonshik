@extends('layouts.app')

@section('title', $profileUser->fio ?? $profileUser->nickname)

@section('slot')
    @include('pages.profile.public-show-inner')
@endsection
