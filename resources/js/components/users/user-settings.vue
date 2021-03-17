<template>
    <div class="ml-8 mb-12">
        <div v-if="user.group">
            <p
                class="-ml-8 mt-4 block mb-7 font-bold text-lg text-center text-gray-700"
            >
                Settings for e-mail notifications (for this group "{{
                    user.group.name
                }}"" )
            </p>

            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-purple-800"
            >
                Notify me if there is a new:
            </p>
            <div class="mb-12">
                <div class="mb-2">
                    <input
                        type="checkbox"
                        id="whiteboard_posts"
                        value="whiteboard_posts"
                        v-model="checkedNotifications"
                    />
                    <label for="whiteboard_posts">Whiteboard post</label>
                </div>
                <div class="mb-2">
                    <input
                        type="checkbox"
                        id="assignments"
                        value="assignments"
                        v-model="checkedNotifications"
                    />
                    <label for="assignments">Assignment</label>
                </div>

                <div class="mb-2">
                    <input
                        type="checkbox"
                        id="events"
                        value="events"
                        v-model="checkedNotifications"
                    />
                    <label for="events">Event</label>
                </div>
            </div>
        </div>

        <hr class="w-full -ml-5 bg-gray-500 border-2 mb-12 rounded-full" />

        <p
            class="-ml-8 mt-4 block mb-7 font-bold text-lg text-center text-gray-700"
        >
            Settings for e-mail notifications (for all groups)
        </p>

        <div class="mb-4">
            <p class="block mb-2 uppercase font-bold text-sm text-orange-600">
                Notify me if I get an assignment:
            </p>
            <input
                type="radio"
                v-model="getting_assignment"
                id="getting_assignment"
                name="getting_assignment"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="getting_assignment"
                id="getting_assignment"
                name="getting_assignment"
                :value="false"
                checked
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-green-700"
            >
                Notify me about any updates on my assignments:
            </p>
            <input
                type="radio"
                v-model="assignment_mine"
                id="assignment_mine"
                name="assignment_mine"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="assignment_mine"
                id="assignment_mine"
                name="assignment_mine"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-green-700"
            >
                Notify me about any updates on events that I'm going to:
            </p>
            <input
                type="radio"
                v-model="event_joined"
                id="event_joined"
                name="event_joined"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="event_joined"
                id="event_joined"
                name="event_joined"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-blue-700"
            >
                Notify me about any updates on assignments that are created by
                me:
            </p>
            <input
                type="radio"
                v-model="assignment_created_by_me"
                id="assignment_created_by_me"
                name="assignment_created_by_me"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="assignment_created_by_me"
                id="assignment_created_by_me"
                name="assignment_created_by_me"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-blue-700"
            >
                Notify me about any updates on events that are created by me:
            </p>
            <input
                type="radio"
                v-model="event_created_by_me"
                id="event_created_by_me"
                name="event_created_by_me"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="event_created_by_me"
                id="event_created_by_me"
                name="event_created_by_me"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>
        <div class="clear-both"></div>

        <div class="mb-6">
            <button
                type="submit"
                @submit.prevent="submit"
                v-on:click="submit()"
                class="float-right mr-12 bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500 mr-4 focus:outline-none"
            >
                Submit
            </button>
        </div>

        <div class="clear-both"></div>

        <hr
            class="w-full mt-12 -ml-5 bg-gray-500 border-2 mb-12 rounded-full"
        />

        <button
            @click="deleteAccount()"
            class="rounded px-2 py-3 mr-2 uppercase text-white text-sm border bg-red-300 border-red-400 hover:bg-red-500 focus:outline-none"
        >
            Delete my account
        </button>

        <div v-if="deletingAccount">
            <div
                class="flex items-center justify-between w-full mt-6 mb-6 p-2 bg-yellow-400 shadow text-white"
            >
                Deleting your account is irreversible. <br />
                The groups you administer will be deleted too. <br />
                If you wish to save these groups pass your admin rights to
                another member. <br />
                You can do so by editing each group that you want to save.
            </div>

            <div class="mb-12">
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-sm text-red-500"
                    for="checkDeleting"
                >
                    Please type "yes" to delete your account.
                </label>

                <input
                    class="focus:outline-none border-b border-gray-400 p-2"
                    type="text"
                    name="checkDeleting"
                    id="checkDeleting"
                    placeholder="do you wish to proceed?"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user"],
    data() {
        return {
            fields: {},
            getting_assignment: this.user.got_assignment_notify == 1,
            assignment_mine: this.user.my_assignment_updated_notify == 1,
            event_joined: this.user.joined_event_updated_notify == 1,
            assignment_created_by_me:
                this.user.created_by_me_assignment_updated_notify == 1,
            event_created_by_me:
                this.user.created_by_me_event_updated_notify == 1,
            checkedNotifications: [],
            errors: {},
            deletingAccount: false,
        };
    },

    methods: {
        submit() {
            this.fields.got_assignment_notify = this.getting_assignment;
            this.fields.my_assignment_updated_notify = this.assignment_mine;
            this.fields.joined_event_updated_notify = this.event_joined;
            this.fields.created_by_me_assignment_updated_notify = this.assignment_created_by_me;
            this.fields.created_by_me_event_updated_notify = this.event_created_by_me;
            console.log(this.fields.my_assignment_updated_notify);
            axios
                .post("/profile/" + this.user.id + "/settings", this.fields)
                .then((response) => {
                    this.fields = {};
                })
                .catch((error) => {
                    console.log(error.message);
                });
        },

        deleteAccount() {
            if (!this.deletingAccount) {
                this.deletingAccount = true;
                return;
            }
            if (
                document
                    .getElementById("checkDeleting")
                    .value.trim()
                    .toLowerCase() != "yes"
            ) {
                return;
            }
            axios
                .delete("/profile/" + this.user.id + "/delete-profile")
                .then((response) => {
                    window.location.href = "/";
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