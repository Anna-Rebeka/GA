<x-master>
    <user-settings 
        :user="{{ $user }}" 
        :new_whiteboard_notify="{{ $new_whiteboard_notify }}" 
        :new_assignment_notify="{{ $new_assignment_notify }}"
        :new_event_notify="{{ $new_event_notify }}"
    >
    </user-settings>
</x-master>