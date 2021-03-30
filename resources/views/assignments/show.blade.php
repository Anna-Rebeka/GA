<x-master>
    <assignment-show :assignment="{{ $assignment }}" :user="{{ $user }}" author="{{ $author }}" :assignment_users_ids="{{ $assignment_users_ids }}"></assignment-show>
    <assignment-comments :assignment="{{ $assignment }}" :user="{{ $user }}"></assignment-comments>
</x-master>