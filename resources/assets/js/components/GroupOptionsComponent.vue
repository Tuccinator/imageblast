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
        <div class="feed-header">
            <span>Members</span>
        </div>
        <div class="feed-content">
            <div class="field">
                <div class="control">
                    <input type="text" class="input" :value="memberName" @input="updateMemberName" placeholder="Search for member..." />
                </div>
            </div>
            <div class="members">
                <div class="member-row" v-for="(member, index) in members" :key="member.id">
                    <div class="member-row-avatar">
                        <img :src="'/' + member.avatar" />
                    </div>
                    <div class="member-row-username">
                        <a href="#" @click="openUserModal(index)">{{ member.username }}</a>
                    </div>
                </div>
            </div>
            <div class="modal" :class="{ 'is-active': this.modalOpen }">
                <div class="modal-background" @click="closeUserModal()"></div>
                <div class="modal-content">
                    <h3 class="title is-5">{{ modal.username }}</h3>
                </div>
                <button class="modal-close is-large" aria-label="close" @click="closeUserModal()"></button>
            </div>
        </div>
    </div>
</template>

<script>
const axios = require('axios');

import { mapState } from 'vuex';

export default {
    props: ['groupId'],

    data: function() {
        return {
            modalOpen: false,
            modal: {}
        };
    },

    computed: {
        ...mapState({
            id: state => state.group.id,
            name: state => state.group.name,
            description: state => state.group.description,
            privacy: state => state.group.privacy,
            inviteCode: state => state.group.mainInviteCode,
            memberName: state => state.search.query,
            members: state => {
                if(state.search.query.length > 0) {
                    return state.group.members.filter(member => {
                        return member.username.includes(state.search.query);
                    });
                }

                return state.group.members;
            },
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
        },

        updateMemberName: function(e) {
            this.$store.commit('setSearchQuery', e.target.value);
        },

        openUserModal: function(index) {
            this.modal = this.members[index];
            this.modalOpen = true;
        },

        closeUserModal: function() {
            this.modalOpen = false;
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
