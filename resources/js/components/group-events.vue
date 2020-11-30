<template>
    <div class="mb-6">
        <div class="h-12">
            <button @click="createNewEvent = !createNewEvent" class="shadow absolute r-0 w-min rounded-lg border border-gray-300 px-4 py-2 mb-8 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                New Event
            </button>
        </div>

        <form v-if="createNewEvent" @submit.prevent="submit">
            <div class="md:w-3/4 m-auto bg-white shadow border rounded-lg py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" /> 
                
                <div class="flex items-center justify-between w-full mb-4 p-2 bg-red-500 shadow text-white" v-if="errors.text">
                    {{ errors.text[0] }}
                </div>

                <div class="mx-auto w-full mb-10">
                    <p class="mb-4">
                        <label class="mb-2" for="name">Name</label>
                        <br>
                        <input
                            id="name"
                            v-model="fields.name"
                            type="text"
                            name="name"
                            class="border w-full p-2"
                            required
                        >
                    </p>
                    <p class="mb-4"> 
                        <label class="mb-2" for="description">Description (optional)</label>
                        <br>
                        <textarea name="description" placeholder="tell your group about this event..."
                            class="w-full border p-2 h-24 resize-none focus:outline-none"
                            v-model="fields.description"
                        >
                        </textarea>
                    </p>
                    <p class="mb-4">
                        <label class="mb-2" for="event_time">When</label>
                        <br>
                        <input
                            id="event_time"
                            v-model="fields.eventTime"
                            type="datetime-local"
                            name="event_time"
                            class="border p-2"
                            required
                        >
                    </p>
                    <p class="mb-4">
                        <label class="mb-2" for="event_place">Place</label>
                        <br>
                        <input
                            id="event_place"
                            v-model="fields.eventPlace"
                            type="text"
                            name="event_place"
                            class="border w-full p-2"
                            required
                        >
                    </p>
                </div>
                <div class="flex items-center justify-between w-full mb-10 p-2 bg-green-500 shadow text-white" v-if="newEventCreated">
                    Your event was created! Check it out down below.
                </div>
                <button type="submit" class="shadow float-right -mt-6 rounded-lg border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                    Create an event
                </button>
            </div>    
        </form>

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
                                {{ event.event_time }}
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
                                href="/"
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
                                        @click="destroyEvent(event)"
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
        </div>
        </div>
        </div>
        <div class="mt-10 clear-both w-full text-center">
            <jw-pagination :items="savedEvents" @changePage="onChangePage" :pageSize="4"></jw-pagination>
        </div>
    </div>
  
</template>

<script>
export default {
    props: ['user', 'eusers', 'events'],
    data() {
        return {
            savedEvents: this.events,
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            pageOfItems: [],
            fields: {},
            errors: {},
            createNewEvent: false,
            newEventCreated: false,
        };
    },
    mounted(){
        
    },
    methods: {
        reload() {
            this.$forceUpdate();
         },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        submit() {
            axios.post('/events', this.fields).then(response => {
                this.fields = {};
                this.newEventCreated = true;
                this.savedEvents.unshift(response.data);                
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
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
                var index = this.user.id;
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

        destroyEvent($event) {
            axios.delete('events/' + $event.id).then(response => {
                this.savedEvents = this.savedEvents.filter(function(e) { return e !== $event })
            });
        }
    },
}
</script>

<style>

</style>