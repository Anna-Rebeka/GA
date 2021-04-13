<template>
    <div>
        <div class="p-8 mr-2 mb-8">
            <div class="float-right">
                <button
                    v-if="savedGoing && !savedGoing.includes(user.id)"
                    @click="joinEvent()"
                    class="rounded-lg py-2 px-4 mr-2 w-16 text-white text-sm bg-green-400 hover:bg-green-300 focus:outline-none"
                >
                    Join
                </button>
                <button
                    v-else
                    @click="leaveEvent()"
                    class="text-sm bg-blue-400 text-white rounded-lg w-16 py-2 hover:bg-blue-500 mr-2 focus:outline-none"
                >
                    Leave
                </button>
                <div
                    class="float-right rounded-lg w-16 px-2 text-center py-2 mr-2 text-white text-sm bg-gray-400 hover:bg-gray-500 focus:outline-none"
                    v-if="event.group.admin_id == user.id || event.host_id == user.id"
                >
                    <a :href="event.id + '/edit'" class="p-3">
                        Edit
                    </a>
                </div>
                <button
                    v-if="event.group.admin_id == user.id || event.host_id == user.id"
                    @click="checkWithUser()"
                    class="rounded-lg w-16 px-2 py-2 mr-2 text-white text-sm bg-red-400 hover:bg-red-300 focus:outline-none"
                >
                    Delete
                </button>
            </div>
            <div class="overflow-ellipsis overflow-hidden ... max-w-sm">
                <h2 class="font-bold text-2xl mb-4">{{ event.name }}</h2>
            </div>
            <div class="flex items-center justify-between w-full mt-5 mb-10 py-2 pl-5 bg-red-500 shadow text-white" v-if="cannotJoinOldEventError">
                    You can't join an event that already happened.
            </div>
            <div class="flex items-center justify-between w-full mt-5 mb-10 py-2 pl-5 bg-red-500 shadow text-white" v-if="cannotLeaveOldEventError">
                    You can't leave an event that already happened.
            </div>
            <div class="py-2 px-6 mb-2 mr-2 border-b border-gray-200 rounded">
                <p class="mb-2"><strong>Hosted by:</strong></p>
                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    {{ host }}
                </div>
            </div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mb-2 rounded border-b border-gray-200 px-6 p-4">
                    <strong>Start time:</strong>
                    <p class="bg-white p-2 rounded">
                        {{
                            new Date(event.event_time)
                                | dateFormat("DD.MM.YYYY , HH:mm")
                        }}
                    </p>
                </div>
                <div
                    class="mb-2 mr-2 rounded border-b border-gray-200 px-6 p-4"
                >
                    <strong>End time:</strong>
                    <p v-if="event.event_ending" class="bg-white p-2 rounded">
                        {{
                            new Date(event.event_ending)
                                | dateFormat("DD.MM.YYYY , HH:mm")
                        }}
                    </p>
                    <p v-else class="bg-white p-2 rounded">not set</p>
                </div>
            </div>

            <div class="py-2 px-6 mb-2 mr-2 border-b border-gray-200 rounded">
                <p class="mb-2"><strong>Location:</strong></p>
                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    {{ event.event_place }}
                </div>
            </div>
            <div
                v-if="event.description"
                class="py-2 px-6 mb-2 mr-2 border-b border-gray-200 rounded"
            >
                <p class="mb-2"><strong>Description:</strong></p>
                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    {{ event.description }}
                </div>
            </div>
            <div class="pt-6 pb-1 px-6 mr-2 border-b border-gray-200 rounded">
                <p class="mb-2"><strong>Who's coming:</strong></p>
                <div class="bg-white rounded mb-4 py-4">
                    <div v-if="savedUsers.length > 0">
                        <ul
                            v-for="goingUser in pageOfItems"
                            :key="goingUser.name"
                        >
                            <li class="flex items-center ml-2 mb-2">
                                <img
                                    class="w-10 h-10 object-cover rounded-full mr-2"
                                    :src="goingUser.avatar"
                                    alt="avatar"
                                />
                                {{ goingUser.name }}
                            </li>
                        </ul>
                    </div>
                    <div class="text-center text-sm">
                        <jw-pagination
                            :items="savedUsers"
                            @changePage="onChangePage"
                            :pageSize="10"
                        ></jw-pagination>
                    </div>
                </div>
            </div>
        </div>
        <event-comments :user="this.user" :event="this.event"></event-comments>
    </div>
</template>

<script>
export default {
    props: ["user", "going", "event", "host", "host_id"],

    data() {
        return {
            savedUsers: this.event.users,
            pageOfItems: [],
            cannotJoinOldEventError: false,
            cannotLeaveOldEventError: false,
            savedGoing: this.going,
        };
    },

    methods: {
        reload() {
            this.$forceUpdate();
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        checkWithUser() {
            if (confirm("Are you sure? This action is irreversible.")) {
                this.destroyEvent();
            }
        },

        joinEvent() {
            if(new Date(this.event.event_time) <  Date.now()){
                this.cannotJoinOldEventError = true;
                return;
            }
            axios
                .post("/events/" + this.event.id + "/join")
                .then((response) => {
                    this.savedGoing.push(this.user.id);
                    this.savedUsers.push(this.user);
                    this.reload();
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        leaveEvent() {
            if(new Date(this.event.event_time) <  Date.now()){
                this.cannotLeaveOldEventError = true;
                return;
            }
            axios
                .post("/events/" + this.event.id + "/leave")
                .then((response) => {
                    this.savedUsers = this.savedUsers.filter(function (eu) {
                        return eu.id != response.data.id;
                    });
                    this.savedGoing = this.savedGoing.filter(function (gid) {
                        return gid != response.data.id;
                    });
                    this.reload();
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        destroyEvent() {
            axios.delete("/events/" + this.event.id).then((response) => {
                window.location.href = "/events";
            });
        },
    },
};
</script>

<style>
</style>