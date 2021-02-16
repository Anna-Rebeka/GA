<x-master>
    <assignment-show :assignment="{{ $assignment }}" :user="{{ $user }}"></assignment-show>
    <div class="px-8 mr-2 mb-16">
        <div class="mb-10">
            @foreach ($assignments_files as $file)
                <a class="float-left mb-1" href="/storage/{{ $file->file_path }}">{{ $file->file_name }}</a>
                @if ($file->user_id == $user->id)
                    <form action="/assignmentsFiles/{{ $file->id }}/file-delete" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="ml-6 float-left text-red-500 font-bold"> x </button>
                    </form>
                    <div class="clear-both"></div>
                @endif
            @endforeach
            {{ $assignments_files->links() }}
        </div>
        
        <form class="clear-both" action="/assignments/{{ $assignment->id }}/file-upload" method="post" enctype="multipart/form-data">
            @csrf
            <div class="float-left">
                <label class="font-bold mb-4 text-underlined" 
                    for="file_path"
                >
                    File upload
                </label>
                
                <div class="text-sm mt-4 file-upload-wrapper" data-text="Select your file!">
                    <input name="file_path" id="file_path" type="file" value="file_path">
                </div>
                @error('file_path')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="float-right">
                <label class="font-bold mb-4 text-underline" 
                    for="file_name"
                >
                    Name your file
                </label>
                <input class="border-b border-gray-400 p-2 mb-4 w-full focus:outline-none" type="text" name="file_name" id="file_name" 
                    value="" pattern="[a-zA-Z0-9_-]+">
                <br>
                <p class="text-xs mb-2">(please use only letters, numbers, "_" and "-")</p>

                @error('file_name')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror

            </div>

            <div class="mb-6 clear-both">
                <button type="submit" class="float-right text-sm bg-blue-400 text-white rounded-lg px-2 w-16 py-2 hover:bg-blue-500 mr-4 focus:outline-none">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <assignment-comments :assignment="{{ $assignment }}" :user="{{ $user }}"></assignment-comments>
    

</x-master>