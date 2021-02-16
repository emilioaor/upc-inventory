@extends('layouts.app')

@section('content')
    <digital-inventory-form
        :edit-data = "{{ isset($digitalInventory) ? json_encode($digitalInventory) : 'null' }}"
        :crossover-select="true"
        :user = "{{ json_encode(Auth::user()) }}"
    ></digital-inventory-form>
@endsection
