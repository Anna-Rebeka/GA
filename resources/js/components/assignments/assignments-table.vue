<template>
    <div>
        <div class="space-x-2 w-full mb-4">
            <select
                id="filter"
                class="inline-block rounded-lg bg-white border border-gray-300 text-gray-700 px-4 pr-8 h-8 mr-2 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
            >
                <option value="all">All</option>
                <option value="mine">Assigned to me</option>
                <option value="toDo">In progress</option>
                <option value="free">Free</option>
                <option value="created">Created by me</option>
                <option v-if="user.id == group.admin_id" value="noAuthor">
                    Without an author
                </option>
            </select>
            <div class="md:inline-block md:mt-0 mt-2 relative text-gray-600 md:ml-0 md:w-1/3 w-48">
                <input
                    class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-8 px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
                    id="searchBar"
                    type="search"
                    name="searchBar"
                    placeholder="Search by name"
                />
                <button
                    id="searchButton"
                    type="submit"
                    class="absolute right-0 top-2 mr-4 bg-transparent focus:outline-none"
                >
                    <img
                        src="/img/search.png"
                        width="20"
                        height="20"
                        alt="search"
                    />
                </button>
            </div>
        </div>
        <p class="text-xs font-bold text-blue-500 float-left">Deadline</p>
        <p class="text-sm font-bold float-left ml-2 mr-2">/</p>
        <p class="float-left text-xs font-bold text-red-600 mb-4 mr-8">
            Appointment
        </p>
        <p class="text-xs px-1 font-medium bg-red-100 float-left">In progress</p>
        <p class="text-sm font-bold float-left ml-2 mr-2">/</p>
        <p class="text-xs px-2 font-medium bg-green-100 float-left">Done</p>

        <div class="flex flex-col clear-both">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                    class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
                >
                    <div
                        class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
                    >
                        <table
                            class="min-w-full divide-y divide-gray-200 table-fixed"
                        >
                            <thead>
                                <tr>
                                    <th
                                        scope="col"
                                        class="pl-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Date & Time
                                    </th>
                                    <th
                                        scope="col"
                                        class="pl-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        What
                                    </th>
                                    <th
                                        scope="col"
                                        class="pl-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase"
                                    >
                                        By whom
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 bg-gray-50"
                                    ></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="assignment in pageOfItems"
                                    :key="assignment.id"
                                    v-bind:class="{
                                        'bg-green-100': assignment.done,
                                        'bg-red-100': !assignment.done,
                                    }"
                                >
                                    <td
                                        class="bg-white pl-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                                    >
                                        <div
                                            class="w-32 text-sm font-bold"
                                            v-bind:class="{
                                                'text-red-600':
                                                    assignment.on_time,
                                                'text-blue-500': !assignment.on_time,
                                            }"
                                        >
                                            {{
                                                new Date(assignment.due)
                                                    | dateFormat(
                                                        "DD.MM.YYYY , HH:mm"
                                                    )
                                            }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="w-48 text-sm text-grey-900 truncate ... max-w-xs"
                                        >
                                            {{ assignment.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <p v-if="assignment.author">
                                                {{ assignment.author.name }}
                                            </p>
                                            <p v-else>*deleted account*</p>
                                        </div>
                                    </td>
                                    <td>
                                        <a
                                            :href="
                                                'assignments/' + assignment.id
                                            "
                                            class="bg-white shadow border border-gray-300 rounded-lg py-2 px-2 mr-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                                        >
                                            Details
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-10 clear-both w-full text-center text-sm">
                        <jw-pagination
                            :items="savedAssignments"
                            @changePage="onChangePage"
                            :pageSize="15"
                        ></jw-pagination>
                    </div>
                    <button
                        v-on:click="loadOlderAssignments()"
                        class="shadow w-min rounded-lg border border-gray-300 px-4 py-2 mb-2 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                    >
                        Load older
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "group", "assignments"],
    data() {
        return {
            allAssignments: this.assignments,
            savedAssignments: this.assignments,
            pageOfItems: [],
            createNewAssignment: false,
            newAssignmentCreated: false,
            filtered: "all",
            select: null,
            searchBar: null,
        };
    },

    mounted() {
        this.select = document.getElementById("filter");
        this.select.addEventListener("click", this.selected);
        document
            .getElementById("searchButton")
            .addEventListener("click", this.findAssignmentByName);
        this.searchBar = document.getElementById("searchBar");
        this.searchBar.addEventListener("keypress", this.searchOnEnter);
    },

    methods: {
        searchOnEnter(event) {
            if (event.which === 13) {
                this.savedAssignments = this.allAssignments;
                this.findAssignmentByName();
                event.preventDefault();
            }
        },

        findAssignmentByName() {
            if (this.searchBar.value == "") {
                this.savedAssignments = this.allAssignments;
                return;
            }
            var findBy = this.searchBar.value;
            this.savedAssignments = this.savedAssignments.filter(function (e) {
                return e.name.toLowerCase().includes(findBy.toLowerCase());
            });
        },

        selected() {
            if (this.searchBar.value != "") {
                this.savedAssignments = this.allAssignments;
                this.searchBar.value = "";
                return;
            }
            if (this.select.value != this.filtered) {
                this.savedAssignments = this.allAssignments;
                this.searchBar.value = "";
                if (this.select.value === "created") {
                    this.filterCreated();
                } else if (this.select.value === "mine") {
                    this.filterMine();
                } else if (this.select.value === "toDo") {
                    this.filterToDo();
                } else if (this.select.value === "free") {
                    this.filterFree();
                } else if (this.select.value === "noAuthor") {
                    this.filterNoAuthor();
                }
                this.filtered = this.select.value;
            }
        },

        filterCreated() {
            var uid = this.user.id;
            this.savedAssignments = this.savedAssignments.filter(function (e) {
                return e.author_id == uid;
            });
        },

        filterMine() {
            var user = this.user;
            this.savedAssignments = this.savedAssignments.filter(function (e) {
                if (e.users.map((u) => u.id).includes(user.id)) {
                    return true;
                }
                return false;
            });
        },

        filterToDo() {
            var uid = this.user.id;
            this.savedAssignments = this.savedAssignments.filter(function (e) {
                if (!e.done) {
                    return true;
                }
                return false;
            });
        },

        filterFree() {
            this.savedAssignments = this.savedAssignments.filter(function (e) {
                if (e.users.length == 0 || e.users.length < e.max_assignees) {
                    return true;
                }
                return false;
            });
        },

        filterNoAuthor() {
            this.savedAssignments = this.savedAssignments.filter(function (e) {
                if (!e.author_id) {
                    return true;
                }
                return false;
            });
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        loadOlderAssignments() {
            axios
                .get(
                    "/assignments/" +
                        this.user.active_group +
                        "/loadOlderAssignments/" +
                        this.allAssignments.length,
                    {}
                )
                .then((response) => {
                    this.allAssignments = response.data
                        .reverse()
                        .concat(this.allAssignments);
                    this.savedAssignments = response.data
                        .reverse()
                        .concat(this.savedAssignments);
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
