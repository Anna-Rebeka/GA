<template>
    <div></div>
    <!--
    <div>
        <form @submit.prevent="submit" enctype="multipart/form-data">
        <input type="hidden" name="_token" :value="csrf" /> 

        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="name"
            >
                Name
            </label>

            <input class="border border-grey-400 p-2 w-full" type="text" name="name" id="name" 
                :value="user.name"
                v-model="fields.name"
            >

                <p class="text-red-500 text-xs mt-2">error message</p>

        </div>


        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="username"
            >
                Username
            </label>

            <input class="border border-grey-400 p-2 w-full" type="text" name="username" id="username" 
                :value="user.username"
                v-model="fields.username"
            >

            <p class="text-red-500 text-xs mt-2">error message</p>

        </div>
        
        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="email"
            >
                Email
            </label>

            <input class="border border-grey-400 p-2 w-full" type="email" name="email" id="email" 
                :value="user.email"
                v-model="fields.email"
            >

            <p class="text-red-500 text-xs mt-2">error message</p>
        </div>


        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="avatar"
            >
                Avatar
            </label>
            
            <div class="flex">
                <input class="border border-grey-400 p-2 my-auto w-full" type="file" name="avatar" id="avatar"
                >

                <img :src="user.avatar" alt="avatar" class="w-16 h-16 object-cover border-2 border-grey-400">
            </div>
            
            <p class="text-red-500 text-xs mt-2">error message</p>

        </div>

        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="banner"
            >
                Banner
            </label>
            
            <div class="flex">
                <input class="border border-grey-400 p-2 my-auto w-full" type="file" name="banner" id="banner">

                <img :src="user.banner" alt="banner" class="w-32 h-16 object-cover border-2 border-grey-400">
            </div>
            
            <p class="text-red-500 text-xs mt-2">error message</p>

        </div>

        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="bio"
            >
                Bio
            </label>

            <input class="border border-grey-400 p-2 w-full" type="text" name="bio" id="bio" 
                :value="user.bio">

            <p class="text-red-500 text-xs mt-2">error message</p>

        </div>

        <div>
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="password"
            >
                Password
            </label>

            <input class="border border-grey-400 p-2 w-full" type="password" name="password" id="password">

            <p class="text-red-500 text-xs mt-2">error message</p>

        </div>

        <div class="mb-6">
            <label class="mt-4 block mb-2 uppercase font-bold text-xs text-grey-700" 
                for="password_confirmation"
            >
                Password Confirmation
            </label>

            <input class="border border-grey-400 p-2 w-full" type="password" name="password_confirmation" id="password_confirmation">

            <p class="text-red-500 text-xs mt-2">error message</p>

        </div>

        <div class="mb-6">
            <button type="submit" class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500 mr-4 focus:outline-none">
                Submit
            </button>

            <a :href="userPath" class="hover:underline">Cancel</a>
        </div>
    </form>    
    </div>
    -->
</template>

<script>
export default {
    props: ['user', 'userPath'],
    
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            fields: {},
            errors: {},
        };
    },
    methods: {
        submit() {
            axios.post('/events', this.fields).then(response => {
                this.fields = {};
                this.createNewEvent = false;
                this.newEventCreated = true;
                this.events.unshift(response.data);    
                this.eusers[response.data.id] = [response.data.host_id];

            }).catch(error => {
                console.log(error.message);
            });
        },
    },
}
</script>

<style>

</style>