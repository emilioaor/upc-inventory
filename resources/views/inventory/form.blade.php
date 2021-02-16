@extends('layouts.app')

@section('content')
    <digital-inventory-form
        :edit-data = "{{ isset($digitalInventory) ? json_encode($digitalInventory) : 'null' }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></digital-inventory-form>
@endsection
