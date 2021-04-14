<template>
    <div class="border-bottom relative border-gray-300 rounded-lg mb-2">
        <h3 class="text-center font-bold text-lg mb-5 text-gray-700">Chats</h3>
        <div class="relative inline-block w-44 h-10 mb-10">
            <input
                id="memberInput"
                type="text"
                name="memberInput"
                placeholder="New message to..."
                class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-full px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
            />
        </div>
        <div v-for="chat in pageOfItems" :key="chat.id">
            <a v-if="chat.latest_message" :href="'/chats/' + chat.id">
                <div
                    class="flex p-4 border-b border-b-gray-400 hover:bg-gray-200"
                >
                    <div class="mr-2">
                        <img
                            v-if="chat.users[0]"
                            :src="chat.users[0].avatar"
                            alt="avatar"
                            class="rounded-full object-cover h-15 w-15 mr-2"
                        />
                        <img
                            v-else
                            src="img/default_avatar.png"
                            alt="avatar"
                            class="rounded-full object-cover h-15 w-15 mr-2"
                        />
                    </div>
                    <div class="w-full">
                        <h5 v-if="chat.users[0]" class="font-bold mb-2 ml-4">
                            {{ chat.users[0].name }}
                        </h5>
                        <h5 v-else class="font-bold mb-2 ml-4">
                            *deleted account*
                        </h5>
                        <div
                            v-if="chat.latest_message.sender_id != user.id"
                            class="text-sm mb-3 mx-4 float-left"
                        >
                            <p v-if="chat.users[0]">
                                {{ chat.users[0].name }}:
                            </p>
                            <p v-else>*deleted account*</p>
                        </div>
                        <div v-else class="text-sm mb-3 mx-4 float-left">
                            You:
                        </div>
                        <div
                            class="text-sm mb-3 ml-4 break-words"
                            v-bind:class="{
                                'font-bold':
                                    !chat.latest_message.read &&
                                    chat.latest_message.sender_id != user.id,
                            }"
                        >
                            <p v-if="chat.latest_message.file_path">
                                *sent a file*
                            </p>
                            <p v-else-if="chat.latest_message.image_path">
                                *sent a photo*
                            </p>
                            <p
                                v-else-if="
                                    chat.latest_message.text &&
                                    chat.latest_message.text.length > 30
                                "
                            >
                                {{
                                    chat.latest_message.text.substring(0, 30) +
                                    "..."
                                }}
                            </p>
                            <p v-else>
                                {{ chat.latest_message.text }}
                            </p>
                        </div>

                        <p class="text-xs float-right">
                            {{
                                new Date(chat.latest_message.created_at)
                                    | dateFormat("HH:mm , DD.MM.YYYY")
                            }}
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="mt-28 clear-both w-full text-center text-sm">
            <jw-pagination
                :items="shownChatroom"
                @changePage="onChangePage"
                :pageSize="50"
            ></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "chatrooms"],

    data() {
        return {
            members: [],
            lettersCounter: 0,
            shownChatroom: this.chatrooms,
            pageOfItems: [],
        };
    },

    mounted() {
        if (this.user.group) {
            this.getGroupMembers();
            this.autocomplete(
                document.getElementById("memberInput"),
                this.members,
                this.lettersCounter
            );
        }
        this.shownChatroom = this.shownChatroom.sort(function (a, b) {
            return (
                new Date(b.latest_message.created_at) -
                new Date(a.latest_message.created_at)
            );
        });
        this.shownChatroom.forEach((chatroom) => {
            window.Echo.private("chatrooms." + chatroom.id).listen(
                "MessageSent",
                (e) => {
                    this.getLatestMessage(chatroom);
                }
            );
        });
    },

    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        getLatestMessage(chatroom) {
            axios
                .get("/chats/" + chatroom.id + "/latestMessage")
                .then((response) => {
                    chatroom.latest_message = response.data;
                    chatroom.latest_message.sender_id =
                        chatroom.latest_message.sender.id;
                    this.shownChatroom = this.shownChatroom.sort(function (
                        a,
                        b
                    ) {
                        return (
                            new Date(b.latest_message.created_at) -
                            new Date(a.latest_message.created_at)
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

        getGroupMembers() {
            axios
                .get("/groups/" + this.user.active_group + "/members/get")
                .then((response) => {
                    this.members = response.data;
                    this.autocomplete(
                        document.getElementById("memberInput"),
                        this.members,
                        this.lettersCounter
                    );
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        autocomplete(inp, members, counter) {
            inp.addEventListener("input", function (e) {
                if (counter < 2) {
                    counter += 1;
                    return false;
                }
                counter = 0;
                var a,
                    b,
                    i,
                    val = this.value;
                closeAllLists();
                if (!val) {
                    return false;
                }
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
                for (i = 0; i < members.length; i++) {
                    if (
                        members[i].name
                            .toLowerCase()
                            .includes(val.toLowerCase())
                    ) {
                        b = document.createElement("DIV");
                        b.setAttribute("class", "w-full h-full");
                        b.innerHTML +=
                            "<a class='block border-none w-full' href='/chats/find/" +
                            members[i].id +
                            "'>" +
                            members[i].name +
                            "</a>";
                        a.appendChild(b);
                    }
                }
            });

            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        },
    },
};
</script>

<style>
.autocomplete-items {
    position: absolute;
    z-index: 99;
    top: 100%;
    left: 0;
    right: 0;
    font-size: 14px;
    border: solid #d1d5db 0.5px;
    border-radius: 8px;
    background-color: #d1d5db;
}
.autocomplete-items div {
    padding: 8px;
    cursor: pointer;
}
.autocomplete-items div:hover {
    background-color: #6366f1;
    color: white;
}
.autocomplete-active {
    background-color: #6366f1 !important;
    color: white;
}
</style>