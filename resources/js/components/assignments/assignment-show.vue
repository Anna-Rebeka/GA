<template>
    <div>
        <div class="p-8 mr-2 mb-2">
            <div class="float-right">
                <div v-if="showedAssignment.author_id == user.id">
                    <button
                        v-if="!showedAssignment.done"
                        @click="checkWithUser(showedAssignment, 'done')"
                        class="rounded-lg py-2 px-4 mr-2 text-white text-sm bg-green-400 hover: hover:bg-green-300 focus:outline-none"
                    >
                        Done
                    </button>
                    <!--
                    <a
                        :href="showedAssignment.id + '/edit'" 
                        class="rounded-lg border border-gray-300 px-4 py-2 mr-2 text-xs bg-white hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                    >
                        Edit
                    </a>
                    -->
                    <button
                        @click="checkWithUser(showedAssignment, 'delete')"
                        class="rounded-lg w-16 px-2 py-2 mr-2 text-white text-sm bg-red-400 hover:bg-red-300 focus:outline-none"
                    >
                        Delete
                    </button>
                </div>
            </div>
            <div class="float-right">
                <div v-if="!takenAssignment">
                    <div v-if="showedAssignment.max_assignees == null || showedAssignment.users.length  < showedAssignment.max_assignees">
                        <button
                            @click="checkWithUser(showedAssignment, 'take')"
                            class="text-sm bg-blue-400 text-white rounded-lg w-16 py-2 hover:bg-blue-500 mr-3 focus:outline-none"
                        >
                            Take
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="overflow-ellipsis overflow-hidden ...  max-w-sm">
                <h2 v-if="showedAssignment.done" style="color: #6cc2bd;" class="font-bold text-2xl mb-4">{{ showedAssignment.name }}</h2>
                <h2 v-else style="color: #f67e7d;" class="font-bold text-2xl mb-4">{{ showedAssignment.name }}</h2>
            </div>
            <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded w-2/5 inline-block">

                <p v-if="showedAssignment.on_time" style="color: #f67e7d;" class="mb-2"><strong>On time</strong></p>
                <p v-else style="color: #5a819e;" class="mb-2"><strong>Deadline</strong></p>

                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    {{
                        new Date(showedAssignment.due)
                            | dateFormat("DD.MM.YYYY , HH:mm")
                    }}
                </div>
            </div>
            <div class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block">
                <strong>By Who</strong>
                <p class="bg-white p-2 rounded">
                    {{ showedAssignment.author.name }}
                </p>
            </div>
            <div
                v-if="showedAssignment.users"
                class="mb-2 rounded bg-gray-100 px-6 p-4"
            >
                <strong>For Who</strong>
                <div class="bg-white p-2 rounded">
                    <p v-for="assignee in showedAssignment.users" :key="assignee.id">
                        {{ assignee.name }}
                    </p>
                </div>
            </div>
            <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
                <p class="mb-2"><strong>What about</strong></p>
                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    {{ showedAssignment.description }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "assignment"],
    data() {
        return {
            showedAssignment: this.assignment,
            takenAssignment: false,
        };
    },

    mounted() {
        this.isAssigned();
    },

    methods: {
        isAssigned(){
            axios.get('/assignments/' + this.showedAssignment.id + '/is-taken-by-auth').then(response => {
                this.takenAssignment = response.data;
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                if (error.response.status == 404){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        checkWithUser($assignment, $whatToDo) {
            if (confirm("Are you sure? This action is irreversible.")) {
                if ($whatToDo == "delete") {
                    this.deleteAssignment($assignment);
                } else if ($whatToDo == "done") {
                    this.markAsDone($assignment);
                } else if ($whatToDo == "take") {
                    this.takeAssignment($assignment);
                }
            }
        },

        takeAssignment($assignment) {
            axios
                .patch("/assignments/" + $assignment.id + "/take")
                .then((response) => {
                    this.showedAssignment.users.push(this.user);
                    this.takenAssignment = true;
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        markAsDone($assignment) {
            axios
                .patch("/assignments/" + $assignment.id + "/done")
                .then((response) => {
                    this.showedAssignment.done = true;
                    this.reload();
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        deleteAssignment($assignment) {
            axios.delete("/assignments/" + $assignment.id).then((response) => {
                window.location.href = "/assignments";
            });
        },
    },
};
</script>

<style>
</style>