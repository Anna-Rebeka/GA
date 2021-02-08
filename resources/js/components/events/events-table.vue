<template>
    <div>
        <div class="space-x-2 w-full mb-4">
            <select
                id="filter"
                class="inline-block rounded-lg bg-white border border-gray-300 text-gray-700 px-4 pr-8 h-8 mr-2 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                <option value="all">All</option>
                <option value="created">Created by me</option>
                <option value="joined">Joined</option>
                <option value="pending">Pending</option>
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
            <table class="min-w-full divide-y divide-gray-200 table-fixed">
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
                        <th scope="col" class="px-6 py-3 bg-gray-50">
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="event in pageOfItems" :key="event.id">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            <div 
                                class="w-32 text-sm font-bold"
                                v-bind:class="{ 'text-red-600': closeDate(new Date(event.event_time)), 'text-blue-500': soonToComeDate(new Date(event.event_time))}"
                            >
                                {{  new Date(event.event_time) | dateFormat('DD.MM.YYYY , HH:mm') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-40 text-sm text-grey-900 truncate ...">
                                    {{ event.name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-32 text-sm text-grey-900 truncate ...">
                                    {{ event.event_place }}
                            </div>
                        </td>
                        <td> 
                            <a 
                                :href="'events/' + event.id"
                                class="shadow border border-gray-300 rounded-lg py-2 px-2 mr-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                                Details
                            </a> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-10 clear-both w-full text-center text-sm">
            <jw-pagination :items="savedEvents" @changePage="onChangePage" :pageSize="4"></jw-pagination>
        </div>
        </div>
        </div>
        </div>
    </div>
    
</template>

<script>
export default {
    props: ['user', 'eusers', 'events'],
    data() {
        return {
            allEvents: this.events,
            savedEvents: this.events,
            pageOfItems: [],
            createNewEvent: false,
            newEventCreated: false,
            filtered: "all",
            select: null,
            searchBar: null,
            today: new Date(),
        };
    },

    mounted(){
        this.select = document.getElementById("filter");
        this.select.addEventListener("click", this.selected);
        document.getElementById("searchButton").addEventListener("click", this.findEventByName);
        this.searchBar = document.getElementById("searchBar");
        this.searchBar.addEventListener("keypress", this.searchOnEnter);
    },

    methods: {
        closeDate(eventDate){
            if (eventDate.toLocaleDateString() == this.today.toLocaleDateString()) {
                return true;
            }
            var Difference_In_Time = eventDate.getTime() - this.today.getTime(); 
            var Difference_In_Days = Math.ceil(Difference_In_Time / (1000 * 3600 * 24)); 
            if (Difference_In_Days <= 3) {
                return true;
            }
            return false;
        },

        soonToComeDate(eventDate){
            var Difference_In_Time = eventDate.getTime() - this.today.getTime(); 
            var Difference_In_Days = Math.ceil(Difference_In_Time / (1000 * 3600 * 24)); 
            if (Difference_In_Days > 3 && Difference_In_Days <= 29) {
                return true;
            }
            return false;
        },

        searchOnEnter(event){
            if(event.which === 13){
                this.savedEvents = this.allEvents;
                this.findEventByName();
                event.preventDefault();     
            }
        },

        findEventByName(){
            if(this.searchBar.value == ""){
                this.savedEvents = this.allEvents;
                return;
            }
            var findBy = this.searchBar.value;
            this.savedEvents = this.savedEvents.filter(function(e) {
                return e.name.toLowerCase().includes(findBy.toLowerCase());
            });
        },

        selected(){
            if(this.searchBar.value != ""){
                this.savedEvents = this.allEvents;
                this.searchBar.value = "";
                return;
            }
            if(this.select.value != this.filtered){
                this.savedEvents = this.allEvents;
                this.searchBar.value = "";
                if(this.select.value === "created"){
                    this.filterCreated();
                }
                else if(this.select.value === "joined"){
                    this.filterJoined();
                }
                else if(this.select.value === "pending"){
                    this.filterPending();
                }
                this.filtered = this.select.value;
            }
        },

        filterCreated(){
            var uid = this.user.id;
            this.savedEvents = this.savedEvents.filter(function(e) {
                return e.host_id == uid;
            });
        },

        filterJoined(){
            var uid = this.user.id;
            var eventUsers = [];
            if(this.eusers){
                eventUsers = this.eusers;
            }
            this.savedEvents = this.savedEvents.filter(function(e) {
                if(eventUsers[e.id]){
                    return eventUsers[e.id].includes(uid) || e.host_id == uid;
                }
            });
        },

        filterPending(){
            var uid = this.user.id;
            var eventUsers = [];
            if(this.eusers){
                eventUsers = this.eusers;
            }
            this.savedEvents = this.savedEvents.filter(function(e) {
                if(eventUsers[e.id]){
                    return !eventUsers[e.id].includes(uid) && e.host_id != uid;
                }
                
            });
        },

        reload() {
            this.$forceUpdate();
         },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        joinEvent($event) {
            axios.post('/events/' + $event.id + '/join').then(response => {
                this.eusers[$event.id].push(this.user.id);
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        leaveEvent($event) {
            axios.post('/events/' + $event.id + '/leave').then(response => {
                var index = this.eusers[$event.id].indexOf(this.user.id);
                this.eusers[$event.id].splice(index, 1);
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        checkWithUser($event) {
            if (confirm("Are you sure? This action is irreversible.")) {
                this.destroyEvent($event);
            }
        },

        destroyEvent($event) {
            axios.delete('/events/' + $event.id).then(response => {
                this.allEvents = this.allEvents.filter(function(e) { return e != $event })
                this.savedEvents = this.allEvents;
            });
        }
    },
}
</script>

<style>

</style>
