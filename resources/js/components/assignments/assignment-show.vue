<template>
<div>
    <div class="p-8 mr-2 mb-8">
        <div class="float-right">
            <!--
            <button 
                v-if="going && !going.includes(user.id)"
                @click="joinEvent()"
                class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100 focus:outline-none">
                Join
            </button> 
            <button 
                v-else-if="event.host_id == user.id"
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
            -->
        </div>
        <h2 class="font-bold text-2xl mb-4"> {{ assignment.name }} </h2>
        <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>Due</strong></p>
            <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2" >
                {{  new Date(assignment.due) | dateFormat('DD.MM.YYYY , HH:mm') }}
            </div>
        </div>
            <div class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block"> <strong>By Who</strong><p class="bg-white p-2 rounded">{{ assignment.author.name }}</p></div>   
            <div v-if="assignment.assignee" class="mb-2 rounded bg-gray-100 px-6 p-4 w-2/5 inline-block"> <strong>For Who</strong><p class="bg-white p-2 rounded">{{ assignment.assignee.name }}</p></div>   
        <div class="py-2 px-6 mb-2 mr-2 bg-gray-100 rounded">
            <p class="mb-2"> <strong>What about</strong></p>
            <div class="bg-white rounded mb-2 pl-2 pt-2 pb-2" >
                {{ assignment.description }}
            </div>
        </div>
    </div>
</div>
    
</template>

<script>
export default {
    props: ['user', 'assignment'],
}
</script>

<style>

</style>