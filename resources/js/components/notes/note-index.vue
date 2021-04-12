<template>
    <div class="mb-6">
        <h3 class="text-center font-bold text-lg mb-8 text-gray-700">Notes</h3>
        <form @submit.prevent="submit">
            <div class="bg-white shadow border rounded-lg py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" /> 
                <div class="flex items-center justify-between w-full mb-4 p-2 bg-red-500 shadow text-white" v-if="errors.text">
                    {{ errors.text[0] }}
                </div>
                <textarea name="text" placeholder="something to remember..."
                    class="w-full h-24 resize-none focus:outline-none"
                    v-model="fields.text"
                >
                </textarea>
            </div>    
            <button type="submit" class="shadow float-right -mt-4 rounded-lg border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                Make a note
            </button>
        </form>
        
        
        <ul class="mt-20">
            <li v-for="note in pageOfItems" v-bind:key="note.id" class="max-w-full inline float-left mr-4">
                <div class="rounded overflow-hidden shadow-lg mb-4 border border-gray-100">
                    <div class="px-6 pt-4 break-words">
                        <p class="text-gray-700 text-base">
                            {{ note.text }}
                        </p>
                    </div>
                    <div class="px-6 pt-4 pb-2 mb-6 float-right">
                            <button 
                                class="w-6 h-4 font-bold text-red-500 text-xl hover:text-red-400 focus:outline-none" style="background: url(/storage/icons/bin.png)" 
                                type="button" data-toggle="modal" 
                                v-on:submit.prevent="deleteData(note)" @click="deleteData(note)">
                                <img class="w-4" src="/img/bin.png" alt="delete">
                            </button>
                    </div>
                </div>
            </li>
        </ul>
        
        <div class="mt-10 clear-both w-full text-center text-sm">
            <jw-pagination :items="savedNotes" @changePage="onChangePage" :pageSize="6"></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ['notes'],
    data() {
        return {
            savedNotes: this.notes,
            csrf: document.head.querySelector('meta[name="csrf-token"]').content,
            pageOfItems: [],
            fields: {},
            errors: {}
        };
    },
    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
        
        deleteData: function(note) {
            if (!confirm("Are you sure you want to delete this note?")) {
                return;
            }
            axios.delete('notes/' + note.id).then(response => {
                this.savedNotes = this.savedNotes.filter(function(e) { return e !== note })
            });
        },

        submit() {
            axios.post('notes', this.fields).then(response => {
                this.fields = {};
                this.savedNotes.push(response.data);
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        }
    }
};
</script>
