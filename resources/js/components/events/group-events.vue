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
                <button type="submit" class="shadow float-right -mt-6 rounded-lg border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                    Create an event
                </button>
            </div>    
        </form>

        <div class="flex items-center justify-between w-full mb-10 p-2 bg-green-500 shadow text-white" v-if="newEventCreated">
                    Your event was created! Check it out down below.
        </div>
        
        <events-table :user="user" :eusers="eusers" :events="events"></events-table>
    </div>
</template>

<script>
export default {
    props: ['user', 'eusers', 'events'],
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            fields: {},
            errors: {},
            createNewEvent: false,
            newEventCreated: false,
        };
    },
    methods: {
        submit() {
            axios.post('/events', this.fields).then(response => {
                this.fields = {};
                this.createNewEvent = false;
                this.newEventCreated = true;
                this.events.unshift(response.data);    
                this.eusers[response.data.id] = [response.data.host_id];

            }).catch(error => {
                console.log(error.message);
            });
        },
    },
}
</script>

<style>

</style>
