import Vue from 'vue';
import dropdown from './components/dropdown.vue';
import groupPanel from './components/group-panel.vue';
import membersPanel from './components/members-panel.vue';
import changeGroup from './components/groups/change-group.vue';
import groupUsers from './components/users/group-members.vue';
import notes from './components/notes/note-index.vue';
import events from './components/events/group-events.vue';
import eventShow from './components/events/event-show.vue';
import eventComments from './components/comments/event-comments.vue';

import JwPagination from 'jw-vue-pagination';

require('masonry-layout');
require('./bootstrap');

Vue.component('dropdown', dropdown);
Vue.component('group-panel', groupPanel);
Vue.component('members-panel', membersPanel);
Vue.component('change-group', changeGroup);
Vue.component('group-members', groupUsers);
Vue.component('note-index', notes);
Vue.component('group-events', events);
Vue.component('event-show', eventShow);
Vue.component('event-comments', eventComments);
Vue.component('jw-pagination', JwPagination);

var app = new Vue({
   el: '#app',
});

