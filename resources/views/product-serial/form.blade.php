@extends('layouts.app')

@section('content')
    <product-serial-form
        :edit-data = "{{ isset($productSerialGroup) ? json_encode($productSerialGroup) : 'null' }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></product-serial-form>
@endsection
