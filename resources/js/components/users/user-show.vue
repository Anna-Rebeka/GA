<template>
    <div class="z-0 -mt-10 mb-6">
        <div class="relative -mx-8 mb-4">
            <img
                :src="user.banner"
                alt="banner"
                class="h-40 w-full px-1 mb-2 object-cover overflow-hidden"
            />
            <img
                :src="user.avatar"
                alt="avatar"
                class="object-cover md:w-32 md:h-32 sm:w-24 sm:h-24 h-16 w-16 mr-2 rounded-full border-4 border-white absolute bottom-0 transform -translate-x-1/2 translate-y-1/2"
                style="left: 50%"
            />
        </div>

        <div class="flex rounded-lg justify-between items-center mb-6">
            <div class="w-14 md:w-44 2xl:w-64">
                <h2 class="font-bold lg:text-2xl md:text-lg text-md">{{ user.name }}</h2>
                <p class="text-sm">Joined {{ userCreatedAt }}</p>
            </div>

            <div v-if="authUser.id == user.id" class="flex">
                <a
                    :href="userEditPath"
                    class="rounded-lg shadow border border-gray-300 py-2 px-4 mr-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                >
                    Edit profile
                </a>
            </div>
            <a
                v-else
                :href="'/chats/find/' + user.id"
                class="rounded-lg shadow border border-gray-300 py-2 px-4 mr-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
            >
                Chat
            </a>
        </div>
        <p class="text-sm float-right mb-4">
            {{ user.email }}
        </p>
        <p class="text-sm mb-4">
            {{ user.bio }}
        </p>

        <div class="w-full mt-12 border-b mb-12 rounded-full"></div>

        <div v-if="authUser.id == user.id" class="mx-auto">
            <user-assignments :authUser="authUser"></user-assignments>
            <user-events class="mt-12" :authUser="authUser"></user-events>
        </div>
    </div>
</template>

<script>
export default {
    props: ["authUser", "user", "userCreatedAt", "userEditPath"],

    mounted() {},
};
</script>

<style>
</style>