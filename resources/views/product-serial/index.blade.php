@extends('layouts.app')

@section('content')
    <product-serial-list
        :items = "{{ json_encode($productSerials->items()) }}"
        :total = "{{ $productSerials->total() }}"
    >
        <template v-slot:pagination>{{ $productSerials->links() }}</template>
    </product-serial-list>
@endsection
