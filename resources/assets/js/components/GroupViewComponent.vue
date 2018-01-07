<template>
    <div class="group-view columns is-padded-top">
        <div class="column is-three-quarters">
            <div class="group-info feed-section">
                <div class="group-settings-btn is-pulled-right">
                    <a :href="'/groups/' + groupId + '/options'"><i class="fa fa-cog"></i></a>
                </div>
                <h1 class="title is-2">{{ name }}</h1>
                <p>{{ description }}</p>
            </div>
        </div>
        <div class="column is-one-quarter">
            <div class="group-members">
                <h2 class="title is-6">Members</h2>
                <group-members :members="members"></group-members>
            </div>
        </div>
    </div>
</template>

<script>
const axios = require('axios');
const parse = require('url-parse');
const GroupMembers = require('./GroupMembersComponent.vue');

import { mapState } from 'vuex';

export default {
    props: ['groupId', 'auth'],

    computed: {
        ...mapState({
            name: state => state.group.name,
            description: state => state.group.description,
            members: state => state.group.members
        })
    },

    mounted: function() {
        const id = this.groupId;

        axios.get(`/graphql?query=query+groupAndMembers{ groups(id: ${parseInt(id)}){id, name, description, public, invite_code, members{id, username, avatar}}}`)
            .then(response => {
                const result = response.data;

                this.$store.dispatch('setGroup', result.data.groups[0]);
            });
    },

    components: {
        'group-members': GroupMembers
    }
}
</script>
