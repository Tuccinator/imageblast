<template>
    <div class="group-options-all feed-section">
        <div class="feed-header">
            <span>Name</span>
        </div>
        <div class="feed-content">
            <p>{{ name }}</p>
        </div>
        <div class="feed-header">
            <span>Description</span>
        </div>
        <div class="feed-content">
            <p>{{ description }}</p>
        </div>
        <div class="feed-header">
            <span>Privacy</span>
        </div>
        <div class="feed-content">
            <div class="field is-grouped">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="privacy" :checked="privacy === 0" @change="updatePrivacy" />
                        Invite-only
                    </label>
                </div>
                <div class="control is-expanded" v-if="privacy === 0">
                    <input type="text" class="input" :value="inviteCode" @input="updateInviteCode" @keyup.enter="changeInviteCode" placeholder="Enter invite code..."/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const axios = require('axios');

import { mapState } from 'vuex';

export default {
    props: ['groupId'],

    computed: {
        ...mapState({
            id: state => state.group.id,
            name: state => state.group.name,
            description: state => state.group.description,
            privacy: state => state.group.privacy,
            inviteCode: state => state.group.mainInviteCode
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

                    this.$store.commit('setGroupPrivacy', result.data.groupPrivacy.public);
                })
        },

        updateInviteCode: function(e) {
            this.$store.commit('setGroupInviteCode', e.target.value);
        },

        changeInviteCode: function() {
            axios.post(`/graphql?query=mutation+groups{ changeGroupCode(id: ${this.id}, code: "${this.inviteCode}"){id, invite_code}}`)
                .then(response => {
                    const result = response.data;

                    if(result.errors) {
                        //display toast
                        return;
                    }

                    // display successful toast
                })
        }
    },

    mounted: function() {
        const id = this.groupId;

        axios.get(`/graphql?query=query+groupAndMembers{ groups(id: ${parseInt(id)}){id, name, description, public, invite_code, members{id, username, avatar}}}`)
            .then(response => {
                const result = response.data;

                this.$store.dispatch('setGroup', result.data.groups[0]);
            });
    }
};
</script>
