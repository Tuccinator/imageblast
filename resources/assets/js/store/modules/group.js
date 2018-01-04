const group = {
    state: {
        id: null,
        name: '',
        nameValid: true,
        description: '',
        privacy: false,
        members: {},
        mainInviteCode: ''
    },
    mutations: {
        setGroupName: (state, name) => {
            state.name = name;

            if(name.length === 0) {
                state.nameValid = false;
            } else {
                state.nameValid = true;
            }
        },

        setGroupDescription: (state, description) => {
            state.description = description;
        },

        setGroupPrivacy: (state, privacy) => {
            state.privacy = privacy;
        },

        setGroupNameValid: (state, validity) => {
            state.nameValid = validity;
        },

        setGroupMembers: (state, members) => {
            state.members = members;
        },

        setGroupId: (state, id) => {
            state.id = id;
        },

        setGroupInviteCode: (state, code) => {
            state.mainInviteCode = code;
        }
    },

    actions: {
        setGroup: (context, group) => {
            context.commit('setGroupName', group.name);
            context.commit('setGroupDescription', group.description);
            context.commit('setGroupMembers', group.members);
            context.commit('setGroupId', group.id);
            context.commit('setGroupPrivacy', group['public']);
            context.commit('setGroupInviteCode', group.invite_code);
        }
    }
}

module.exports = group;
