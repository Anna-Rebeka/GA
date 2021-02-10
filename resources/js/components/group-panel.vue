<template>
    <div class="text-center pt-6">
        <a
            href="/create-group"
            class="bg-white mb-6 rounded-lg border border-gray-300 py-2 px-3 font-bold text-black text-xs hover:text-gray-500 hover:bg-gray-100"
            >+
        </a>
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
                    class="bg-white block w-32 h-8 shadow border border-gray-300 rounded-lg mx-auto mb-2 py-2 px-6 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    href="/assignments"
                    >Assignments
                </a>
                <a
                    class="bg-white block w-32 h-8 shadow border border-gray-300 rounded-lg mx-auto mb-2 py-2 px-6 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    href="/events"
                    >Events
                </a>
            </div>
            <div v-else>
                <h2 class="font-bold text-2xl mb-6">No Group</h2>
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

        window.Echo.private("user." + this.user.id + ".readMessages").listen(
            "MessagesRead",
            (e) => {
                this.checkForNewMessages();
            }
        );
    },

    methods: {
        getAllChatrooms() {
            axios
                .get("/group-panel/getAllUserChatrooms", {})
                .then((response) => {
                    this.chatrooms = response.data;
                    this.chatrooms.forEach((chatroom) => {
                        window.Echo.private("chatrooms." + chatroom.id).listen(
                            "MessageSent",
                            (e) => {
                                this.checkForNewMessages();
                            }
                        );
                    });
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        checkForNewMessages() {
            axios
                .get("/group-panel/checkForNewMessages", {})
                .then((response) => {
                    this.howMany = response.data[0].count;
                    if (this.howMany > 0) {
                        this.newMessage = true;
                    } else {
                        this.newMessage = false;
                    }
                })
                .catch((error) => {
                    if (error.response.status == 422) {
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