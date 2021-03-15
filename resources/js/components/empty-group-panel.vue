<template>
    <div class="text-center pt-6 z-40 relative">
        <a
            href="/create-group"
            class="bg-white mb-6 rounded-lg border border-gray-300 py-2 px-3 font-bold text-black text-xs hover:text-gray-500 hover:bg-gray-100"
            >+
        </a>
        <div class="container text-center pt-4 pb-8">
            <h2 class="font-bold text-2xl mb-6">Create a group</h2>
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