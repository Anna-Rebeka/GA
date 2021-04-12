<template>
    <div>
        <p
            class="-ml-8 mt-4 block mb-5 font-bold text-lg text-center text-gray-700"
        >
            Edit "{{group.name}}" group 
        </p>
        <div @submit.prevent="submit" enctype="multipart/form-data">
            <div class="mb-8">
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="name"
                >
                    Name
                </label>

                <input
                    class="focus:outline-none border-b border-gray-400 p-2 w-full"
                    type="text"
                    name="name"
                    id="name"
                    :placeholder="group.name"
                    v-model="groupEditFields.name"
                />
            </div>
            <div class="mb-8">
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="avatar"
                >
                    Avatar
                </label>

                <div class="flex text-sm -ml-2">
                    <input
                        class="p-2 my-auto w-full"
                        type="file"
                        name="avatar"
                        id="avatar"
                        ref="avatar"
                        accept=".jpg, .jpeg, .png"
                        v-on:change="handleAvatarUpload()"
                    />
                    <img
                        :src="avatar"
                        alt="avatar"
                        class="w-16 h-16 object-cover border-2 border-gray-400"
                    />
                </div>
                <p
                    class="flex items-center justify-between w-full my-5 p-2 bg-red-500 shadow text-white"
                    v-if="avatarTooBig"
                >
                    Please choose a picture under 5MB.
                </p>
            </div>

            <div class="mb-8" v-if="user.id == group.admin_id">
                <p class="text-sm text-gray-600">You can't leave this group unless you pass on your admin rights.</p>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="admin_id"
                >
                    Pick a new admin
                </label>
                <select
                    name="admin_id"
                    id="admin_id"
                    v-model="groupEditFields.admin_id"
                    class="border-b border-gray-400"
                >
                    <option :value="user.id">{{ user.name }}</option>
                    <option
                        v-for="member in members"
                        :key="member.id"
                        :value="member.id"
                    >
                        {{ member.name }}
                    </option>
                </select>
            </div>

            <div v-if="groupEditFields.admin_id && groupEditFields.admin_id != user.id">
                <div
                    class="flex items-center justify-between w-full mb-4 p-2 bg-yellow-400 shadow text-white"
                >
                    Passing your admin rights is irreversible.
                </div>

                <div class="mb-12">
                    <label
                        class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                        for="checkAdminChange"
                    >
                        Please type "yes" to pass your admin rights.
                    </label>

                    <input
                        class="focus:outline-none border-b border-gray-400 p-2"
                        type="text"
                        name="checkAdminChange"
                        id="checkAdminChange"
                        placeholder="do you wish to proceed?"
                    />
                </div>
            </div>

            <div
                class="flex items-center justify-between w-full mb-10 p-2 bg-red-500 shadow text-white"
                v-if="groupEditFields.admin_id != user.id && adminChangeFailed"
            >
                Please type "yes" if you wish to pass your admin rights.
            </div>

            <div
                class="flex items-center justify-between w-full mb-10 p-2 bg-red-500 shadow text-white"
                v-if="!groupEditFields.admin_id && !groupEditFields.name && emptyEditSubmitted"
            >
                There are no changes to submit.
            </div>

            <div class="mb-6">
                <button
                    type="submit"
                    @submit.prevent="submit"
                    v-on:click="submit()"
                    class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500 mr-4 focus:outline-none"
                >
                    Submit
                </button>
            </div>

            <div class="w-full mt-12 border-b mb-12 rounded-full"></div>

            <button
                @click="deleteGroup()"
                class="rounded px-2 py-3 mr-2 mb-8 uppercase text-white text-sm border bg-red-300 border-red-400 hover:bg-red-500 focus:outline-none"
            >
                Delete this group
            </button>

            <div v-if="deletingGroup">
                <div
                    class="flex items-center justify-between w-full mt-6 mb-6 p-2 bg-yellow-400 shadow text-white"
                >
                    Deleting a group is irreversible. <br />
                    If you wish to preserve this group you have to pass your admin rights <br />
                    to another member BEFORE continuing. <br />
                    Otherwise everything will be lost.
                </div>

                <div class="mb-12">
                    <label
                        class="mt-4 block mb-2 uppercase font-bold text-sm text-red-500"
                        for="checkDeleting"
                    >
                        Please type "yes" to delete this group.
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
    </div>
</template>

<script>
export default {
    props: ["user", "group", "members"],
    data() {
        return {
            groupEditFields: [],
            avatar: this.group.avatar,
            changedAvatar: false,
            emptyEditSubmitted: false,
            adminChangeFailed: false,
            avatarTooBig: false,
            deletingGroup: false,
        };
    },

    methods: {
        handleAvatarUpload() {
            if (this.$refs.avatar.files[0].size > 5000000) {
                this.avatarTooBig = true;
                return;
            }
            this.avatarTooBig = false;
            this.avatar = URL.createObjectURL(this.$refs.avatar.files[0]);
            this.changedAvatar = true;
            this.emptyEditSubmitted = false;
        },

        submit() {
            if (
                !this.groupEditFields.name &&
                !this.changedAvatar &&
                !this.groupEditFields.admin_id
            ) {
                this.emptyEditSubmitted = true;
                return;
            }
            var formData = new FormData();
            if (this.avatar != this.group.avatar) {
                formData.append("avatar", this.$refs.avatar.files[0]);
            }
            if (this.groupEditFields.name) {
                formData.append("name", this.groupEditFields.name);
            } else {
                formData.append("name", this.group.name);
            }
            if (this.groupEditFields.admin_id && this.groupEditFields.admin_id != this.user.id) {
                var checkAdminChange = document.getElementById(
                    "checkAdminChange"
                ).value;
                if (
                    checkAdminChange == null ||
                    checkAdminChange.trim().toLowerCase() != "yes"
                ) {
                    this.adminChangeFailed = true;
                    return;
                }
                formData.append("admin_id", this.groupEditFields.admin_id);
            } else {
                formData.append("admin_id", this.user.id);
            }

            axios
                .post("/groups/" + this.group.id + "/edit-group", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    window.location.href = "/profile/" + this.user.username;
                })
                .catch((error) => {
                    console.log(error.message);
                });
        },

        deleteGroup() {
            if (!this.deletingGroup) {
                this.deletingGroup = true;
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
                .delete("/groups/" + this.group.id + "/destroy")
                .then((response) => {
                    window.location.href = "/dashboard";
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