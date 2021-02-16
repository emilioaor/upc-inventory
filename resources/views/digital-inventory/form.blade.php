@extends('layouts.app')

@section('content')
    <digital-inventory-form
        :edit-data = "{{ isset($digitalInventory) ? json_encode($digitalInventory) : 'null' }}"
        :crossover-select="true"
    ></digital-inventory-form>
@endsection
