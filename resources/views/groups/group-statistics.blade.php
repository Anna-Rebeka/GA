<x-master>
    <group-statistics :user="{{ $user }}" :group="{{ $group }}" :stats="{{ json_encode($stats) }}" :free_users="{{ json_encode($free_users) }}"></group-statistics>
</x-master>