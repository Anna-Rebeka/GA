<x-master>
    <div class="mb-6 relative">
        <div class="relative">
            <img 
                src="{{ $user->banner }}" 
                alt="{{ $user->username }}"
                class="h-40 w-full mb-2 rounded shadow-lg object-cover overflow-hidden"
            >
            
            <img 
                src="{{ $user->avatar }}"
                alt="avatar"
                class="object-cover w-32 h-32 mr-2 rounded-full absolute bottom-0 transform -translate-x-1/2 translate-y-1/2"
                style="left: 50%"
            >
        </div>


        <div class="flex justify-between items-center mb-6">
            <div style="max-width: 270px;">
                <h2 class="font-bold text-2xl"> {{ $user->name }} </h2>
                <p class="text-sm">Joined {{ $user->created_at->diffForHumans() }}</p>
            </div>

            @if($user->id === auth()->user()->id)
                <div class="flex">
                    <a 
                        href="{{ $user->path('edit') }}"
                        class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                        Edit profile
                    </a>
                </div>
            @endif
        </div>

        <p class="text-sm">
            Lorem ipsum, dolor sit amet consectetur adipisicing elit. 
            Placeat maiores assumenda hic vel architecto quis dolorum rem officia quidem, 
            repellendus quo, eligendi aliquam, culpa ratione unde eum magni libero laborum.
        </p>


    </div>
    
    <!-- <profile-show :user="{{ $user }}" :url="'{{ $user->username }}/edit'"></profile-show> -->
    
</x-master>
