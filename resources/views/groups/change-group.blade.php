<x-master>
    <change-group :user="{{ auth()->user() }}" :groups="{{ $groups }}"></change-group>
</x-master>