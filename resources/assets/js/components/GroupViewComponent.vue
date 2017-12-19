<template>
    <div class="group-view columns is-padded-top">
        <div class="column is-three-quarters">
            <div class="group-info feed-section">
                <h1 class="title is-2">{{ name }}</h1>
                <p>{{ description }}</p>
            </div>
        </div>
        <div class="column is-one-quarter">
            <div class="group-options" v-if="auth">
                <h2 class="title is-6">Options</h2>
                <group-options :privacy="privacy" :update-privacy="updatePrivacy"></group-options>
            </div>
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
const GroupOptions = require('./GroupOptionsComponent.vue');

import { mapState } from 'vuex';

export default {
    props: ['groupId', 'auth'],

    computed: {
        ...mapState({
            name: state => state.group.name,
            description: state => state.group.description,
            id: state => state.group.id,
            members: state => state.group.members,
            privacy: state => state.group.privacy
        })
    },

    methods: {
        updatePrivacy: function(e) {
            const privacy = !this.privacy;

            axios.post(`/graphql?query=mutation+groups{groupPrivacy(id: ${this.id}, privacy: ${privacy ? 1 : 0}){id, public}}`)
                .then(response => {
                    const result = response.data;

                    if(result.errors) {
                        // display toast
                        return;
                    }

                    this.$store.commit('setGroupPrivacy', result.groupPrivacy.public);
                })
        }
    },

    mounted: function() {
        const id = this.groupId;

        axios.get(`/graphql?query=query+groupAndMembers{ groups(id: ${parseInt(id)}){id, name, description, public, members{id, username, avatar}}}`)
            .then(response => {
                const result = response.data;

                this.$store.dispatch('setGroup', result.data.groups[0]);
            });
    },

    components: {
        'group-members': GroupMembers,
        'group-options': GroupOptions
    }
}
</script>
