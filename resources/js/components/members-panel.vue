<template>
    <div class="container text-center pb-4 pt-7">
        <h2 class="font-bold text-2xl mb-4">Group members</h2>
        <ul v-if="members" class="w-full max-w-md mb-2">
            <li
                v-for="member in visibleMembers"
                :key="member.name"
                class="p-4 mb-1 flex justify-between items-center"
            >
                <a :href="'/profile/' + member.username">
                    <div class="flex items-center">
                        <img
                            class="w-10 h-10 object-cover rounded-full"
                            :src="member.avatar"
                            :alt="member.name"
                        />
                        <p
                            class="hover:underline ml-2 text-left font-semibold font-sans tracking-wide"
                        >
                            {{ member.name }}
                        </p>
                    </div>
                </a>
            </li>
        </ul>
        <p v-else>No members</p>

        <div class="flex w-full justify-between">
            <a
                v-if="user.id == group.admin_id"
                :href="'/groups/' + group.id + '/invite-member'"
                class="bg-white w-24 float-left m-2 shadow border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 rounded-lg"
                >Ivite
            </a>
            <a
                v-if="members.length > 0"
                :href="'/groups/' + group.id + '/all-members/'"
                class="bg-white w-24 float-right m-2 shadow border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 rounded-lg"
                >Show all
            </a>
            <button
                v-if="user.id != group.admin_id"
                @click="checkWithUserPanel()"
                class="bg-white w-24 float-left m-2 shadow border border-red-400 py-2 px-4 text-white text-xs bg-red-300 hover:bg-red-500 rounded-lg focus:outline-none"
                :href="'/groups/' + group.id + '/group-statistics'"
            >
                Leave
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "members", "group"],

    data() {
        return {
            visibleMembers: null,
        };
    },

    mounted() {
        this.visibleMembers = this.members.sort(() => Math.random() - 0.5);
        if (this.visibleMembers.length > 3) {
            this.visibleMembers = this.visibleMembers.slice(0, 4);
        }
    },

    methods: {
        checkWithUserPanel() {
            var retVal = prompt(
                "Are you sure you want to leave this group?",
                'type "yes" to continue'
            );
            if (retVal.trim().toLowerCase() == "yes") {
                this.excludeUserFromGroupPanel();
            }
        },

        excludeUserFromGroupPanel(){
            axios.delete("/groups/" + this.group.id + "/exclude-member/" + this.user.id).then((response) => {
                window.location.href = "/dashboard";
            });
        },

    },
};
</script>

<style>
</style>