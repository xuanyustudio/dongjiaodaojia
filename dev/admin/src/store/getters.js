const getters = {
    site_title: state => state.user.site_title,
    username: state => state.user.username,
    headimg: state => state.user.headimg,
    show_notice: state => state.user.show_notice,
    role_id: state => state.user.role_id,
    addRoutes: state => state.user.addRoutes,
    menu: state => state.user.menu,
    actions: state => state.user.actions,
    aside_state: state => state.app.aside_state,
    device: state => state.app.device,
    sidebar: state => state.app.sidebar,
    tags: state => state.tags.tags,
    tagsView: state => state.setting.tagsView,
  }
  export default getters
  