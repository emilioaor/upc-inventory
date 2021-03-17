@extends('layouts.app')

@section('content')
    <product-serial-list
        :items = "{{ json_encode($productSerialGroups->items()) }}"
        :total = "{{ $productSerialGroups->total() }}"
    >
        <template v-slot:pagination>{{ $productSerialGroups->links() }}</template>
    </product-serial-list>
@endsection
