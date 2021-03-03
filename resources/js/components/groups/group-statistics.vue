<template>
    <div>
        <div>
            <h3 class="text-center font-bold text-lg mb-3">Team Workload</h3>
            <p class="text-sm font-bold float-left ml-2 mr-2">%</p>
            <p class="text-xs font-bold text-blue-500 float-left">
                = users assignments
            </p>
            <p class="text-sm font-bold float-left ml-2 mr-2">/</p>
            <p class="float-left text-xs font-bold text-red-600 mb-4 mr-8">
                all assignments
            </p>
            <div class="clear-both mb-6"></div>
            <div
                v-for="statUser in pageOfItems"
                :key="statUser.id"
                class="relative float-left w-32 h-44 py-2 mr-5 text-center shadow-md border border-gray-200"
            >
                <img
                    v-if="!statUser.user_to_all"
                    class="rounded-full shadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8 border-green-400 border-opacity-30"
                    :src="avatarPath(statUser.avatar)"
                    alt="userAvatar"
                />
                <img
                    v-else
                    class="rounded-full shadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8"
                    :src="avatarPath(statUser.avatar)"
                    alt="userAvatar"
                    :style="
                        'border-color: rgba(0, ' +
                        (220 - Math.ceil(130 * statUser.user_to_all)) +
                        ' , 0, 0.3)'
                    "
                />
                <div class="max-h-14 overflow-y-auto">
                    <a
                        class="hover:underline"
                        :href="'/profile/' + statUser.username"
                    >
                        {{ statUser.name }}
                    </a>
                </div>
                <p
                    v-if="statUser.user_to_all"
                    class="text-sm font-medium absolute bottom-0 right-8"
                >
                    {{ (statUser.user_to_all * 100).toFixed(2) }} %
                </p>
                <p v-else class="text-sm font-medium absolute bottom-0 right-9">
                    0.00 %
                </p>
            </div>
        </div>
        <div class="clear-both h-10"></div>
        <div class="mt-5 clear-both w-full text-center text-sm">
            <jw-pagination :items="allUsers" @changePage="onChangePage" :pageSize="20"></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "group", "stats", "free_users"],

    data() {
        return {
            newMessage: false,
            howMany: 0,
            chatrooms: [],
            usersInfo: null,
            pageOfItems: [],
            allUsers: this.allUsers = this.free_users.concat(this.stats),
        };
    },

    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        avatarPath(oldPath) {
            var index = oldPath.lastIndexOf("/") + 1;
            var newPath = '/storage/users/avatars/'.concat(oldPath.substring(index));
            return newPath;
        },
    },
};
</script>

<style>
</style>