@extends('layouts.app')

@section('content')
    <digital-inventory-list
        :items = "{{ json_encode($digitalInventories->items()) }}"
        :total = "{{ $digitalInventories->total() }}"
    >
        <template v-slot:pagination>{{ $digitalInventories->links() }}</template>
    </digital-inventory-list>
@endsection
