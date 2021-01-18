<template>
    <div>
        <div class="space-x-2 w-full mb-4">
            <select
                id="filter"
                class="inline-block rounded-lg bg-white border border-gray-300 text-gray-700 px-4 pr-8 h-8 mr-2 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                <option value="all">All</option>
                <option value="mine">Mine</option>
                <option value="free">Free</option>
                <option value="created">Created by me</option>
            </select>
            <div class="inline-block relative text-gray-600 w-1/3">
                <input class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-8 px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
                    id="searchBar" type="search" name="searchBar" placeholder="Search by name">
                <button id="searchButton" type="submit" class="absolute right-0 top-2 mr-4 bg-transparent focus:outline-none">
                    <img src="/img/search.png" width="20" height="20" alt="submit" />
                </button>
            </div>
        </div>
        
        <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            When
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            What
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            By who
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            For who
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50">
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50">
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="assignment in pageOfItems" :key="assignment.id">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{  new Date(assignment.due) | dateFormat('DD.MM.YYYY , HH:mm') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                    {{ assignment.name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                    {{ assignment.author.name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div v-if="assignment.assignee" class="text-sm text-gray-900">
                                    {{ assignment.assignee.name }}
                            </div>
                        </td>
                        <td> 
                            <a 
                                :href="'assignments/' + assignment.id"
                                class="shadow border border-gray-300 rounded-lg py-2 px-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                                About
                            </a> 
                        </td>
                        <td> 
                            <div class="ml-4">
                                <div v-if="assignment.assignee_id == null">
                                    <button 
                                        @click="checkTakeWithUser(assignment)"
                                        class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100 focus:outline-none">
                                        Take
                                    </button> 
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-10 clear-both w-full text-center text-sm">
            <jw-pagination :items="savedAssignments" @changePage="onChangePage" :pageSize="4"></jw-pagination>
        </div>
        </div>
        </div>
        </div>
    </div>
    
</template>

<script>
export default {
    props: ['user', 'assignments'],
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

    mounted(){
        this.select = document.getElementById("filter");
        this.select.addEventListener("click", this.selected);
        document.getElementById("searchButton").addEventListener("click", this.findAssignmentByName);
        this.searchBar = document.getElementById("searchBar");
        this.searchBar.addEventListener("keypress", this.searchOnEnter);
    },

    methods: {
        searchOnEnter(assignment){
            if(assignment.which === 13){
                this.savedAssignments = this.allAssignments;
                this.findAssignmentByName();
                assignment.preventDefault();     
            }
        },

        findAssignmentByName(){
            if(this.searchBar.value == ""){
                this.savedAssignments = this.allAssignments;
                return;
            }
            var findBy = this.searchBar.value;
            this.savedAssignments = this.savedAssignments.filter(function(e) {
                return e.name.toLowerCase().includes(findBy.toLowerCase());
            });
        },

        selected(){
            if(this.searchBar.value != ""){
                this.savedAssignments = this.allAssignments;
                this.searchBar.value = "";
                return;
            }
            if(this.select.value != this.filtered){
                this.savedAssignments = this.allAssignments;
                this.searchBar.value = "";
                if(this.select.value === "created"){
                    this.filterCreated();
                }
                else if(this.select.value === "mine"){
                    this.filterMine();
                }
                else if(this.select.value === "free"){
                    this.filterFree();
                }
                this.filtered = this.select.value;
            }
        },

        filterCreated(){
            var uid = this.user.id;
            this.savedAssignments = this.savedAssignments.filter(function(e) {
                return e.author_id == uid;
            });
        },

        filterMine(){
            var uid = this.user.id;
            this.savedAssignments = this.savedAssignments.filter(function(e) {
                if(e.assignee_id == uid){
                    return true;
                }
                return false;
            });
        },

        filterFree(){
            this.savedAssignments = this.savedAssignments.filter(function(e) {
                if(e.assignee_id){
                    return false;
                }
                return true;
            });
        },

        reload() {
            this.$forceUpdate();
         },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        takeAssignment($assignment) {
            axios.patch('/assignments/' + $assignment.id + '/take').then(response => {    
                var index = this.savedAssignments.indexOf($assignment);
                this.allAssignments[index].assignee = this.user;
                this.allAssignments[index].assignee_id = this.user.id;
                this.savedAssignments = this.allAssignments;
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        abandonAssignment($assignment) {
            axios.post('/assignments/' + $assignment.id + '/leave').then(response => {
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        checkTakeWithUser($assignment, $whatToDo){
            if (confirm("Are you sure? This action is irreversible.")) {
                this.takeAssignment($assignment);
            }
        },
    },
}
</script>

<style>

</style>
