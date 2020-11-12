<x-master>
  <div class="container mx-auto flex justify-center">
    <div class="px-12 py-8 bg-gray-200 border-gray-300 rounded-lg">
        <div class="font-bold text-lg mb-4 text-center">
            The more the merrier!
        </div>
        <p class="mb-4">Invite a new member by e-mail.</p>
        <form method="POST" action="/invite-member">
          @csrf
          <div class="mb-4">
            <label for="email" class="mb-2">E-Mail Address: </label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
              @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
          </div>

          <div class="text-center">
              <button type="submit" class="rounded-full border border-gray-300 bg-white py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                Send Request
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