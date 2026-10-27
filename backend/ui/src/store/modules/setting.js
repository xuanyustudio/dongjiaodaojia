import defaultSettings from '@/settings'

const {  tagsView } = defaultSettings

const storageSetting = JSON.parse(localStorage.getItem('layout-setting')) || ''

export default {

    state :{
        tagsView: storageSetting.tagsView === undefined ? tagsView : storageSetting.tagsView,
    },
    mutations :{
        CHANGE_SETTING: (state, { key, value }) => {
            if (state.hasOwnProperty(key)) {
              state[key] = value
            }
        }
    },
    actions :{
        changeSetting({ commit }, data) {
            commit('CHANGE_SETTING', data)
        }
    }
}