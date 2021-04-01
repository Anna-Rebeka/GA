<template>
    <div class="py-1">
        <div class="container mx-auto flex justify-between">
            <div class="container flex justify-between">
                <a :href="route_dashboard" class="mr-3">
                    <img src="/img/logo.png" width="45px" alt="logo" />
                </a>
                <group-selection class="mt-1" :user="auth"></group-selection>
            </div>
            <div class="ml-2">
                <a :href="'/profile/' + auth.id + '/settings'" class="w-9"
                    ><img
                        class="w-10 border border-gray-300 rounded-full"
                        src="/img/settings.png"
                        alt="edit"
                    />
                </a>
            </div>
            <form action="/logout" method="post">
                <input type="hidden" name="_token" :value="csrf" />
                <button
                    class="shadow w-22 h-10 text-center relative inline-flex justify-center rounded-full border border-gray-300 ml-3 px-4 py-2 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                >
                    Logout
                </button>
            </form>
        </div>
        <a
            :href="'/' + user.username + '/notes'"
            class="z-40 xl:w-20 xl:h-20 w-14 h-14 fixed bottom-4 left-6 hover:text-gray-500 hover:bg-gray-100 shadow border border-gray-300 text-black text-xs font-bold text-gray-700 rounded-full bg-white flex items-center justify-center font-mono"
            style="font-size: 20px"
        >
            <img src="/img/notes.png" alt="notes" />
        </a>
        <messages-link :user="user"></messages-link>
    </div>
</template>

<script>
export default {
    props: ["user", "route_dashboard"],
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            userActiveGroup: null,
            auth: this.user,
        };
    },

    mounted() {
        this.getUsersActiveGroup();
    },

    methods: {
        getUsersActiveGroup() {
            axios
                .get("/" + this.user.username + "/active-group")
                .then((response) => {
                    this.auth.group = response.data;
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