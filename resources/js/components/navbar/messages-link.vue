<template>
    <div class="z-30 z-30 xl:w-20 xl:h-20 w-14 h-14 fixed bottom-4 right-6">
        <a
            v-bind:class="{
                'bg-purple-200 hover:text-white hover:bg-purple-400': newMessage,
                'hover:text-gray-500 hover:bg-gray-100': !newMessage,
            }"
            class="shadow border border-gray-300 text-black text-xs font-bold text-gray-700 rounded-full bg-white flex items-center justify-center font-mono"
            style="font-size: 20px"
            href="/chats"
        >
            <div
                v-if="newMessage"
                class="fixed xl:bottom-20 bottom-16 right-8 rounded-full bg-red-500 w-3 h-3"
            ></div>
            <img src="/img/messages.png" alt="messages" />
        </a>
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