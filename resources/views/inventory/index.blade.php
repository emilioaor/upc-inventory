@extends('layouts.app')

@section('content')
    <inventory-list
        :items = "{{ json_encode($digitalInventories->items()) }}"
        :total = "{{ $digitalInventories->total() }}"
    >
        <template v-slot:pagination>{{ $digitalInventories->links() }}</template>
    </inventory-list>
@endsection
