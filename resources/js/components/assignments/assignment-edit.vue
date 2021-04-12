<template>
    <div>
        <form @submit.prevent="submit">
            <div class="md:w-3/4 m-auto py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" />

                <div class="mx-auto w-full mb-10">
                    <p class="mb-4">
                        <label class="mb-2  uppercase font-bold text-sm" for="name">Name</label>
                        <br />
                        <input
                            id="name"
                            v-model="shownAssignment.name"
                            type="text"
                            name="name"
                            class="border-b w-full p-2"
                            required
                        />
                    </p>
                    <p class="mb-4">
                        <label class="mb-2  uppercase font-bold text-sm" for="description"
                            >Description</label
                        >
                        <br />
                        <textarea
                            name="description"
                            placeholder="specify this task..."
                            class="w-full border-b p-2 h-24 resize-none focus:outline-none"
                            v-model="shownAssignment.description"
                            required
                        >
                        </textarea>
                    </p>
                    <p class="mb-6">
                        <label class="mb-2  uppercase font-bold text-sm" for="due">When</label>
                        <br />
                        <input
                            id="due"
                            v-model="due"
                            type="datetime-local"
                            name="due"
                            class="border-b p-2"
                            required
                        />
                    </p>
                    <p class="mb-1  uppercase font-bold text-sm">Type :</p>
                    <div class="mb-6">
                        <input
                            type="radio"
                            v-model="onTime"
                            id="false"
                            name="on_time"
                            :value="false"
                            required
                        />
                        <label for="false">Deadline</label><br />
                        <input
                            type="radio"
                            v-model="onTime"
                            id="true"
                            name="on_time"
                            :value="true"
                            required
                        />
                        <label for="true">Appointment</label><br />
                    </div>

                    <div class="mb-6">
                        <p class=" uppercase font-bold text-sm">Time estimate:</p>
                        <p class="text-sm text-gray-500">(optional)</p>
                        <label class="mb-2 text-md" for="duration_hours"
                            >Hours</label
                        >
                        <input
                            type="number"
                            v-model="hours"
                            id="duration_hours"
                            name="duration_hours"
                            min="0"
                            class="border w-16 p-2"
                        />
                        <label class="mb-2 text-md" for="duration_minutes"
                            >Minutes</label
                        >
                        <input
                            type="number"
                            v-model="minutes"
                            id="duration_minutes"
                            name="duration_minutes"
                            min="0"
                            max="59"
                            class="border w-16 p-2"
                        />
                        <p class="text-sm">0h 0min = not set</p>
                    </div>

                    <div class="mb-4">
                        <label class="mb-2 uppercase font-bold text-sm" for="event_place"
                            >Assign this task to:</label
                        >
                        <p class="text-sm text-gray-500">(optional)</p>
                        <div
                            v-for="assignee in assignees"
                            :key="assignee.id"
                            :value="assignee.id"
                        >
                            <input
                                type="checkbox"
                                :id="assignee.id"
                                :name="assignee.id"
                                :value="assignee.id"
                                v-model="users"
                            />
                            <label :for="assignee.id">
                                {{ assignee.name }}</label
                            ><br />
                        </div>
                        <div
                            v-for="free_member in free_members"
                            :key="free_member.id"
                            :value="free_member.id"
                        >
                            <div></div>
                            <input
                                type="checkbox"
                                :id="free_member.id"
                                :name="free_member.id"
                                :value="free_member.id"
                                v-model="users"
                            />
                            <label :for="free_member.id">
                                {{ free_member.name }}</label
                            ><br />
                        </div>
                    </div>
                    
                    <label class="mt-6 uppercase font-bold text-sm" for="quantity">Maximum number of assignees:</label>
                    <p class="text-sm text-gray-500">(optional)</p>
                    <input
                        v-model="shownAssignment.max_assignees"
                        type="number"
                        id="max_assignees"
                        min="0"
                        name="max_assignees"
                        class="border p-2"
                    />
                    <p class="text-sm">0 = not set</p>
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
    props: ["user", "assignment", "group", "free_members"],
    data() {
        return {
            assignees: this.assignment.users,
            members: this.group.users,
            shownAssignment: this.assignment,
            onTime: this.assignment.on_time == 1,
            due: this.assignment.due,
            hours: this.assignment.duration,
            minutes: this.assignment.duration,
            takenAssignment: false,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            users: this.assignment.users.map(u => u.id),
            pageOfItems: [],
            asEditErrors: {},
        };
    },

    mounted() {
        if (this.due) {
            this.due =
                this.assignment.due.slice(
                    0,
                    this.assignment.due.lastIndexOf(" ")
                ) +
                "T" +
                this.assignment.due.slice(
                    this.assignment.due.lastIndexOf(" ") + 1
                );
        }
        if (this.hours && this.minutes) {
            this.hours = Number(
                this.assignment.duration.slice(
                    0,
                    this.assignment.duration.lastIndexOf("h")
                )
            );
            this.minutes = Number(
                this.assignment.duration.slice(
                    this.assignment.duration.lastIndexOf("h") + 2,
                    this.assignment.duration.lastIndexOf("m")
                )
            );
        }
    },

    methods: {
        submit(){
            this.shownAssignment.on_time = this.onTime;
            this.shownAssignment.due = this.due;
            this.shownAssignment.duration_hours = this.hours;
            this.shownAssignment.duration_minutes = this.minutes;
            this.shownAssignment.users = this.users;

            axios.patch("/assignments/" + this.assignment.id + "/edit", 
                this.shownAssignment).then(response => {
                window.location.href = "/assignments/" + this.assignment.id;
            }).catch(error => {
                console.log(error.message);
            });
        }

    },
};
</script>

<style>
</style>