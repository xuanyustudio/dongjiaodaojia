export default {
    state :{
        sidebar: {
            opened: true,
            withoutAnimation: false
        },
        device: 'desktop'
    },
    mutations :{
        toggle_device: (state, device) => {
            state.device = device
        },
        toggle_sidebar: state => {
            state.sidebar.opened = !state.sidebar.opened
            state.sidebar.withoutAnimation = false
        },
        close_sidebar: (state, withoutAnimation) => {
            state.sidebar.opened = false
            state.sidebar.withoutAnimation = withoutAnimation
        },
    },
    actions :{
        toggleDevice({ commit }, device) {
            commit('toggle_device', device)
        },
        toggleSideBar({ commit }) {
            commit('toggle_sidebar')
        },
        closeSideBar({ commit }, { withoutAnimation }) {
            commit('close_sidebar', withoutAnimation)
        },
    }
}