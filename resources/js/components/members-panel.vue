<template>
  <div class="container text-center py-4">
            <h2 class="font-bold text-2xl mb-4"> Group members </h2> 
            <ul v-if="members" class="w-full max-w-md mb-4">
              <li v-for="member in visibleMembers" :key="member.name"
                  class="p-4 mb-1 flex justify-between items-center">
                  <a :href="'/profile/' + member.username">
                      <div class="flex items-center">
                          <img class="w-10 h-10 object-cover rounded-full" :src="member.avatar" :alt="member.name">
                          <p class="ml-2 text-left text-gray-700 font-semibold font-sans tracking-wide">{{ member.name }}</p>
                      </div>
                  </a>
              </li>
            </ul>
            <p v-else> No members </p>

            <div class="flex w-full justify-between">
                <a
                    href="/invite-member" 
                    class="w-24 float-left m-2 rounded-full shadow border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    >Ivite
                </a>

                <a  
                    v-if="members.length > 0"
                    :href="'/all-members/' + groupid"
                    class="w-24 float-right m-2 rounded-full shadow border border-gray-300 py-2 px-4 text-black text-xs hover:text-gray-500 hover:bg-gray-100"
                    >Show all
                </a>
            </div>
            


        
  </div>
</template>

<script>
export default {
  props: ['user', 'members', 'groupid'],

  data(){
    return {
      visibleMembers: null
    }
  },

  mounted(){
    this.visibleMembers = this.members.sort(() => Math.random() - 0.5);
    if (this.visibleMembers.length > 4){
      this.visibleMembers = this.visibleMembers.slice(0,5);
    }
  }
}
</script>

<style>

</style>