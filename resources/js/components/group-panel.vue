<template>
  <div>
        <div class="container text-center">
            <img src="/storage/groups/default.jpg" alt="group-avatar" class="mx-auto mb-2 object-cover w-32 h-32 rounded-full">
           
            <h2 v-if="user.group" class="font-bold text-2xl"> {{ user.group.name }} </h2> 
            <h2 v-else class="font-bold text-2xl"> No Group </h2> 

            <p class="text-sm text-gray-500">group</p>
         
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