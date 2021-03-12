@extends('layouts.app')

@section('content')
    <product-serial-form
        :edit-data = "{{ isset($productSerial) ? json_encode($productSerial) : 'null' }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></product-serial-form>
@endsection
