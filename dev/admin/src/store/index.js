import { createStore } from 'vuex'
import getters from './getters'
import app from './modules/app'
import setting from './modules/setting'
import tags from './modules/tags'
import user from './modules/user'

export default createStore({
  modules: { app, setting, tags, user },
  getters
})
