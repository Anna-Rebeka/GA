<template>
<div>
    <div class="p-8 mr-2 mb-8">
        <div class="float-right">
            <button 
                v-if="going && !going.includes(user.id)"
                @click="joinEvent()"
                class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100 focus:outline-none">
                Join
            </button> 
            <button 
                v-else
                @click="leaveEvent()" 
                class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-red-200 hover:text-gray-500 hover:bg-red-100 focus:outline-none">
                Leave
            </button> 
             <button 
                v-if="event.host_id == user.id"
                @click="checkWithUser()"
                class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-white text-xs bg-red-400 hover:text-gray-500 hover:bg-red-200 focus:outline-none">
                Delete
            </button> 
        </div>
        <div class="overflow-ellipsis overflow-hidden ...  max-w-sm">
            <h2 class="font-bold text-2xl mb-4"> {{ event.name }} </h2>
        </div>
        <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>Host</strong></p>
            <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2" >
                {{ host }}
            </div>
        </div>
            <div class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block"> <strong>When</strong><p class="bg-white p-2 rounded">{{  new Date(event.event_time) | dateFormat('DD.MM.YYYY , HH:mm') }}</p></div>   
            <div class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block"> <strong>Where</strong><p class="bg-white p-2 rounded">{{ event.event_place }}</p></div>   
        <div v-if="event.description" class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>What about</strong></p>
            <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2" >
                {{ event.description }}
            </div>
        </div>
        <div class="pt-6 px-6 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>Who's coming</strong></p>
            <div class="bg-white rounded mb-4 pt-4 pb-2" >
                <ul v-for="goingUser in pageOfItems" :key="goingUser.name">
                    <li class="flex items-center ml-2 mb-2">
                        <img class="w-10 h-10 object-cover rounded-full mr-2" :src="goingUser.avatar" alt="">
                        {{ goingUser.name }}
                    </li>
                </ul>
            </div>
            <div class="text-center text-sm">
                <jw-pagination :items="savedUsers" @changePage="onChangePage" :pageSize="10"></jw-pagination>
            </div>
        </div>
    </div>
    <event-comments :user ="this.user" :event="this.event"></event-comments>
</div>
    
</template>

<script>
export default {
    props: ['user', 'going','event', 'host'],

     data() {
        return {
            savedUsers: this.event.users,
            pageOfItems: [],
        };
    },
    
    methods: {
        reload() {
            this.$forceUpdate();
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        checkWithUser() {
            if (confirm("Are you sure? This action is irreversible.")) {
                this.destroyEvent();
            }
        },

        joinEvent() {
                axios.post('/events/' + this.event.id + '/join').then(response => {
                    this.going.push(this.user.id);
                    this.event.users.push(this.user);
                    this.reload();
                }).catch(error => {
                    if (error.response.status == 422){
                        this.errors = error.response.data.errors;
                        console.log(this.errors);
                    }
                    console.log(error.message);
                });
        },

        leaveEvent() {
            axios.post('/events/' + this.event.id + '/leave').then(response => {
                var indexEU = this.event.users.indexOf(this.user);
                var indexG = this.going.indexOf(this.user.id);
                this.event.users.splice(indexEU, 1);
                this.going.splice(indexG, 1);
                this.reload();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        destroyEvent() {
            axios.delete('/events/' + this.event.id).then(response => {
                window.location.href = "/events";
            });
        }
    }

}
</script>

<style>

</style>