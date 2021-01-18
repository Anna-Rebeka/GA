<template>
    <div class="mb-6">
        <ul>
            <li v-for="user in pageOfItems" :key="user.id" class="inline float-left mr-4">
                <a :href="'/profile/' + user.username">
                    <div class="max-w-xs lg:w-40 lg:h-60 w-20 h-30 rounded overflow-hidden shadow-lg mb-4">
                        <img class="lg:w-40 lg:h-40 w-20 h-20 object-cover" :src="user.avatar" :alt="user.username">
                        <div class="px-6 py-2 text-center">
                            <div class="font-bold text-sm mb-2">
                                {{ user.name }}
                            </div>
                        </div>
                        <p class="invisible text-gray-700 text-center mb-1 text-xs lg:visible xl:visible">
                                {{ user.email }}
                        </p>
                    </div>
                </a>
            </li>
        </ul>
        
        <div class="mt-10 clear-both w-full text-center text-sm">
            <jw-pagination :items="users" @changePage="onChangePage" :pageSize="6"></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ['users'],
    data() {
        return {
            pageOfItems: []
        };
    },
    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        }
    }
};
</script>
