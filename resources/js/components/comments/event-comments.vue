<template>
    <div class="mx-8">
        <h3 class="font-bold mb-4 text-underlined">Comments section</h3>
        <form class="mb-4" @submit.prevent="submit">
            <div class="bg-white shadow border rounded-lg h-20 py-3 px-8">
                <input type="hidden" name="_token" :value="csrf" />
                <textarea
                    id="commentArea"
                    name="text"
                    placeholder="Press enter to send..."
                    class="w-full resize-none focus:outline-none"
                >
                </textarea>
            </div>
        </form>

        <div class="border border-gray-300 rounded-lg mb-2">
            <div
                v-for="comment in pageOfItems"
                :key="comment.id"
                class="flex relative p-4 border-b border-b-gray-400"
            >
                <button
                    v-if="
                        comment.user_id == user.id ||
                        user.id == event.group.admin_id
                    "
                    v-on:click="deleteComment(comment)"
                    class="absolute top-3 right-3 focus:outline-none"
                >
                    <img class="w-4" src="/img/bin.png" alt="delete" />
                </button>
                <div class="mr-2">
                    <img
                        :src="comment.user.avatar"
                        alt="avatar"
                        class="rounded-full object-cover h-15 w-15 mr-2"
                    />
                </div>
                <div class="w-full">
                    <h5 class="font-bold mb-2">{{ comment.user.name }}</h5>
                    <p class="text-sm mb-3 break-words">
                        {{ comment.text }}
                    </p>
                    <p class="text-xs float-right">
                        {{
                            new Date(comment.created_at)
                                | dateFormat("HH:mm , DD.MM.YYYY")
                        }}
                    </p>
                </div>
            </div>
        </div>
        <div class="text-center text-sm">
            <jw-pagination
                :items="comments"
                @changePage="onChangePage"
                :pageSize="10"
            ></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "event"],

    data() {
        return {
            comments: [],
            pageOfItems: [],
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
        };
    },

    mounted() {
        this.getComments();
        document
            .getElementById("commentArea")
            .addEventListener("keypress", this.submitOnEnter);

        window.Echo.private(
            "commented_event." + this.event.id + ".group." + this.event.group_id
        ).listen("EventCommented", (e) => {
            e.event_comment.user = e.user;
            this.comments.unshift(e.event_comment);
        });

        window.Echo.private(
            "commented_event." +
                this.event.id +
                ".group." +
                this.event.group_id +
                ".deleted"
        ).listen("EventCommentDeleted", (e) => {
            this.comments = this.comments.filter(function (c) {
                return c.id !== e.event_comment.id;
            });
        });
    },

    methods: {
        getComments() {
            axios
                .get("/events/" + this.event.id + "/comments")
                .then((response) => {
                    this.comments = response.data;
                })
                .catch((error) => {
                    console.log(error.message);
                });
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        submitOnEnter(event) {
            if (event.which === 13) {
                event.target.form.dispatchEvent(
                    new Event("submit", { cancelable: true })
                );
                event.preventDefault();
            }
        },

        submit() {
            var commentArea = document.getElementById("commentArea").value;
            document.getElementById("commentArea").value = "";
            axios
                .post("/events/" + this.event.id + "/comments", {
                    event_id: this.event.id,
                    text: commentArea,
                })
                .then((response) => {
                    response.data["user"] = this.user;
                    this.comments.unshift(response.data);
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        deleteComment(comment) {
            if (!confirm("Are you sure you want to delete this comment?")) {
                return;
            }
            axios
                .delete(
                    "/events/" +
                        this.event.id +
                        "/comments/" +
                        comment.id +
                        "/destroy"
                )
                .then((response) => {
                    this.comments = this.comments.filter(function (c) {
                        return c.id !== response.data.id;
                    });
                });
        },
    },
};
</script>

<style>
</style>