<template>
    <div class="relative">
        <select
            v-on:click="selected()"
            id="groupSelect"
            class="inline-block rounded-lg bg-white border border-gray-300 text-gray-700 px-4 pr-8 h-8 mr-2 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
        >
            <option v-for="group in groups" :key="group.id" :value="group.id">
                {{ group.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    props: ["user"],
    data() {
        return {
            select: null,
            lastSelectValue: null,
            groups: null,
        };
    },

    mounted() {
        this.getUserGroups();
    },

    methods: {
        getUserGroups() {
            axios
                .get("/" + this.user.username + "/groups")
                .then((response) => {
                    this.groups = response.data;
                    if (this.groups.length > 0) {
                        this.select = document.getElementById("groupSelect");
                        this.lastSelectValue = document.getElementById(
                            "groupSelect"
                        ).value;
                    }
                    if (this.user.group) {
                        this.groups.unshift(this.user.group);
                        this.lastSelectValue = this.user.group.id;
                    }
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                        console.log(this.groups);
                    }
                    console.log(error.message);
                });
        },

        selected() {
            if (this.lastSelectValue == null || this.select == null) {
                return;
            }
            if (this.lastSelectValue != this.select.value) {
                this.lastSelectValue = this.select.value;
                this.changeActiveGroup(this.lastSelectValue);
            }
        },

        changeActiveGroup(groupId) {
            axios
                .patch("/activate-group/" + groupId)
                .then((response) => {
                    location.reload();
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