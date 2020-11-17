<x-master>
    <change-group :user="{{ auth()->user() }}" :groups="{{ $groups }}" :gusers="{{ json_encode($gusers) }}"></change-group>
</x-master>