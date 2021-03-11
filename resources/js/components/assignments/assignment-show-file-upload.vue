<template>
    <div>
        <div class="px-8 mr-2 mb-16">
            <div class="mb-10" v-if="assignmentsFiles.length > 0">
                <div v-for="file in pageOfItems" :key="file.id">
                    <a
                        class="float-left mb-1"
                        :href="'/storage/' + file.file_path"
                        >{{ file.file_name }}</a
                    >
                    <div v-if="file.user_id == user.id">
                        <button
                            v-on:click="deleteFile(file)"
                            class="ml-6 float-left text-red-500 font-bold focus:outline-none"
                        >
                            x
                        </button>
                    </div>
                    <div class="clear-both"></div>
                </div>
                <div class="mb-10 clear-both w-full text-center text-sm">
                    <jw-pagination
                        :items="assignmentsFiles"
                        @changePage="onChangePage"
                        :pageSize="5"
                    ></jw-pagination>
                </div>
            </div>

            <form class="clear-both" v-on:submit.prevent="submit()">
                <input type="hidden" name="_token" :value="csrf" />
                <div class="float-left">
                    <label
                        class="font-bold mb-4 text-underlined"
                        for="file_path"
                    >
                        File upload
                    </label>

                    <div
                        class="text-sm mt-4 file-upload-wrapper"
                        data-text="Select your file!"
                    >
                        <input
                            name="file_path"
                            id="file_path"
                            type="file"
                            value="file_path"
                            ref="file_path"
                            v-on:change="handleFileUpload()"
                            required
                        />
                    </div>
                </div>

                <div class="float-right">
                    <label
                        class="font-bold mb-4 text-underline"
                        for="file_name"
                    >
                        Name your file
                    </label>
                    <input
                        class="border-b border-gray-400 p-2 mb-4 w-full focus:outline-none"
                        type="text"
                        name="file_name"
                        id="file_name"
                        value=""
                        pattern="[a-zA-Z0-9_-]+"
                        required
                    />
                    <br />
                    <p class="text-xs mb-2">
                        (please use only letters, numbers, "_" and "-")
                    </p>
                </div>

                <div class="mb-6 clear-both">
                    <button
                        type="submit"
                        v-on:submit.prevent="submit()"
                        class="float-right text-sm bg-blue-400 text-white rounded-lg px-2 w-16 py-2 hover:bg-blue-500 mr-4 focus:outline-none"
                    >
                        Submit
                    </button>
                </div>
            </form>
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
            assignmentsFiles: [],
            newFile: null,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            pageOfItems: [],
        };
    },

    mounted() {
        this.getAssignmentFiles();
        console.log(this.assignmentsFiles);
    },

    methods: {
        getAssignmentFiles() {
            axios
                .get("/assignments/" + this.assignment.id + "/get-files")
                .then((response) => {
                    this.assignmentsFiles = response.data;
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        handleFileUpload() {
            this.newFile = this.$refs.file_path.files[0];
        },

        deleteFile(file) {
            axios
                .delete("/assignments/file-delete/" + file.id)
                .then((response) => {
                    this.assignmentsFiles = this.assignmentsFiles.filter(
                        function (e) {
                            return e !== file;
                        }
                    );
                });
        },

        submit() {
            var formData = new FormData();
            if (this.newFile) {
                formData.append("file_path", this.newFile);
                var fileName = document.getElementById("file_name").value;
                if (fileName.trim() != "") {
                    formData.append("file_name", fileName);
                }
            }
            axios
                .post(
                    "/assignments/" + this.assignment.id + "/file-upload",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                )
                .then((response) => {
                    this.assignmentsFiles.push(response.data);
                    this.uploadImage = false;
                    this.uploadFile = false;
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