<template>
  <div>
    <div class="container text-center">
      <img src="/storage/groups/avatars/default.jpg" alt="group-avatar" class="rounded shadow-lg mx-auto mb-2 object-cover w-32 h-32">
      
      <div v-if="user.group"> 
        <h2  class="font-bold text-2xl"> {{ user.group.name }} </h2> 
        <p class="text-sm text-gray-500">group</p>
      </div>   
      <div v-else> 
        <h2 class="font-bold text-2xl mb-6"> No Group </h2> 
        <a
          href="/create-group" 
          class="top-6 rounded-full border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
        >Create a group
        </a>
      </div> 
    </div>
  </div>
</template>

<script>
export default {
  props: ['user'],

  data(){
    return {
    }
  },
}
</script>

<style>

</style>