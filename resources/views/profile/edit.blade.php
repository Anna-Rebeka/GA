<x-master>
    <div class="px-8 mr-2 mb-10 mt-10">
        <form action="{{ $user->path() }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="name"
                >
                    Name
                </label>

                <input class="focus:outline-none border-b border-gray-400 p-2 w-full" type="text" name="name" id="name" 
                    value="{{ $user->name }}">

                @error('name')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror

            </div>


            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="username"
                >
                    Username
                </label>

                <input class="focus:outline-none border-b border-gray-400 p-2 w-full" type="text" name="username" id="username" 
                value="{{ $user->username }}">

                @error('username')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="email"
                >
                    Email
                </label>

                <input class="focus:outline-none border-b border-gray-400 p-2 w-full" type="email" name="email" id="email" 
                    value="{{ $user->email }}">

                @error('email')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>


            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="avatar"
                >
                    Avatar
                </label>
                
                <div class="flex text-sm">
                    <input class="p-2 my-auto w-full" type="file" name="avatar" id="avatar" 
                        value="{{ $user->avatar }}">

                    <img src="{{ $user->avatar }}" alt="avatar" class="w-16 h-16 object-cover border-2 border-gray-400">
                </div>
                
                @error('avatar')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="banner"
                >
                    Banner
                </label>
                
                <div class="flex text-sm">
                    <input class="p-2 my-auto w-full" type="file" name="banner" id="banner" 
                        value="{{ $user->banner }}">

                    <img src="{{ $user->banner }}" alt="banner" class="w-32 h-16 object-cover border-2 border-gray-400">
                </div>
                
                @error('banner')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="bio"
                >
                    Bio
                </label>

                <input class="focus:outline-none border-b border-gray-400 p-2 w-full" type="text" name="bio" id="bio" 
                value="{{ $user->bio }}">

                @error('bio')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="password"
                >
                    Password
                </label>

                <input class="focus:outline-none border-b border-gray-400 p-2 w-full" type="password" name="password" id="password">

                @error('password')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700" 
                    for="password_confirmation"
                >
                    Password Confirmation
                </label>

                <input class="focus:outline-none border-b border-gray-400 p-2 w-full" type="password" name="password_confirmation" id="password_confirmation">

                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500 mr-4 focus:outline-none">
                    Submit
                </button>

                <a href="{{ $user->path() }}" class="hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</x-master>