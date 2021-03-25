<template>
    <div>
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
                    v-model="fields.name"
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
                        v-on:change="handleAvatarUpload()"
                    />
                    <img
                        :src="avatar"
                        alt="avatar"
                        class="w-16 h-16 object-cover border-2 border-gray-400"
                    />
                </div>
            </div>

            <div class="mb-8">
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="admin_id"
                >
                    Pick a new admin
                </label>
                <select
                    name="admin_id"
                    id="admin_id"
                    v-model="fields.admin_id"
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

            <div v-if="fields.admin_id && fields.admin_id != user.id">
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
                v-if="fields.admin_id != user.id && adminChangeFailed"
            >
                Please type "yes" if you wish to pass your admin rights.
            </div>

            <div
                class="flex items-center justify-between w-full mb-10 p-2 bg-red-500 shadow text-white"
                v-if="!fields.admin_id && !fields.name && emptyEditSubmitted"
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
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "group", "members"],
    data() {
        return {
            fields: [],
            avatar: this.group.avatar,
            changedAvatar: false,
            emptyEditSubmitted: false,
            adminChangeFailed: false,
        };
    },

    methods: {
        handleAvatarUpload() {
            this.avatar = URL.createObjectURL(this.$refs.avatar.files[0]);
            this.changedAvatar = true;
            this.emptyEditSubmitted = false;
        },

        submit() {
            if (
                !this.fields.name &&
                !this.changedAvatar &&
                !this.fields.admin_id
            ) {
                this.emptyEditSubmitted = true;
                return;
            }
            var formData = new FormData();
            if (this.avatar != this.group.avatar) {
                formData.append("avatar", this.$refs.avatar.files[0]);
            }
            if (this.fields.name) {
                formData.append("name", this.fields.name);
            } else {
                formData.append("name", this.group.name);
            }
            if (this.fields.admin_id && this.fields.admin_id != this.user.id) {
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
                formData.append("admin_id", this.fields.admin_id);
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
    },
};
</script>

<style>
</style>