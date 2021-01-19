<template>
<div>
    <div v-if="filtered == 'joined'">
        <h2 class="font-bold text-lg"> Your upcoming events </h2>
        <p class="mb-4 text-sm text-gray-500">(from all of your groups)</p>
    </div>
    <div v-else>
        <h2 class="font-bold text-lg"> All upcoming events </h2>
        <p class="mb-4 text-sm text-gray-500">(from all of your groups)</p>
    </div>
    
    <div class="space-x-2 w-full mb-4">
            <select
                id="filterEvents"
                class="inline-block rounded-lg bg-white border border-gray-300 text-gray-700 px-4 pr-8 h-8 mr-2 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                <option value="joined">Joined</option>
                <option value="all">All</option>
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
                            Where
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Group
                        </th>
                        <th scope="col" class="px-6 py-3 bg-gray-50">
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="event in pageOfItems" :key="event.id">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{  new Date(event.event_time) | dateFormat('DD.MM.YYYY , HH:mm') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                    {{ event.name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                    {{ event.event_place }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                    {{ event.group_name }}
                            </div>
                        </td>
                        <td> 
                            <a 
                                :href="'/events/' + event.id"
                                class="shadow border border-gray-300 rounded-lg mr-6 py-2 px-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                                About
                            </a> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4 clear-both w-full text-center text-sm">
            <jw-pagination :items="filteredEvents" @changePage="onChangePage" :pageSize="4"></jw-pagination>
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
            events: [],
            filteredEvents: [],
            pageOfItems: [],
            filtered: "joined",
            searchBar: null,
        };
    },

    mounted(){
        this.getUserJoinedEvents();
        this.select = document.getElementById("filterEvents");
        this.select.addEventListener("click", this.selected);
        document.getElementById("searchButton").addEventListener("click", this.findEventByName);
        this.searchBar = document.getElementById("searchBar");
        this.searchBar.addEventListener("keypress", this.searchOnEnter);
        
    },

    methods: {
        searchOnEnter(event){
            if(event.which === 13){
                this.findEventByName();
                event.preventDefault();     
            }
        },

        findEventByName(){
            if(this.searchBar.value == ""){
                this.filteredEvents = this.events;
                return;
            }
            var findBy = this.searchBar.value;
            this.filteredEvents = this.events.filter(function(e) {
                return e.name.toLowerCase().includes(findBy.toLowerCase());
            });
        },

        selected(){
            if(this.searchBar.value != ""){
                this.filteredEvents = this.events;
                this.searchBar.value = "";
                return;
            }
            if(this.select.value != this.filtered){
                if(this.select.value === "joined"){
                    this.getUserJoinedEvents();
                }
                else if(this.select.value === "all"){
                    this.getAllUserEvents();
                }
                this.filtered = this.select.value;
            }
        },

        getUserJoinedEvents() {
            axios.get(this.authUser.username + '/events/joined').then(response => {
                this.events = response.data;
                this.filteredEvents = this.events;
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        getAllUserEvents() {
            axios.get(this.authUser.username + '/events/all').then(response => {
                this.events = response.data;
                this.filteredEvents = this.events;
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