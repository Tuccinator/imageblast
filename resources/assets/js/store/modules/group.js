const group = {
    state: {
        name: '',
        nameValid: true,
        description: '',
        privacy: false
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
        }
    }
}

module.exports = group;
