<template>
    <div>
        <div class="container mx-auto flex justify-center">
            <div class="px-12 py-8 bg-gray-200 border-gray-300 rounded-lg">
                <div class="font-bold text-lg mb-4 text-center">
                    The more the merrier!
                </div>
                <p class="mb-4">Invite a new member by e-mail.</p>
                <div>
                    <div v-for="field in fields" :key="field.id">
                        <div class="mb-4">
                            <label for="email" class="mb-2"
                                >E-Mail Address:
                            </label>
                            <input
                                v-model="field.email"
                                id="email"
                                type="email"
                                class="form-control"
                                name="email"
                                value=""
                                required
                                autocomplete="email"
                            />
                        </div>
                    </div>
                    <div class="float-left mb-10">
                        <button
                            @click="AddField"
                            class="rounded border border-gray-300 bg-white py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                        >
                            New Field
                        </button>
                    </div>

                    <div class="relative clear-both">
                        <button
                            type="submit"
                            class="rounded-lg absolute right-0 bottom-0 border border-gray-300 bg-white py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                        >
                            Send Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user"],
    data() {
        return {
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
            fields: [{ id: 0, email: "" }],
            aiId: 0,
        };
    },
    created: function () {},
    methods: {
        AddField: function () {
            this.aiId += 1;
            this.fields.push({ id: this.aiId, email: "" });
            console.log(this.fields);
        },

        //        <form method="POST" action="{{ route('invite') }}">


    },
};
</script>

<style>
</style>