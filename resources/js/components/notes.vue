<template>
    <!-- TODO process all through VUE -->
    <div class="mb-6">
        <form action="notes" method="POST">
            <div class="bg-white shadow border rounded-lg py-6 px-8 mb-8">
                <input type="hidden" name="_token" :value="csrf" /> 
                <textarea name="text" placeholder="something to remember..."
                    class="w-full h-24 resize-none focus:outline-none">
                </textarea>
            </div>    
            <button type="submit" class="shadow float-right -mt-4 rounded-full border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100">
                Make a note
            </button>
        </form>
        
        
        <ul>
            <li v-for="note in pageOfItems" v-bind:key="note.id">
                <div class="max-w-xs rounded overflow-hidden shadow-lg mb-4 border border-gray-100">
                    <div class="px-6 py-4">
                        <p class="text-gray-700 text-base">
                            {{ note.text }}
                        </p>
                    </div>
                    <div class="px-6 pt-4 pb-2 mb-6 float-right">
                            <button 
                                class="w-4 h-4" style="background: url(/storage/icons/bin.png)" 
                                type="button" data-toggle="modal" 
                                v-on:submit.prevent="deleteData(note)" @click="deleteData(note)">
                            </button>
                    </div>
                </div>
            </li>
        </ul>
        
        <div class="mt-10 clear-both w-full text-center">
            <jw-pagination :items="savedNotes" @changePage="onChangePage" :pageSize="2"></jw-pagination>
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
            pageOfItems: []
        };
    },
    methods: {
        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },
        
        deleteData: function(note) {
            axios.delete('notes/' + note.id)
            .then(response => {
                this.savedNotes = this.savedNotes.filter(function(e) { return e !== note })
            });
        },
    }
};
</script>
