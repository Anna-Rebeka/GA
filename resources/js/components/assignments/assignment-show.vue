<template>
    <div>
        <div class="p-8 mr-2 mb-2">
            <div class="float-right">
                <div
                    v-if="
                        user.id == assignment.group.admin_id ||
                        shownAssignment.author_id == user.id
                    "
                    class="mb-5"
                >
                    <button
                        v-if="!shownAssignment.done"
                        @click="checkWithUser(shownAssignment, 'done')"
                        class="rounded-lg py-2 px-4 mr-2 text-white text-sm bg-green-400 hover:bg-green-300 focus:outline-none"
                    >
                        Done
                    </button>
                    <div
                        class="float-right rounded-lg w-16 px-2 text-center py-2 mr-2 text-white text-sm bg-gray-400 hover:bg-gray-500 focus:outline-none"
                    >
                        <a :href="shownAssignment.id + '/edit'" class="p-3">
                            Edit
                        </a>
                    </div>

                    <button
                        @click="checkWithUser(shownAssignment, 'delete')"
                        class="rounded-lg w-16 px-2 py-2 mr-2 text-white text-sm bg-red-400 hover:bg-red-300 focus:outline-none"
                    >
                        Delete
                    </button>
                </div>
            </div>
            <div class="float-right">
                <div v-if="!takenAssignment">
                    <div
                        v-if="
                            shownAssignment.max_assignees == null ||
                            shownAssignment.users.length <
                                shownAssignment.max_assignees
                        "
                    >
                        <button
                            @click="checkWithUser(shownAssignment, 'take')"
                            class="text-sm bg-blue-400 text-white rounded-lg w-16 py-2 hover:bg-blue-500 mr-3 focus:outline-none"
                        >
                            Take
                        </button>
                    </div>
                </div>
            </div>
            <div
                class="mt-5 clear-both overflow-ellipsis overflow-hidden ... max-w-sm"
            >
                <h2
                    v-if="shownAssignment.done"
                    style="color: #6cc2bd"
                    class="font-bold text-2xl mb-4"
                >
                    {{ shownAssignment.name }}
                </h2>
                <h2
                    v-else
                    style="color: #f67e7d"
                    class="font-bold text-2xl mb-4"
                >
                    {{ shownAssignment.name }}
                </h2>
            </div>
            <div class="mt-7 grid grid-cols-2 gap-2">
                <div
                    class="py-2 px-6 mb-2 mr-2 border-b border-gray-200 rounded"
                >
                    <p
                        v-if="shownAssignment.on_time"
                        style="color: #f67e7d"
                        class="mb-2"
                    >
                        <strong>On time</strong>
                    </p>
                    <p v-else style="color: #5a819e" class="mb-2">
                        <strong>Deadline</strong>
                    </p>

                    <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                        {{
                            new Date(shownAssignment.due)
                                | dateFormat("DD.MM.YYYY , HH:mm")
                        }}
                    </div>
                </div>
                <div
                    class="mb-2 rounded border-b border-gray-200 px-6 pl-6 pt-2 pb-2"
                >
                    <p class="mb-2"><strong>By Who</strong></p>
                    <p class="bg-white p-2 rounded">
                        {{ author }}
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div
                    class="py-2 px-6 mb-2 mr-2 border-b border-gray-200 rounded"
                >
                    <p class="mb-2">
                        <strong>For how long:</strong>
                    </p>
                    <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                        <p v-if="shownAssignment.duration">
                            {{ shownAssignment.duration }}
                        </p>
                        <p v-else>not set</p>
                    </div>
                </div>
                <div
                    class="mb-2 rounded border-b border-gray-200 px-6 pl-6 pt-2 pb-2"
                >
                    <p class="mb-2"><strong>Max participants:</strong></p>
                    <div class="bg-white p-2 rounded">
                        <p
                            v-if="
                                shownAssignment.max_assignees &&
                                shownAssignment.max_assignees >
                                    shownAssignment.users.length
                            "
                        >
                            {{ shownAssignment.max_assignees }} (free to take)
                        </p>
                        <p
                            v-if="
                                shownAssignment.max_assignees &&
                                shownAssignment.max_assignees <=
                                    shownAssignment.users.length
                            "
                        >
                            {{ shownAssignment.max_assignees }} (already taken)
                        </p>
                        <p v-if="shownAssignment.max_assignees == null">
                            not set
                        </p>
                    </div>
                </div>
            </div>

            <div
                v-if="shownAssignment.users"
                class="mb-2 rounded border-b border-gray-200 px-6 pl-6 pt-2 pb-4"
            >
                <p class="mb-2"><strong>For Who</strong></p>
                <div class="bg-white p-2 rounded">
                    <p
                        v-for="assignee in shownAssignment.users"
                        :key="assignee.id"
                    >
                        {{ assignee.name }}
                    </p>
                </div>
            </div>
            <div class="py-4 px-6 mb-2 border-b border-gray-200 rounded">
                <p class="mb-2"><strong>What about</strong></p>
                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    {{ shownAssignment.description }}
                </div>
            </div>
            <div
                v-if="user.id == assignment.author_id || assignment_users_ids.includes(user.id) || user.id == assignment.group.admin_id"
                class="py-4 px-6 mb-2 border-b border-gray-200 rounded"
            >
                <p class="mb-2"><strong>Author commented (privatly)</strong></p>
                <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2">
                    <div
                        v-if="!editingComment"
                        class="relative bg-purple-100 border-gray-300 rounded-lg p-5 mb-5"
                    >
                        <p v-if="authorComment">{{ authorComment }}</p>
                        <div
                            v-if="assignment.group.admin_id == user.id"
                            class="absolute top-1 right-1"
                        >
                            <button
                                v-on:click="editingComment = !editingComment"
                                class="focus:outline-none"
                            >
                                <img
                                    class="w-9 border border-gray-300 rounded-full"
                                    src="/img/edit.png"
                                    alt="edit"
                                />
                            </button>
                        </div>
                    </div>
                    <div v-if="editingComment" class="relative">
                        <textarea
                            id="author_comment"
                            name="text"
                            :placeholder="authorComment"
                            maxlength="500"
                            class="relative w-full resize-none bg-purple-100 border-gray-300 rounded-lg p-5 mb-5 focus:outline-none"
                        >
                        </textarea>
                        <div
                            v-if="assignment.group.admin_id == user.id"
                            class="absolute z-40 top-1 right-1"
                        >
                            <button
                                v-on:click="editComment()"
                                class="focus:outline-none"
                            >
                                <img
                                    class="w-9 border border-gray-300 rounded-full"
                                    src="/img/edit.png"
                                    alt="edit"
                                />
                            </button>
                            <button
                                v-on:click="editingComment = !editingComment"
                                class="focus:outline-none"
                            >
                                <img
                                    class="w-9 border border-gray-300 rounded-full"
                                    src="/img/cancel.png"
                                    alt="edit"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <assignment-show-file-upload
            :user="user"
            :assignment="assignment"
            :assignment_users_ids="assignment_users_ids"
        ></assignment-show-file-upload>
    </div>
</template>

<script>
export default {
    props: ["user", "assignment", "author", "assignment_users_ids"],
    data() {
        return {
            shownAssignment: this.assignment,
            takenAssignment: this.assignment.taken,
            editingComment: false,
            authorComment: this.assignment.author_comment,
        };
    },

    methods: {
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
                    this.shownAssignment.users.push(this.user);
                    this.assignment_users_ids.push(this.user.id);
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
                    this.shownAssignment.done = true;
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

        editComment() {
            axios
                .patch("/assignments/" + this.assignment.id + "/edit-comment", {
                    author_comment: document.getElementById("author_comment").value,
                })
                .then((response) => {
                    this.authorComment = response.data;
                    this.editingComment = false;
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