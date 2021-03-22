<template>
    <div>
        <form @submit.prevent="submit">
            <div class="md:w-3/4 m-auto py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" />

                <div class="mx-auto w-full mb-10">
                    <p class="mb-4">
                        <label class="mb-2" for="name">Name</label>
                        <br />
                        <input
                            id="name"
                            v-model="showedAssignment.name"
                            type="text"
                            name="name"
                            class="border-b w-full p-2"
                            required
                        />
                    </p>
                    <p class="mb-4">
                        <label class="mb-2" for="description"
                            >Description</label
                        >
                        <br />
                        <textarea
                            name="description"
                            placeholder="specify this task..."
                            class="w-full border-b p-2 h-24 resize-none focus:outline-none"
                            v-model="showedAssignment.description"
                            required
                        >
                        </textarea>
                    </p>
                    <p class="mb-6">
                        <label class="mb-2" for="due">When</label>
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
                    <p class="mb-1">Do assignment :</p>
                    <div class="mb-4">
                        <input
                            type="radio"
                            v-model="onTime"
                            id="false"
                            name="on_time"
                            :value="false"
                            required
                        />
                        <label for="false">before deadline</label><br />
                        <input
                            type="radio"
                            v-model="onTime"
                            id="true"
                            name="on_time"
                            :value="true"
                            required
                        />
                        <label for="true">on time</label><br />
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-500">(optional)</p>
                        <p>For how long :</p>
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
                        <p class="text-sm text-gray-500">(optional)</p>
                        <label class="mb-2" for="event_place"
                            >Assign this task to:</label
                        >
                        <div
                            v-for="assignee in assignees"
                            :key="assignee.id"
                            :value="assignee.id"
                        >
                            <input
                                type="checkbox"
                                checked="true"
                                :id="assignee.id"
                                :name="assignee.id"
                                :value="assignee.id"
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
                            />
                            <label :for="free_member.id">
                                {{ free_member.name }}</label
                            ><br />
                        </div>
                    </div>
                    <p class="text-sm mt-2 text-gray-500">(optional)</p>
                    <label for="quantity">Maximum number of assignees:</label>
                    <p class="text-sm">0 = not set</p>
                    <input
                        v-model="showedAssignment.max_assignees"
                        type="number"
                        id="max_assignees"
                        min="0"
                        name="max_assignees"
                        class="border p-2"
                    />
                </div>
                <button
                    type="submit"
                    class="shadow float-right -mt-6 rounded border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                >
                    Create an assignment
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
            showedAssignment: this.assignment,
            onTime: this.assignment.on_time == 1,
            due: this.assignment.due,
            hours: this.assignment.duration,
            minutes: this.assignment.duration,
            takenAssignment: false,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            pageOfItems: [],
            errors: {},
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

    methods: {},
};
</script>

<style>
</style>