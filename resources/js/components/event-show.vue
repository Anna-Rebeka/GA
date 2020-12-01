<template>
    <div>
        <h2 class="font-bold text-2xl mb-4"> {{ event.name }} </h2>
        <p v-if="event.description" class="text-sm">What about: {{ event.description }}</p>
        <div class="mr-4 float-right">
            <button 
                v-if="going && !going.includes(user.id)"
                @click="joinEvent()"
                class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100 focus:outline-none">
                Join
            </button> 
            <button 
                v-else-if="event.author_id == user.id"
                @click="checkWithUser()"
                class="rounded-lg border border-gray-300 py-2 px-4 mr-2 text-white text-xs bg-red-400 hover:text-gray-500 hover:bg-red-200 focus:outline-none">
                Delete
            </button> 
            <button 
                v-else
                @click="leaveEvent()"
                class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-red-200 hover:text-gray-500 hover:bg-red-100 focus:outline-none">
                Leave
            </button> 
        </div>
        <p>When : {{ event.event_time }}</p>
        <p>Where : {{ event.event_place }}</p>
        <p>Who's coming : 
            <ul v-for="goingUser in event.users" :key="goingUser.name">
                <li class="flex items-center mb-2">
                    <img class="w-12 object-cover rounded-full mr-2" :src="goingUser.avatar" alt="">
                    {{ goingUser.name }}
                </li>
            </ul>

        </p>
        
    </div>
</template>

<script>
export default {
    props: ['user', 'going','event'],
    
    methods: {
        reload() {
            this.$forceUpdate();
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
                window.location.href = '/' + this.event.group.id + '/events';
            });
        }
    }

}
</script>

<style>

</style>