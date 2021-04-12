<template>
<div>
    <div v-if="filtered == 'mine'">
        <h2 class="font-bold text-lg"> Your assignments </h2>
        <p class="mb-4 text-sm text-gray-500">(from all of your groups)</p>
    </div>
    <div v-else>
        <h2 class="font-bold text-lg"> All assignments </h2>
        <p class="mb-4 text-sm text-gray-500">(from all of your groups)</p>
    </div>
    
    <div class="space-x-2 w-full mb-4">
            <select
                id="filterAssignments"
                class="inline-block rounded-lg bg-white border border-gray-300 text-gray-700 px-4 pr-8 h-8 mr-2 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                <option value="mine">Assigned to me</option>
                <option value="all">All</option>
            </select>
            <div class="md:inline-block md:mt-0 mt-2 relative text-gray-600 md:w-1/3 w-48">
                <input class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-8 px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
                    id="searchBar" type="search" name="searchBar" placeholder="Search by name">
                <button id="searchButton" type="submit" class="absolute right-0 top-2 mr-4 bg-transparent focus:outline-none">
                    <img src="/img/search.png" width="20" height="20" alt="submit" />
                </button>
            </div>
        </div>
        <p class="ml-2 text-xs font-bold text-blue-500 float-left">Deadline </p> <p class="text-sm font-bold float-left ml-2 mr-2">/</p> <p class="float-left text-xs font-bold text-red-600 mb-4 mr-8">Appointment</p>
        <p class="ml-2 text-xs px-1 font-medium bg-red-100 float-left">In progress</p> <p class="text-sm font-bold float-left ml-2 mr-2">/</p> <p class="text-xs px-2 font-medium bg-green-100 float-left"> Done</p>
        <div class="flex flex-col clear-both">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 table-fixed">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            Date & Time
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            What
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            By whom
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            Group
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50">
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="assignment in pageOfItems" :key="assignment.id"
                        v-bind:class="{ 'bg-green-100': assignment.done, 'bg-red-100': !assignment.done}"
                    >
                        <td class="bg-white px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            <div 
                                class="w-32 text-sm font-bold"
                                v-bind:class="{ 'text-red-600': assignment.on_time, 'text-blue-500': !assignment.on_time}"

                            >
                                {{  new Date(assignment.due) | dateFormat('DD.MM.YYYY , HH:mm') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-32 text-sm text-grey-900 truncate ... max-w-xs">
                                    {{ assignment.name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div v-if="assignment.author" class="text-sm text-gray-900">
                                    {{ assignment.author.name }}
                            </div>
                            <div v-else class="text-sm text-gray-900">
                                    {{ assignment.author_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div v-if="assignment.group" class="text-sm text-gray-900">
                                    {{ assignment.group.name }}
                            </div>
                            <div v-else class="text-sm text-gray-900">
                                    {{ assignment.group_name }}
                            </div>
                        </td>
                        <td class="pr-6 py-4">  
                            <a 
                                :href="'/assignments/' + assignment.id"
                                class="bg-white shadow border border-gray-300 rounded-lg py-2 px-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                                Details
                            </a> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4 clear-both w-full text-center text-sm">
            <jw-pagination :items="filteredAssignments" @changePage="onChangePage" :pageSize="4"></jw-pagination>
        </div>
        </div>
        </div>
        </div>
    </div>
    
</template>

<script>
export default {
    props: ['authUser'],
    
    data() {
        return {
            assignments: [],
            filteredAssignments: [],
            pageOfItems: [],
            filtered: "mine",
            searchBar: null,
        };
    },

    mounted(){
        this.getUsersAssignments();
        this.select = document.getElementById("filterAssignments");
        this.select.addEventListener("click", this.selected);
        document.getElementById("searchButton").addEventListener("click", this.findAssignmentByName);
        this.searchBar = document.getElementById("searchBar");
        this.searchBar.addEventListener("keypress", this.searchOnEnter);
    },

    methods: {
        searchOnEnter(assignment){
            if(assignment.which === 13){
                this.findAssignmentByName();
                assignment.preventDefault();     
            }
        },

        findAssignmentByName(){
            if(this.searchBar.value == ""){
                this.filteredAssignments = this.assignments;
                return;
            }
            var findBy = this.searchBar.value;
            this.filteredAssignments = this.assignments.filter(function(e) {
                return e.name.toLowerCase().includes(findBy.toLowerCase());
            });
        },

        selected(){
            if(this.searchBar.value != ""){
                this.filteredAssignments = this.assignments;
                this.searchBar.value = "";
                return;
            }
            if(this.select.value != this.filtered){
                if(this.select.value === "mine"){
                    this.getUsersAssignments();
                }
                else if(this.select.value === "all"){
                    this.getAllUsersAssignments();
                }
                this.filtered = this.select.value;
            }
        },

        getUsersAssignments() {
            axios.get(this.authUser.username + '/assignments/mine').then(response => {
                this.assignments = response.data;
                this.filteredAssignments = this.assignments;
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        getAllUsersAssignments() {
            axios.get(this.authUser.username + '/assignments/all').then(response => {
                this.assignments = response.data;
                this.filteredAssignments = this.assignments;
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
    },


}
</script>

<style>

</style>