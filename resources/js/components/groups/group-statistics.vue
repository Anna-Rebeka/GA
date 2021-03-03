<template>
    <div>
        <div>
            <h3 class="text-center font-bold text-lg mb-3">
                Assignments and Workload
            </h3>
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
                v-for="freeUser in free_users"
                :key="freeUser.id"
                class="relative float-left w-32 h-44 py-2 mr-5 text-center shadow-md border border-gray-200"
            >
                <img
                    class="rounded-full shadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8 border-green-400 border-opacity-30"
                    :src="freeUser.avatar"
                    alt="userAvatar"
                />
                <div class="max-h-14 overflow-y-auto">
                    <a
                        class="hover:underline"
                        :href="'/profile/' + freeUser.username"
                    >
                        {{ freeUser.name }}
                    </a>
                </div>
                <p class="text-sm font-medium absolute bottom-0 right-9">
                    0.00 %
                </p>
            </div>
            <div
                v-for="stat in stats"
                :key="stat.user_id"
                class="relative float-left w-32 h-44 py-2 mr-5 text-center shadow-md border border-gray-200"
            >
                <img
                    v-if="stat.avatar != null"
                    class="rounded-full shadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8"
                    :style="
                        'border-color: rgba(0, ' +
                        (220 - Math.ceil(130 * stat.user_to_all)) +
                        ' , 0, 0.3)'
                    "
                    :src="'/storage/' + stat.avatar"
                    alt="userAvatar"
                />
                <img
                    v-else
                    class="rounded-full hadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8"
                    :style="
                        'border-color: rgba(0, ' +
                        (220 + Math.ceil(130 * stat.user_to_all)) +
                        ' , 0, 0.3)'
                    "
                    src="/img/default.jpg"
                />
                <div class="max-h-14 overflow-y-auto">
                    <a
                        class="hover:underline"
                        :href="'/profile/' + stat.username"
                    >
                        {{ stat.name }}
                    </a>
                </div>
                <p class="text-sm font-medium absolute bottom-0 right-8">
                    {{ (stat.user_to_all * 100).toFixed(2) }} %
                </p>
            </div>
        </div>
        <div class="clear-both h-10"></div>
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
        };
    },
};
</script>

<style>
</style>