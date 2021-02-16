<template>
    <div>
        <h5
            v-for="chatUser in users"
            :key="chatUser.id"
            class="text-lg font-bold text-center h-14 w-full -mt-4 mb-6"
        >
            {{ chatUser.name }}
        </h5>
        <div
            id="chatWindow"
            ref="chat"
            class="container mb-2 pr-4 h-80 overflow-y-scroll"
        >
            <button
                v-on:click="loadOlderMessages()"
                class="bg-white text-sm text-center relative clear-both w-full h-8 px-4 pt-1 mb-4 border border-gray-300 hover:bg-purple-100 focus:outline-none focus:bg-purple-100"
            >
                Load older messages
            </button>
            <div
                v-for="message in messages"
                :key="message.id"
                class="bg-white relative clear-both w-full md:w-3/4 lg:w-1/2 px-4 pt-4 mb-2 border border-gray-300 rounded-lg"
                v-bind:class="{
                    'float-right bg-purple-100': message.sender.id == user.id,
                    'pb-6': message.sender.id != user.id,
                }"
            >
                <div>
                    <h5 class="text-xs text-gray-500 absolute bottom-0">
                        {{ message.sender.name }}
                    </h5>
                    <div class="float-left mr-2">
                        <img
                            :src="message.sender.avatar"
                            alt="avatar"
                            class="rounded-full object-cover h-15 w-15 mr-2 mb-3"
                        />
                    </div>
                    <div>
                        <div class="text-sm mb-3 break-words p-4">
                            {{ message.text }}
                            <div v-if="message.image_path">
                                <img
                                    :src="'/storage/' + message.image_path"
                                    alt="image"
                                />
                            </div>
                            <div v-if="message.file_path">
                                <a
                                    v-if="message.file_name"
                                    class="font-semibold underline"
                                    :href="'/storage/' + message.file_path"
                                >
                                    {{ message.file_name }}
                                </a>
                                <a
                                    v-else
                                    class="font-semibold underline"
                                    :href="'/storage/' + message.file_path"
                                >
                                    attached file
                                </a>
                            </div>
                        </div>

                        <p class="text-xs float-right mb-2">
                            {{
                                new Date(message.created_at)
                                    | dateFormat("HH:mm , DD.MM.YYYY")
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div v-on:submit.prevent="submit()" class="mb-4 clear-both">
            <div
                class="bg-white relative h-24 w-full px-4 pt-4 mt-4 mb-2 border border-gray-300 rounded-lg"
            >
                <textarea
                    id="messageArea"
                    name="text"
                    placeholder="New message..."
                    maxlength="1000"
                    class="w-full resize-none focus:outline-none"
                >
                </textarea>
                <div class="absolute bottom-2 right-0">
                    <button
                        v-on:submit.prevent="submit()"
                        @click="submit()"
                        id="sendImage"
                        type="submit"
                        class="text-sm bg-blue-400 text-white rounded-full w-10 px-2 py-2 mr-2 hover:bg-blue-500 focus:outline-none"
                    >
                        <img src="/img/send_icon.png" alt="send" class="w-8" />
                    </button>
                </div>
            </div>
            <button @click="uploadImage = !uploadImage" class="relative">
                <img class="h-16" src="/img/image.png" alt="" srcset="" />
            </button>
            <button @click="uploadFile = !uploadFile" class="relative mb-5">
                <img class="h-16" src="/img/file.png" alt="" srcset="" />
            </button>
            <div v-if="uploadImage" class="mb-6">
                <div class="relative">
                    <label class="font-bold mb-4 text-underlined" for="image">
                        Select a photo
                    </label>
                    <div
                        class="text-sm mt-4 file-upload-wrapper"
                        data-text="Select a photo!"
                    >
                        <input
                            type="file"
                            id="image"
                            ref="image"
                            v-on:change="handleImageUpload()"
                        />
                    </div>
                </div>
            </div>

            <div class="clear-both mb-6"></div>
            <div v-if="uploadFile">
                <div class="relative mb-2 lg:float-left">
                    <label
                        class="font-bold mb-4 text-underlined"
                        for="file_path"
                    >
                        Select a file
                    </label>
                    <div
                        class="text-sm mt-4 file-upload-wrapper"
                        data-text="Select your file!"
                    >
                        <input
                            type="file"
                            id="file"
                            ref="file"
                            v-on:change="handleFileUpload()"
                        />
                    </div>
                </div>
                <div class="relative mt-5 mb-2 lg:mt-0 lg:ml-6 lg:float-left">
                    <label
                        class="font-bold mb-4 text-underline"
                        for="file_name"
                    >
                        Name your file
                    </label>
                    <br />
                    <input
                        id="file_name"
                        class="border-b border-gray-400 p-2 mb-4 w-64 focus:outline-none"
                        type="text"
                        name="file_name"
                        value=""
                        pattern="[a-zA-Z0-9_-]+"
                    />
                    <br />
                    <p class="text-xs mb-2">(max 1000 chars)</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "chatroom"],
    data() {
        return {
            messages: [],
            users: [],
            newMessage: "",
            image: "",
            uploadImage: false,
            uploadFile: false,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
        };
    },

    mounted() {
        this.getMessages();
        this.getUsers();
        document
            .getElementById("messageArea")
            .addEventListener("keypress", this.submitOnEnter);

        window.Echo.private("chatrooms." + this.chatroom.id).listen(
            "MessageSent",
            (e) => {
                e.message.sender = e.sender;
                this.markAsReadMessage(e.message.id);
                this.messages.push(e.message);
                this.scrollChat();
            }
        );
    },

    methods: {
        scrollChat() {
            this.$nextTick(() => {
                this.$refs.chat.scrollTop = 9999;
            });
        },

        getUsers() {
            axios
                .get("/chats/" + this.chatroom.id + "/users/" + this.user.id)
                .then((response) => {
                    this.users = response.data;
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        getMessages() {
            var div = this.chatWindow;
            axios
                .get("/chats/" + this.chatroom.id + "/messages")
                .then((response) => {
                    this.messages = response.data.reverse();
                    this.scrollChat();
                    this.markAsReadMessages();
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        loadOlderMessages() {
            axios
                .get(
                    "/chats/" +
                        this.chatroom.id +
                        "/loadOlderMessages/" +
                        this.messages.length,
                    {}
                )
                .then((response) => {
                    this.messages = response.data
                        .reverse()
                        .concat(this.messages);
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        markAsReadMessages() {
            axios
                .patch("/chats/" + this.chatroom.id + "/readAll")
                .then((response) => {})
                .catch((error) => {
                    console.log(error.message);
                });
        },

        markAsReadMessage(messageId) {
            axios
                .patch(
                    "/chats/" + this.chatroom.id + "/readMessage/" + messageId
                )
                .then((response) => {})
                .catch((error) => {
                    console.log(error.message);
                });
        },

        submitOnEnter(event) {
            if (event.which === 13) {
                this.submit();
            }
        },

        handleImageUpload() {
            this.image = this.$refs.image.files[0];
        },

        handleFileUpload() {
            this.file = this.$refs.file.files[0];
        },

        submit() {
            var messageArea = document.getElementById("messageArea").value;

            if (messageArea == "" && !this.image && !this.file) {
                return;
            }
            document.getElementById("messageArea").value = "";
            
            var formData = new FormData();
            if (this.image) {
                formData.append("image", this.image);
                document.getElementById("image").value = "";
            }
            if (this.file) {
                formData.append("file", this.file);
                var fileName = document.getElementById("file_name").value;
                document.getElementById("file").value = "";
                if (fileName.trim() != "") {
                    formData.append("file_name", fileName);
                    document.getElementById("file_name").value = "";
                }
            }
            formData.append("text", messageArea);
            formData.append("chatroom_id", this.chatroom.id);
            formData.append("chatroom", this.chatroom);
            
            axios
                .post("/chats/" + this.chatroom.id + "/send", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    response.data["sender"] = this.user;
                    this.messages.push(response.data);
                    document.getElementById("messageArea").value = "";
                    this.uploadImage = false;
                    this.uploadFile = false;
                    this.$nextTick(() => {
                        this.$refs.chat.scrollTop = 9999;
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
    },
};
</script>

<style>
</style>