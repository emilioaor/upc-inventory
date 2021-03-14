@extends('layouts.app')

@section('content')
    <product-serial-form
        :edit-data = "{{ isset($product) ? json_encode($product) : 'null' }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></product-serial-form>
@endsection
