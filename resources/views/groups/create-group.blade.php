<x-master>
    <div class="container mx-auto flex justify-center">
        <div class="px-16 py-8 bg-gray-200 border-gray-300 rounded-lg">
            <div class="font-bold text-lg mb-4 text-center">
                Get connected!
            </div>
            <p class="mb-4">Don't worry. You will be an administrator.</p>
            <form method="POST" action="/create-group">
                @csrf
                <div class="mb-4">
                    <label class="block mb-2 uppercase font-bold text-xs text-gray-700"
                        for="name"
                    >
                        Name
                    </label>

                    <input class="border border-gray-400 p-2 w-full"
                        type="text"
                        name="name"
                        id="name"
                        autocomplete="name"
                        autofocus
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            
                <div class="text-center">
                    <button type="submit" class="rounded-full border border-gray-300 bg-white py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        Create my own group!
                    </button>

                        @if (session('message'))
                            <div class="mt-2">
                                <p>
                                    {{ session('message') }}
                                </p>
                            </div>
                        @endif
                </div>
            </form>
        </div>
    </div>
</x-master>