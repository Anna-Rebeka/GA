<template>
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
                        <th scope="col" class="px-6 py-3 bg-gray-50">
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
                        <td> 
                            <a 
                                :href="'events/' + event.id"
                                class="shadow border border-gray-300 rounded-lg py-2 px-2 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                                About
                            </a> 
                        </td>
                        <td> 
                            <div class="ml-4">
                                <div v-if="eusers[event.id] && !eusers[event.id].includes(user.id)">
                                    <button 
                                        @click="joinEvent(event)"
                                        class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100 focus:outline-none">
                                        Join
                                    </button> 
                                </div>
                                <div  v-else-if="event.author_id == user.id">
                                    <button 
                                        @click="checkWithUser(event)"
                                        class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-white text-xs bg-red-400 hover:text-gray-500 hover:bg-red-200 focus:outline-none">
                                        Delete
                                    </button> 
                                </div>
                                <div v-else>
                                    <button 
                                        @click="leaveEvent(event)"
                                        class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-red-200 hover:text-gray-500 hover:bg-red-100 focus:outline-none">
                                        Leave
                                    </button> 
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-10 clear-both w-full text-center">
            <jw-pagination :items="savedEvents" @changePage="onChangePage" :pageSize="4"></jw-pagination>
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
            savedEvents: this.events,
            pageOfItems: [],
            createNewEvent: false,
            newEventCreated: false,
        };
    },
    methods: {
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
                this.savedEvents = this.savedEvents.filter(function(e) { return e !== $event })
            });
        }
    },
}
</script>

<style>

</style>
