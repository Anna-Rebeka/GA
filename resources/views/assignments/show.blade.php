<x-master>
    <assignment-show :assignment="{{ $assignment }}" :user="{{ $user }}"></assignment-show>
    <div class="px-8 mr-2 mb-16">
        <div class="mb-10">
            @foreach ($assignments_files as $file)
                <a class="block mb-1" href="/storage/{{ $file->assignment_file }}">{{ $file->file_name }}</a>
            @endforeach
            {{ $assignments_files->links() }}
        </div>
        
        <form action="/assignments/{{ $assignment->id }}/file-upload" method="post" enctype="multipart/form-data">
            @csrf
            <div class="float-left">
                <label class="font-bold mb-4 text-underlined" 
                    for="assignment_file"
                >
                    File upload
                </label>
                
                <div class="text-sm mt-4 file-upload-wrapper" data-text="Select your file!">
                    <input name="assignment_file" id="assignment_file" type="file" value="assignment_file">
                </div>
                @error('assignment_file')
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