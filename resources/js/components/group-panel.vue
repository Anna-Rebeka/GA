<template>
    <div>
        <div class="container text-center pt-4 pb-8">
            <img
                src="/storage/groups/avatars/default.jpg"
                alt="group-avatar"
                class="rounded shadow-lg mx-auto mb-2 object-cover w-32 h-32"
            />

            <div v-if="user.group">
                <h2 class="font-bold text-2xl">{{ user.group.name }}</h2>
                <p class="text-sm text-gray-500 mb-6">group</p>
                <a
                    v-bind:class="{
                        'bg-purple-200 hover:text-white hover:bg-purple-400': newMessage,
                        'hover:text-gray-500 hover:bg-gray-100': !newMessage,
                    }"
                    class="relative block w-32 h-8 shadow border border-gray-300 rounded-lg text-black mx-auto mb-2 py-2 px-6 text-xs"
                    href="/chats"
                >
                    <div
                        v-if="newMessage"
                        class="absolute t-0 l-0 -mt-3 -ml-8 border rounded-lg bg-red-500 w-3 h-3"
                    ></div>
                    Messages
                </a>
                <a
                    class="block w-32 h-8 shadow border border-gray-300 rounded-lg mx-auto mb-2 py-2 px-6 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    :href="'/' + user.group.id + '/assignments'"
                    >Assignments
                </a>
                <a
                    class="block w-32 h-8 shadow border border-gray-300 rounded-lg mx-auto mb-2 py-2 px-6 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    :href="'/' + user.group.id + '/events'"
                    >Events
                </a>
            </div>
            <div v-else>
                <h2 class="font-bold text-2xl mb-6">No Group</h2>
                <a
                    href="/create-group"
                    class="top-6 rounded-full border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    >Create a group
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user"],

    data() {
        return {
            newMessage: false,
            howMany: 0,
            chatrooms: [],
        };
    },

    mounted() {
        this.checkForNewMessages();
        this.getAllChatrooms();
    },

    methods: {
        getAllChatrooms() {
            axios.get('/group-panel/' + this.user.group.id + '/getAllUserChatrooms', {
            }).then(response => {
                this.chatrooms = response.data;
                this.chatrooms.forEach(chatroom => {
                    window.Echo.private('chatrooms.' + chatroom.id)
                    .listen('MessageSent', (e) => {
                        this.newMessage = true;
                        this.howMany += 1;
                    });
                });
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        checkForNewMessages() {
            axios.get('/group-panel/' + this.user.group.id + '/checkForNewMessages', {
            }).then(response => {
                this.howMany = response.data[0].count;
                if(this.howMany > 0){
                    this.newMessage = true;   
                }
                else {
                    this.newMessage = false;
                }
                
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },
    },
};
</script>

<style>
</style>