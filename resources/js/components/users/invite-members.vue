<template>
    <div>
        <div class="container mx-auto flex justify-center">
            <div class="px-12 py-8 bg-gray-200 border-gray-300 rounded-lg">
                <div class="font-bold text-lg mb-4 text-center">
                    The more the merrier!
                </div>
                <p class="mb-4">Invite a new member by e-mail.</p>
                <form v-on:submit.prevent="submit()">
                    <input type="hidden" name="_token" :value="csrf" /> 
                    <div v-for="field in fields" :key="field.id">
                        <div class="mb-4">
                            <button 
                                @click="removeField(field.id)"
                                class="font-bold text-red-500 focus:outline-none">
                                x
                            </button>
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
                            @click="addField"
                            class="rounded border border-gray-300 bg-white py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                        >
                            New Field
                        </button>
                    </div>

                    <div class="relative clear-both">
                        <button
                            type="submit"
                            v-on:submit.prevent="submit()"
                            class="rounded-lg absolute right-0 bottom-0 border border-gray-300 bg-white py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                        >
                            Send Request
                        </button>
                    </div>
                </form>
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
        addField() {
            this.aiId += 1;
            this.fields.push({ id: this.aiId, email: "" });
            console.log(this.fields);
        },

        removeField(fieldId) {
            this.fields = this.fields.filter(field => field.id != fieldId);
            console.log(this.fields);
        },

        submit() {
            this.fields = this.fields.map(field => field['email']);
            axios.post('/invite-member', this.fields).then(response => {
                this.fields = {};
                console.log(response.data);

            }).catch(error => {
                console.log(error.message);
            });
        }

        //        <form method="POST" action="{{ route('invite') }}">


    },
};
</script>

<style>
</style>