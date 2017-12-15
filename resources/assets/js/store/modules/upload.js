const upload = {
    state: {
        fileName: 'Upload an image',
        fileSource: null,
        fullFile: {},
        selectedType: '0',
    },
    mutations: {
        setSelectedType: function(state, type) {
            state.selectedType = type;
        },

        setFileName: function(state, name) {
            state.fileName = name;
        },

        setFileSource: function(state, source) {
            state.fileSource = source;
        },

        setFullFile: function(state, file) {
            state.fullFile = file;
        }
    }
}

module.exports = upload;
