<x-master>
    <chatrooms-index user="{{ $user }}" chatrooms="{{ json_encode($chatrooms) }}" messages="{{ json_encode($messages) }}"></chatrooms-index>
</x-master>