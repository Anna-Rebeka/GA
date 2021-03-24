<template>
    <div>
        <form @submit.prevent="submit">
            <div class="md:w-3/4 m-auto py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" />

                <div
                    class="flex items-center justify-between w-full mb-4 p-2 bg-red-500 shadow text-white"
                    v-if="errors.text"
                >
                    {{ errors.text[0] }}
                </div>

                <div class="mx-auto w-full mb-10">
                    <p class="mb-4">
                        <label
                            class="mb-2 uppercase font-bold text-sm"
                            for="name"
                            >Name:</label
                        >
                        <br />
                        <input
                            id="name"
                            v-model="editedEvent.name"
                            type="text"
                            name="name"
                            class="border-b w-full p-2"
                            required
                        />
                    </p>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">(optional)</p>
                        <label
                            class="mb-2 uppercase font-bold text-sm"
                            for="description"
                            >Description:</label
                        >
                        <br />
                        <textarea
                            name="description"
                            placeholder="tell your group about this event..."
                            class="w-full border-b p-2 h-24 resize-none focus:outline-none"
                            v-model="editedEvent.description"
                        >
                        </textarea>
                    </div>
                    <p class="mb-4">
                        <label
                            class="mb-2 uppercase font-bold text-sm"
                            for="event_time"
                            >When:</label
                        >
                        <br />
                        <input
                            id="event_time"
                            v-model="eventTime"
                            type="datetime-local"
                            name="event_time"
                            class="border-b p-2"
                            required
                        />
                    </p>
                    <div class="mb-6">
                        <p class="text-sm text-gray-500">(optional)</p>
                        <label
                            class="mb-2 uppercase font-bold text-sm"
                            for="event_ending"
                            >Ending at:</label
                        >
                        <br />
                        <input
                            id="event_ending"
                            v-model="eventEnding"
                            type="datetime-local"
                            name="event_ending"
                            class="border-b p-2"
                        />
                    </div>
                    <div
                        class="flex items-center justify-between w-full mb-10 p-2 bg-red-500 shadow text-white"
                        v-if="wrongDatesError"
                    >
                        Oops. An event can not end before it starts.
                    </div>
                    <p class="mb-4">
                        <label
                            class="mb-2 uppercase font-bold text-sm"
                            for="event_place"
                            >Place:</label
                        >
                        <br />
                        <input
                            id="event_place"
                            v-model="editedEvent.event_place"
                            type="text"
                            name="event_place"
                            class="border-b w-full p-2"
                            required
                        />
                    </p>
                </div>
                <button
                    type="submit"
                    class="shadow float-right -mt-6 rounded border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                >
                    Save changes
                </button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ["user", "event"],
    data() {
        return {
            editedEvent: this.event,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            fields: {},
            errors: {},
            eventTime:
                this.event.event_time.slice(
                    0,
                    this.event.event_time.lastIndexOf(" ")
                ) +
                "T" +
                this.event.event_time.slice(
                    this.event.event_time.lastIndexOf(" ") + 1
                ),
            eventEnding: this.event.event_ending,
            wrongDatesError: false,
        };
    },
    mounted() {
        if (this.eventEnding) {
            this.eventEnding =
                this.event.event_ending.slice(
                    0,
                    this.event.event_ending.lastIndexOf(" ")
                ) +
                "T" +
                this.event.event_ending.slice(
                    this.event.event_ending.lastIndexOf(" ") + 1
                );
        }
    },
    methods: {
        submit() {
            this.editedEvent.event_time = this.eventTime;
            if(this.editedEvent.event_time.length == 16) {
                this.editedEvent.event_time += ":00";
            }
            if(this.eventEnding){
                this.editedEvent.event_ending = this.eventEnding;
                if(this.editedEvent.event_ending.length == 16){
                    this.editedEvent.event_ending += ":00";
                }
            }
            else{
                this.editedEvent.event_ending = this.eventEnding;
            }
            
            if(this.editedEvent.event_ending){
                if (Date.parse(this.editedEvent.event_time) > Date.parse(this.editedEvent.event_ending)) {
                    this.wrongDatesError = true;
                    return;
                } 
            }

            axios
                .patch("/events/" + this.event.id + "/edit", this.editedEvent)
                .then((response) => {
                    window.location.href = "/events/" + this.event.id;
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