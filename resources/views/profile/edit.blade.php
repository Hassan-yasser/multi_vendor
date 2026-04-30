@extends('layout.dashboard')

@section('title', 'Profile')
@section('page_title', 'Profile')

@section('content_header')
  <x-page-heading
    title="Profile"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Profile'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <div class="card-body">
      @include('profile.partials.update-profile-information-form')
    </div>
  </div>
@endsection
