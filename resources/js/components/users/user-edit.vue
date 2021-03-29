<template>
    <div>
        <form @submit.prevent="submit">
            
            
            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="name"
                >
                    Name
                </label>

                <input
                    class="border-b border-gray-400 p-2 w-full"
                    type="text"
                    name="name"
                    id="name"
                    v-model="shownUser.name"
                />
            </div>

            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="username"
                >
                    Username
                </label>

                <input
                    class="border-b border-gray-400 p-2 w-full"
                    type="text"
                    name="username"
                    id="username"
                    v-model="shownUser.username"
                />
            </div>

            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="email"
                >
                    Email
                </label>

                <input
                    class="border-b border-gray-400 p-2 w-full"
                    type="email"
                    name="email"
                    id="email"
                    v-model="shownUser.email"
                />
            </div>

            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="avatar"
                >
                    Avatar
                </label>

                <div class="flex">
                    <input
                        class="text-sm p-2 my-auto w-full"
                        type="file"
                        accept=".jpg, .jpeg, .png"
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
                <p class="flex items-center justify-between w-full my-5 p-2 bg-red-500 shadow text-white" v-if="avatarTooBig">Please choose a picture under 5MB.</p>
            </div>

            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="banner"
                >
                    Banner
                </label>

                <div class="flex">
                    <input
                        class="text-sm p-2 my-auto w-full"
                        type="file"
                        name="banner"
                        id="banner"
                        ref="banner"
                        v-on:change="handleBannerUpload()"
                    />

                    <img
                        :src="banner"
                        alt="banner"
                        class="w-32 h-16 object-cover border-2 border-gray-400"
                    />
                </div>

                <p class="flex items-center justify-between w-full my-5 p-2 bg-red-500 shadow text-white" v-if="bannerTooBig">Please choose a picture under 5MB.</p>

            </div>

            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="bio"
                >
                    Bio
                </label>

                <input
                    class="border-b border-gray-400 p-2 w-full"
                    type="text"
                    name="bio"
                    id="bio"
                    v-model="shownUser.bio"
                />
            </div>

            <div>
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="password"
                >
                    Password
                </label>

                <input
                    v-model="newPassword"
                    class="border-b border-gray-400 p-2 w-full"
                    type="password"
                    name="password"
                    id="password"
                />
            </div>

            <div class="mb-6">
                <label
                    class="mt-4 block mb-2 uppercase font-bold text-xs text-gray-700"
                    for="password_confirmation"
                >
                    Password Confirmation
                </label>

                <input
                    v-model="confirmPassword"
                    class="border-b border-gray-400 p-2 w-full"
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                />
            </div>

            <div
                class="flex items-center justify-between w-full mb-10 p-2 bg-red-500 shadow text-white"
                v-if="passwordsDoNotMatch"
            >
                Passwords do not match.
            </div>

            <div class="mb-6">
                <button
                    type="submit"
                    class="bg-blue-400 text-white rounded py-2 px-4 hover:bg-blue-500 mr-4 focus:outline-none"
                >
                    Submit
                </button>

                <a :href="user_path" class="hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ["user", "user_path"],

    data() {
        return {
            shownUser: this.user,
            newPassword: null,
            confirmPassword: null,
            avatar: this.user.avatar,
            banner: this.user.banner,
            passwordsDoNotMatch: false,
            avatarTooBig: false,
            bannerTooBig: false,
        };
    },
    methods: {
        handleAvatarUpload() {
            if(this.$refs.avatar.files[0].size > 5000000){
                this.avatarTooBig = true;
                return;
            }
            this.avatarTooBig = false;
            this.avatar = URL.createObjectURL(this.$refs.avatar.files[0]);
        },

        handleBannerUpload() {
            if(this.$refs.banner.files[0].size > 5000000){
                this.bannerTooBig = true;
                return;
            }
            this.bannerTooBig = false;
            this.banner = URL.createObjectURL(this.$refs.banner.files[0]);
        },

        submit() {
            var formData = new FormData();
            if (this.newPassword || this.confirmPassword) {
                if (this.newPassword != this.confirmPassword) {
                    this.passwordsDoNotMatch = true;
                    return;
                } else {
                    this.passwordsDoNotMatch = false;
                    formData.append("password", this.newPassword);
                    formData.append(
                        "password_confirmation",
                        this.confirmPassword
                    );
                }
            }

            if (this.avatar != this.user.avatar) {
                formData.append("avatar", this.$refs.avatar.files[0]);
            }

            if (this.banner != this.user.banner) {
                formData.append("banner", this.$refs.banner.files[0]);
            }

            if (this.shownUser.bio) {
                formData.append("bio", this.shownUser.bio);
            }

            formData.append("name", this.shownUser.name);
            formData.append("username", this.shownUser.username);
            formData.append("email", this.shownUser.email);

            axios
                .post(this.user_path + "/edit", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    window.location.href = response.data;
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