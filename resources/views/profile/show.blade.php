<x-master>
    <user-show 
        :auth-user="{{ $auth_user }}" 
        :user="{{ $user }}" 
        user-created-at="{{ $user_created_at }}" 
        user-edit-path="{{ $user_edit_path }}"
    >
    </user-show>
</x-master>
