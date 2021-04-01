<template>
    <div>
        <div
            v-if="!editingBoard"
            class="relative bg-purple-100 border-gray-300 rounded-lg p-5 mb-5"
        >
            <p v-if="groupBoard">{{ groupBoard }}</p>
            <p
                v-if="!groupBoard && group.admin_id == user.id"
                class="text-gray-400 text-sm text-center"
            >
                Write here for others to see!
            </p>
            <p
                v-if="!groupBoard && group.admin_id != user.id"
                class="text-gray-400 text-center"
            >
                ...
            </p>

            <div
                v-if="group.admin_id == user.id"
                class="absolute top-1 right-1"
            >
                <button
                    v-on:click="editingBoard = !editingBoard"
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
        <div v-if="editingBoard" class="relative">
            <textarea
                id="boardArea"
                name="text"
                :placeholder="groupBoard"
                maxlength="500"
                class="relative w-full resize-none bg-purple-100 border-gray-300 rounded-lg p-5 mb-5 focus:outline-none"
            >
            </textarea>
            <div
                v-if="group.admin_id == user.id"
                class="absolute z-40 top-1 right-1"
            >
                <button v-on:click="editBoard()" class="focus:outline-none">
                    <img
                        class="w-9 border border-gray-300 rounded-full"
                        src="/img/edit.png"
                        alt="edit"
                    />
                </button>
                <button
                    v-on:click="editingBoard = !editingBoard"
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
        <div
            id="whiteboard"
            ref="whiteboard"
            class="container mb-2 pr-4 h-80 overflow-y-scroll min-h-screen"
        >
            <button
                v-on:click="loadOlderPosts()"
                class="bg-white text-sm text-center relative clear-both w-full h-8 px-4 pt-1 mb-4 border border-gray-300 hover:bg-purple-100 focus:outline-none focus:bg-purple-100"
            >
                Load older posts
            </button>
            <div
                v-for="post in posts"
                :key="post.id"
                class="bg-white relative clear-both w-full px-4 pt-4 mb-2 border border-gray-300 rounded-lg"
                v-bind:class="{
                    'float-right bg-purple-100': post.sender.id == user.id,
                    'pb-6': post.sender.id != user.id,
                }"
            >
                <button
                    v-if="
                        post.sender_id == user.id || user.id == group.admin_id
                    "
                    v-on:click="deletePost(post)"
                    class="absolute top-3 right-3"
                >
                    <img class="w-4" src="/img/bin.png" alt="delete" />
                </button>
                <div>
                    <h5 class="text-xs text-gray-500 absolute bottom-0">
                        {{ post.sender.name }}
                    </h5>
                    <div class="float-left mr-2">
                        <img
                            :src="post.sender.avatar"
                            alt="avatar"
                            class="rounded-full object-cover md:h-15 md:w-15 w-9 h-9 mr-2 mb-3"
                        />
                    </div>
                    <div>
                        <div class="text-sm mb-3 break-words p-4">
                            {{ post.text }}
                            <div v-if="post.image_path">
                                <img
                                    class="max-h-80 max-w-lg mx-auto"
                                    :src="'/storage/' + post.image_path"
                                    alt="image"
                                />
                            </div>
                            <div v-if="post.file_path">
                                <a
                                    v-if="post.file_name"
                                    class="font-semibold underline"
                                    :href="'/storage/' + post.file_path"
                                >
                                    {{ post.file_name }}
                                </a>
                                <a
                                    v-else
                                    class="font-semibold underline"
                                    :href="'/storage/' + post.file_path"
                                >
                                    attached file
                                </a>
                            </div>
                        </div>

                        <p class="text-xs md:float-right md:relative md:mb-2 absolute top-0 left-1">
                            {{
                                new Date(post.created_at)
                                    | dateFormat("HH:mm , DD.MM.YYYY")
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div v-on:submit.prevent="submit()" class="mb-4 clear-both">
            <div
                class="bg-white relative h-24 w-full px-4 pt-4 mt-4 mb-2 border border-gray-300 rounded-lg"
            >
                <textarea
                    id="postArea"
                    name="text"
                    placeholder="New post..."
                    maxlength="1000"
                    class="w-full resize-none focus:outline-none"
                >
                </textarea>
                <div class="absolute bottom-2 right-0">
                    <button
                        v-on:submit.prevent="submit()"
                        @click="submit()"
                        id="sendImage"
                        type="submit"
                        class="text-sm bg-blue-400 text-white rounded-full w-10 px-2 py-2 mr-2 hover:bg-blue-500 focus:outline-none"
                    >
                        <img src="/img/send_icon.png" alt="send" class="w-8" />
                    </button>
                </div>
            </div>
            <button @click="uploadImage = !uploadImage" class="relative">
                <img class="h-16" src="/img/image.png" alt="" srcset="" />
            </button>
            <button @click="uploadFile = !uploadFile" class="relative mb-5">
                <img class="h-16" src="/img/file.png" alt="" srcset="" />
            </button>
            <div v-if="uploadImage" class="mb-6">
                <div class="relative">
                    <label class="font-bold mb-4 text-underlined" for="image">
                        Select a photo
                    </label>
                    <div
                        class="text-sm mt-4 file-upload-wrapper"
                        data-text="Select a photo!"
                    >
                        <input
                            type="file"
                            id="image"
                            ref="image"
                            v-on:change="handleImageUpload()"
                        />
                    </div>
                </div>
            </div>
            <p
                class="flex items-center justify-between w-full my-5 p-2 bg-red-500 shadow text-white"
                v-if="imageTooBig"
            >
                Please choose a picture under 5MB.
            </p>

            <div class="clear-both mb-6"></div>
            <div v-if="uploadFile">
                <div class="relative mb-2 lg:float-left">
                    <label
                        class="font-bold mb-4 text-underlined"
                        for="file_path"
                    >
                        Select a file
                    </label>
                    <div
                        class="text-sm mt-4 file-upload-wrapper"
                        data-text="Select your file!"
                    >
                        <input
                            type="file"
                            id="file"
                            ref="file"
                            v-on:change="handleFileUpload()"
                        />
                    </div>
                </div>
                <div class="relative mt-5 mb-2 xl:float-right xl:mt-0 mt-4">
                    <label
                        class="font-bold mb-4 text-underline"
                        for="file_name"
                    >
                        Name your file
                    </label>
                    <br />
                    <input
                        id="file_name"
                        class="border-b border-gray-400 p-2 mb-4 w-64 focus:outline-none"
                        type="text"
                        name="file_name"
                        value=""
                        pattern="[a-zA-Z0-9_-]+"
                    />
                    <br />
                    <p class="text-xs mb-2">(max 1000 chars)</p>
                </div>
                <p
                    class="flex items-center justify-between w-full my-5 p-2 bg-red-500 shadow text-white"
                    v-if="fileTooBig"
                >
                    Please choose a file under 25MB.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "group"],
    data() {
        return {
            posts: [],
            newPost: "",
            image: "",
            groupBoard: this.group.board,
            uploadImage: false,
            uploadFile: false,
            editingBoard: false,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            imageTooBig: false,
            fileTooBig: false,
        };
    },

    mounted() {
        this.getPosts();
        document
            .getElementById("postArea")
            .addEventListener("keypress", this.submitOnEnter);

        window.Echo.private("groups." + this.group.id).listen(
            "NewWhiteboardPost",
            (e) => {
                e.post.sender = e.sender;
                this.posts.push(e.post);
                this.scrollPosts();
            }
        );
    },

    methods: {
        editBoard() {
            axios
                .patch("/groups/" + this.group.id + "/update-whiteboard", {
                    board: document.getElementById("boardArea").value,
                })
                .then((response) => {
                    this.groupBoard = response.data;
                    this.editingBoard = false;
                })
                .catch((error) => {
                    console.log(error.message);
                });
        },

        scrollPosts() {
            this.$nextTick(() => {
                this.$refs.whiteboard.scrollTop = 9999;
            });
        },

        getPosts() {
            axios
                .get("/groups/" + this.group.id + "/get-whiteboard-posts")
                .then((response) => {
                    this.posts = response.data.reverse();
                    this.scrollPosts();
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        loadOlderPosts() {
            axios
                .get(
                    "/groups/" +
                        this.group.id +
                        "/loadOlderPosts/" +
                        this.posts.length,
                    {}
                )
                .then((response) => {
                    this.posts = response.data.reverse().concat(this.posts);
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        submitOnEnter(event) {
            if (event.which === 13) {
                this.submit();
            }
        },

        handleImageUpload() {
            if (this.$refs.image.files[0].size > 5000000) {
                this.imageTooBig = true;
                return;
            }
            this.imageTooBig = false;
            this.image = this.$refs.image.files[0];
        },

        handleFileUpload() {
            if (this.$refs.file.files[0].size > 25000000) {
                this.fileTooBig = true;
                return;
            }
            this.fileTooBig = false;
            this.file = this.$refs.file.files[0];
        },

        submit() {
            var postArea = document.getElementById("postArea").value;

            if (postArea == "" && !this.image && !this.file) {
                return;
            }
            document.getElementById("postArea").value = "";

            var formData = new FormData();
            if (this.image && document.getElementById("image")) {
                formData.append("image", this.image);
                document.getElementById("image").value = "";
            }
            if (this.file && document.getElementById("file_name")) {
                formData.append("file", this.file);
                var fileName = document.getElementById("file_name").value;
                document.getElementById("file").value = "";
                if (fileName.trim() != "") {
                    formData.append("file_name", fileName);
                    document.getElementById("file_name").value = "";
                }
            }
            formData.append("text", postArea);
            formData.append("group_id", this.group.id);

            axios
                .post(
                    "/groups/" + this.group.id + "/whiteboard-post",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                )
                .then((response) => {
                    response.data["user"] = this.user;
                    this.posts.push(response.data);
                    document.getElementById("postArea").value = "";
                    this.uploadImage = false;
                    this.uploadFile = false;
                    this.$nextTick(() => {
                        this.imageTooBig = false;
                        this.fileTooBig = false;
                        this.$refs.whiteboard.scrollTop = 9999;
                    });
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        deletePost(post) {
            if (!confirm("Are you sure you want to delete this post?")) {
                return;
            }
            axios
                .delete(
                    "/groups/" +
                        this.group.id +
                        "/whiteboard-post-delete/" +
                        post.id,
                    {}
                )
                .then((response) => {
                    this.posts = this.posts.filter(function (p) {
                        return p != post;
                    });
                    console.log(response);
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },
    },
};
</script>

<style>
</style>