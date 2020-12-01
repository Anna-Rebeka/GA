<x-master>
    <group-events :user="{{ $user }}"  :eusers="{{ json_encode($eusers) }}" :events="{{ $events }}"></group-events>
</x-master>