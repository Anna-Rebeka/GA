<template>
    <div class="mb-6">
        <form @submit.prevent="submit">
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

                <button type="submit" class="shadow float-right -mt-6 rounded-lg border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                    Make an event
                </button>
            </div>    
        </form>

        <ul class="mt-20 mb-16">
            <li v-for="event in pageOfItems" :key="event.id" class="inline float-left mr-4 mb-12 h-80">
                <div class="max-w-xs rounded overflow-hidden shadow-lg mb-4">
                    <img class="w-full" src="/img/event-banner.jpg" :alt="event.name">
                    <div class="px-6 py-4">
                        <div class="mb-2">
                            <p class="font-bold text-xl">
                                {{ event.name }}
                            </p>
                            <p class="font-bold text-sm">
                                {{ event.event_time }}
                            </p>
                            <div v-if="event.author">
                                <p class="text-sm">
                                    created by : <strong> {{ event.author.name }} </strong>
                                </p>
                            </div>
                            <p class="font-bold float-right text-sm">
                                {{ event.event_place }}
                            </p>
                        </div>
                        <p class="mb-4">
                            {{ event.description }}
                        </p>
                        <p class="text-gray-700 text-base">
                            
                            Going :
                            <ul class="text-base">
                                <div v-if="event.users">
                                    <li class="text-sm" v-for="user in event.users" :key="user.name">
                                        {{ user.name }}
                                    </li>
                                </div>
                            </ul>    
                             
                        </p>
                    </div>
                    <div class="px-6 pt-4 pb-2 mb-6 float-right">
                        <button 
                            @click="joinEvent(event)"
                            class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100">
                            Join
                        </button>
                    </div>
                </div>
            </li>
        </ul>
        
        <div class="mt-10 clear-both w-full text-center">
            <jw-pagination :items="savedEvents" @changePage="onChangePage" :pageSize="4"></jw-pagination>
        </div>

    </div>
  
</template>

<script>
export default {
    props: ['events'],
    data() {
        return {
            savedEvents: this.events,
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            pageOfItems: [],
            fields: {},
            errors: {}
        };
    },
    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
        
        deleteData: function(note) {
            axios.delete('events/' + event.id).then(response => {
                this.savedEvents = this.savedEvents.filter(function(e) { return e !== note })
            });
        },

        submit() {
            axios.post('/events', this.fields).then(response => {
                this.fields = {};
                this.savedEvents.push(response.data);
                
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        joinEvent($event) {
            console.log($event.id);
        }
    },
}
</script>

<style>

</style>