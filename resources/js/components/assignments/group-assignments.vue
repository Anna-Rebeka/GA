<template>
    <div class="mb-6">
        <div class="h-12">
            <button @click="createNewAssignment = !createNewAssignment" class="shadow absolute w-min rounded border border-gray-300 px-4 py-2 mb-8 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                New Assignment
            </button>
        </div>

        <form v-if="createNewAssignment" @submit.prevent="submit">
            <div class="md:w-3/4 m-auto bg-white shadow border rounded py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" /> 
                
                <div class="flex items-center justify-between w-full mb-4 p-2 bg-red-500 shadow text-white" v-if="groupAssignmentsErrors.text">
                    {{ groupAssignmentsErrors.text[0] }}
                </div>

                <div class="mx-auto w-full mb-10">
                    <p class="mb-4">
                        <label class="mb-2 uppercase font-bold text-sm" for="name">Name</label>
                        <br>
                        <input
                            id="name"
                            v-model="groupAssignmentsFields.name"
                            type="text"
                            name="name"
                            class="border w-full p-2"
                            required
                        >
                    </p>
                    <p class="mb-4"> 
                        <label class="mb-2 uppercase font-bold text-sm" for="description">Description</label>
                        <br>
                        <textarea name="description" placeholder="specify this task..."
                            class="w-full border p-2 h-24 resize-none focus:outline-none"
                            v-model="groupAssignmentsFields.description" required
                        >
                        </textarea>
                    </p>
                    <p class="mb-6">
                        <label class="mb-2 uppercase font-bold text-sm" for="due">When</label>
                        <br>
                        <input
                            id="due"
                            v-model="groupAssignmentsFields.due"
                            type="datetime-local"
                            name="due"
                            class="border p-2"
                            required
                        >
                    </p>
                    <p class="mb-1  uppercase font-bold text-sm">
                        Do assignment :
                    </p>
                    <div class="mb-4">
                        <input type="radio" v-model="groupAssignmentsFields.on_time" id="false" name="on_time" :value="false" required>
                        <label for="false">before deadline</label><br>
                        <input type="radio" v-model="groupAssignmentsFields.on_time" id="true" name="on_time" :value="true" required>
                        <label for="true">on time</label><br>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-500">(optional)</p>
                        <p class=" uppercase font-bold text-sm">For how long :</p>
                        <label class="mb-2 text-md"  for="duration_hours">Hours</label>
                        <input type="number" v-model="groupAssignmentsFields.duration_hours" id="duration_hours" name="duration_hours" min="0" class="border w-16 p-2">
                        <label class="mb-2 text-md" for="duration_minutes">Minutes</label>
                        <input type="number" v-model="groupAssignmentsFields.duration_minutes" id="duration_minutes" name="duration_minutes" min="0" max="59" class="border w-16 p-2">
                        <p class="text-sm">0h 0min = not set</p>
                    </div>
                    
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">(optional)</p>
                            <label class="mb-2 uppercase font-bold text-sm" for="event_place">Assign this task to:</label>
                            <div 
                                v-for="member in members" :key="member.id" 
                                :value="member.id"
                            >
                                <input type="checkbox" v-model="groupAssignmentsFields.users" :id="member.id" :name="member.id" :value="member.id">
                                <label :for="member.id"> {{ member.name }}</label><br>
                            </div>
                        </div>
                        <p class="text-sm mt-2 text-gray-500">(optional)</p>
                        <label class=" uppercase font-bold text-sm" for="quantity">Maximum number of assignees:</label>
                        <p class="text-sm">0 = not set</p>
                        <input 
                            v-model="groupAssignmentsFields.max_assignees" 
                            type="number" id="max_assignees" min="0" name="max_assignees"
                            class="border p-2"
                        >
                </div>
                <button type="submit" class="shadow float-right -mt-6 rounded border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                    Create an assignment
                </button>
            </div>    
        </form>

        <div class="flex items-center justify-between w-full mb-10 p-2 bg-green-500 shadow text-white" v-if="newAssignmentCreated">
                    Your assignment was created! Check it out down below.
        </div>
        
        <assignments-table :user="user" :group="group" :assignments="assignments"></assignments-table>
    </div>
</template>

<script>
export default {
    props: ['user', 'group', 'assignments', 'members'], 
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            groupAssignmentsFields: {},
            groupAssignmentsErrors: {},
            createNewAssignment: false,
            newAssignmentCreated: false,
        };
    },
    mounted() {
        this.groupAssignmentsFields.users = [];
    },
    methods: {
        submit() {
            axios.post('/assignments', this.groupAssignmentsFields).then(response => {
                this.groupAssignmentsFields = {};
                this.createNewAssignment = false;
                this.newAssignmentCreated = true;
                response.data.author = this.user;
                if(this.assignments.length > 0){
                    this.assignments.unshift(response.data);
                }
                else {
                    this.assignments.push(response.data);
                }
            }).catch(error => {
                console.log(error.message);
            });
        },
    },
}
</script>

<style>

</style>
