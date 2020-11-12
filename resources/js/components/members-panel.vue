<template>
  <div class="container text-center">
       
            <h2 class="font-bold text-2xl mb-2"> Group Members </h2> 

            <ul v-if="members" id="example-1">
              <li v-for="member in members" :key="member.name">
                <a :href="'/profile/' + member.username">{{ member.name }}</a>
              </li>
            </ul>
            <p v-else> No members </p>

     
            <a
                href="/invite-member" 
                class="relative top-6 rounded-full border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                >Ivite Members
            </a>

        
  </div>
</template>

<script>
export default {
  props: ['user', 'members'],

  data(){
    return {
    }
  },
}
</script>

<style>

</style>