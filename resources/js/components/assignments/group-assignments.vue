<template>
    <div class="mb-6">
        <div class="h-12">
            <button @click="createNewAssignment = !createNewAssignment" class="shadow absolute r-0 w-min rounded-lg border border-gray-300 px-4 py-2 mb-8 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                New Assignment
            </button>
        </div>

        <form v-if="createNewAssignment" @submit.prevent="submit">
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
                        <label class="mb-2" for="description">Description</label>
                        <br>
                        <textarea name="description" placeholder="specify this task..."
                            class="w-full border p-2 h-24 resize-none focus:outline-none"
                            v-model="fields.description" required
                        >
                        </textarea>
                    </p>
                    <p class="mb-6">
                        <label class="mb-2" for="due">When</label>
                        <br>
                        <input
                            id="due"
                            v-model="fields.due"
                            type="datetime-local"
                            name="due"
                            class="border p-2"
                            required
                        >
                    </p>
                    <p class="mb-1">
                        Do assignment :
                    </p>
                    <div class="mb-4">
                        <input type="radio" v-model="fields.on_time" id="false" name="on_time" :value="false" required>
                        <label for="false">before deadline</label><br>
                        <input type="radio" v-model="fields.on_time" id="true" name="on_time" :value="true" required>
                        <label for="true">on time</label><br>
                    </div>
                        <div class="mb-4">
                            <label class="mb-2" for="event_place">Assign this task to</label>
                            <div 
                                v-for="member in members" :key="member.id" 
                                :value="member.id"
                            >
                                <input type="checkbox" v-model="fields.users" :id="member.id" :name="member.id" :value="member.id">
                                <label :for="member.id"> {{ member.name }}</label><br>
                            </div>
                        </div>
                        
                        <label for="quantity">Maximum number of assignees:</label>
                        <p class="text-sm">0 = not set</p>
                        <input 
                            v-model="fields.max_assignees" 
                            type="number" id="max_assignees" min="0" name="max_assignees"
                            class="border p-2"
                        >
                </div>
                <button type="submit" class="shadow float-right -mt-6 rounded-lg border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                    Create an assignment
                </button>
            </div>    
        </form>

        <div class="flex items-center justify-between w-full mb-10 p-2 bg-green-500 shadow text-white" v-if="newAssignmentCreated">
                    Your assignment was created! Check it out down below.
        </div>
        
        <assignments-table :user="user" :assignments="assignments"></assignments-table>
    </div>
</template>

<script>
export default {
    props: ['user', 'assignments', 'members'], 
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            fields: {},
            errors: {},
            createNewAssignment: false,
            newAssignmentCreated: false,
        };
    },
    mounted() {
        this.fields.users = [];
    },
    methods: {
        submit() {
            axios.post('/assignments', this.fields).then(response => {
                this.fields = {};
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
