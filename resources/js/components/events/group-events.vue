<template>
    <div class="mb-6">
        <div class="h-12">
            <button
                @click="createNewEvent = !createNewEvent"
                class="shadow absolute r-0 w-min rounded border border-gray-300 px-4 py-2 mb-8 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
            >
                New Event
            </button>
        </div>

        <form v-if="createNewEvent" @submit.prevent="submit">
            <div
                class="md:w-3/4 m-auto bg-white shadow border rounded py-6 px-8 mb-8"
            >
                <input type="hidden" name="_token" :value="csrf" />

                <div
                    class="flex items-center justify-between w-full mb-4 p-2 bg-red-500 shadow text-white"
                    v-if="errors.text"
                >
                    {{ errors.text[0] }}
                </div>

                <div class="mx-auto w-full mb-10">
                    <p class="mb-4">
                        <label class="mb-2" for="name">Name:</label>
                        <br />
                        <input
                            id="name"
                            v-model="fields.name"
                            type="text"
                            name="name"
                            class="border w-full p-2"
                            required
                        />
                    </p>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">(optional)</p>
                        <label class="mb-2" for="description"
                            >Description:</label
                        >
                        <br />
                        <textarea
                            name="description"
                            placeholder="tell your group about this event..."
                            class="w-full border p-2 h-24 resize-none focus:outline-none"
                            v-model="fields.description"
                        >
                        </textarea>
                    </div>
                    <p class="mb-4">
                        <label class="mb-2" for="event_time">When:</label>
                        <br />
                        <input
                            id="event_time"
                            v-model="fields.eventTime"
                            type="datetime-local"
                            name="event_time"
                            class="border p-2"
                            required
                        />
                    </p>
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">(optional)</p>
                        <label class="mb-2" for="event_ending"
                            >Ending at:</label
                        >
                        <br />
                        <input
                            id="event_ending"
                            v-model="fields.eventEnding"
                            type="datetime-local"
                            name="event_ending"
                            class="border p-2"
                        />
                    </div>
                    <div class="flex items-center justify-between w-full mb-10 p-2 bg-red-500 shadow text-white" v-if="wrongDatesError">
                        Oops. An event can not end before it starts. 
                     </div>
                    <p class="mb-4">
                        <label class="mb-2" for="event_place">Place:</label>
                        <br />
                        <input
                            id="event_place"
                            v-model="fields.eventPlace"
                            type="text"
                            name="event_place"
                            class="border w-full p-2"
                            required
                        />
                    </p>
                </div>
                <button
                    type="submit"
                    class="shadow float-right -mt-6 rounded border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                >
                    Create an event
                </button>
            </div>
        </form>

        <div
            class="flex items-center justify-between w-full mb-10 p-2 bg-green-500 shadow text-white"
            v-if="newEventCreated"
        >
            Your event was created! Check it out down below.
        </div>

        <events-table :user="user" :events="savedEvents"></events-table>
    </div>
</template>

<script>
export default {
    props: ["user", "events"],
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            fields: {},
            errors: {},
            createNewEvent: false,
            newEventCreated: false,
            savedEvents: this.events,
            wrongDatesError: false,
        };
    },
    methods: {
        submit() {
            if(this.fields.eventEnding){
                if (Date.parse(this.fields.eventTime) > Date.parse(this.fields.eventEnding)) {
                    this.wrongDatesError = true;
                    return;
                } 
            }
            axios
                .post("/events", this.fields)
                .then((response) => {
                    this.fields = {};
                    this.createNewEvent = false;
                    this.newEventCreated = true;
                    this.savedEvents.unshift(response.data);
                })
                .catch((error) => {
                    console.log(error.message);
                });
        },
    },
};
</script>

<style>
</style>
