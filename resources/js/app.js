import Vue from 'vue';
import navbar from './components/navbar/navbar.vue';
import messagesLink from './components/navbar/messages-link.vue';
import groupSelection from './components/navbar/group-selection.vue';
import groupPanel from './components/group-panel.vue';
import emptyGroupPanel from './components/empty-group-panel.vue';
import membersPanel from './components/members-panel.vue';
import userShow from './components/users/user-show.vue';
import userEdit from './components/users/user-edit.vue';
import groupUsers from './components/users/group-members.vue';
import inviteUsers from './components/users/invite-members.vue';
import userEvents from './components/users/user-events.vue';
import userAssignments from './components/users/user-assignments.vue';
import notes from './components/notes/note-index.vue';
import events from './components/events/group-events.vue';
import eventShow from './components/events/event-show.vue';
import eventsTable from './components/events/events-table.vue';
import eventComments from './components/comments/event-comments.vue';
import assignments from './components/assignments/group-assignments.vue';
import assignmentShow from './components/assignments/assignment-show.vue';
import assignmentFileUpload from './components/assignments/assignment-show-file-upload.vue';
import assignmentsTable from './components/assignments/assignments-table.vue';
import assignmentComments from './components/comments/assignment-comments.vue';
import chatroomsIndex from './components/chatrooms/chatrooms-index.vue';
import chatroomsShow from './components/chatrooms/chatrooms-show.vue';
import groupEdit from './components/groups/group-edit.vue';
import groupWhiteboard from './components/groups/group-whiteboard.vue';
import groupStatistics from './components/groups/group-statistics.vue';

import JwPagination from 'jw-vue-pagination';

import VueFilterDateFormat from 'vue-filter-date-format';

require('masonry-layout');
require('./bootstrap');

Vue.use(VueFilterDateFormat);

Vue.component('navbar', navbar);
Vue.component('messages-link', messagesLink);
Vue.component('group-selection', groupSelection);
Vue.component('group-panel', groupPanel);
Vue.component('empty-group-panel', emptyGroupPanel);
Vue.component('members-panel', membersPanel);
Vue.component('user-show', userShow);
Vue.component('user-edit', userEdit);
Vue.component('group-members', groupUsers);
Vue.component('invite-members', inviteUsers);
Vue.component('user-events', userEvents);
Vue.component('user-assignments', userAssignments);
Vue.component('note-index', notes);
Vue.component('group-events', events);
Vue.component('event-show', eventShow);
Vue.component('event-comments', eventComments);
Vue.component('events-table', eventsTable);
Vue.component('group-assignments', assignments);
Vue.component('assignment-show', assignmentShow);
Vue.component('assignment-show-file-upload', assignmentFileUpload);
Vue.component('assignment-comments', assignmentComments);
Vue.component('assignments-table', assignmentsTable);
Vue.component('jw-pagination', JwPagination);
Vue.component('chatrooms-index', chatroomsIndex);
Vue.component('chatrooms-show', chatroomsShow);
Vue.component('group-edit', groupEdit);
Vue.component('group-whiteboard', groupWhiteboard);
Vue.component('group-statistics', groupStatistics);


var app = new Vue({
   el: '#app',
});

