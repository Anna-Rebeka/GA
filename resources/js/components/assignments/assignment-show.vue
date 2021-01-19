<template>
<div>
    <div class="p-8 mr-2 mb-8">
        <div class="float-right">
            <div v-if="showedAssignment.author_id == user.id">
                <button 
                    v-if="!showedAssignment.done"
                    @click="checkWithUser(showedAssignment, 'done')"
                    class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-white text-xs bg-green-400 hover:text-gray-500 hover:bg-green-200 focus:outline-none">
                    Done
                </button> 
                <button 
                    @click="checkWithUser(showedAssignment, 'delete')"
                    class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-white text-xs bg-red-400 hover:text-gray-500 hover:bg-red-200 focus:outline-none">
                    Delete
                </button> 
            </div>
        </div>
        <div class="float-right">
            <div v-if="showedAssignment.assignee_id == null">
                <button 
                    @click="checkWithUser(showedAssignment, 'take')"
                    class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100 focus:outline-none">
                    Take
                </button> 
            </div>
        </div>
        <h2 class="font-bold text-2xl mb-4"> {{ showedAssignment.name }} </h2>
        <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>Due</strong></p>
            <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2" >
                {{  new Date(showedAssignment.due) | dateFormat('DD.MM.YYYY , HH:mm') }}
            </div>
        </div>
            <div class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block"> <strong>By Who</strong><p class="bg-white p-2 rounded">{{ showedAssignment.author.name }}</p></div>   
            <div v-if="showedAssignment.assignee" class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block"> <strong>For Who</strong><p class="bg-white p-2 rounded">{{ showedAssignment.assignee.name }}</p></div>   
        <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>What about</strong></p>
            <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2" >
                {{ showedAssignment.description }}
            </div>
        </div>
    </div>
    <assignment-comments :user ="this.user" :assignment="this.assignment"></assignment-comments>
</div>
    
</template>

<script>
export default {
    props: ['user', 'assignment'],
    data(){
        return {
            showedAssignment: this.assignment, 

        };
    },
    
    methods: {
        checkWithUser($assignment, $whatToDo){
            if (confirm("Are you sure? This action is irreversible.")) {
                if ($whatToDo == "delete"){
                    this.deleteAssignment($assignment);
                }
                else if ($whatToDo == "done"){
                    this.markAsDone($assignment);
                }
                else if ($whatToDo == "take"){
                    this.takeAssignment($assignment);
                }
            }
        },

        takeAssignment($assignment) {
            axios.patch('/assignments/' + $assignment.id + '/take').then(response => {    
                this.showedAssignment.assignee = this.user;
                this.showedAssignment.assignee_id = this.user.id;
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        markAsDone($assignment) {
            axios.patch('/assignments/' + $assignment.id + '/done').then(response => {    
                this.showedAssignment.done = true;
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        deleteAssignment($assignment) {
            axios.delete('/assignments/' + $assignment.id).then(response => {
                window.location.href = "/assignments";
            });
        }
    },
}
</script>

<style>

</style>