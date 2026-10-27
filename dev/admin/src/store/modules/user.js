import { login,getUserInfo,logout} from '@/api/admin/base'
import router from '@/router'
import Layout from '@/layout/admin/default/Main.vue'

const viewModules = import.meta.glob('../../views/**/*.vue')

function loadView(componentPath) {
    const normalized = String(componentPath || '').replace(/\\/g, '/').replace(/^\//, '')
    const withExt = normalized.endsWith('.vue') ? normalized : `${normalized}.vue`
    const key = `../../views/${withExt}`
    if (viewModules[key]) return viewModules[key]
    const found = Object.keys(viewModules).find(item => item.toLowerCase() === key.toLowerCase())
    return found ? viewModules[found] : undefined
}


export default {
    state :{
        addRoutes:[],
        menu:[],
        actions:[],
        username:'',
        headimg:'',
        site_title:'',
        show_notice:true,
    },
    mutations :{
        setRoute(state,route){
            state.addRoutes = route
        },
        setMenu(state,menu){
            state.menu = menu
        },
        setActions(state,actions){
            state.actions = actions
        },
        setUserInfo(state,val){
            state.username = val.user
            state.headimg = val.headimg
            state.role_id = val.role_id
            state.site_title = val.site_title
            state.show_notice = val.show_notice
        }
    },
    actions :{
        login({commit},userInfo) {
            return new Promise((resolve, reject) => {
                login(userInfo).then(res => {
                    localStorage.setItem("adminToken", res.token)
                    resolve()
                }).catch(error => {
                    reject(error)
                })
            })
        },
        getUserInfo({commit}){
            return new Promise((resolve, reject) => {
                getUserInfo().then(res => {
                    if(res.status == 200){
                        const children = []
                        ;(res.components || []).forEach(item => {
                            if (!item.component_path) return
                            const component = loadView(item.component_path)
                            if (!component) return
                            children.push({ ...item, component })
                        })
                        const layoutRoute = {
                            name: 'layout-root',
                            path: '/',
                            component: Layout,
                            children
                        }
                        if (router.hasRoute('layout-root')) router.removeRoute('layout-root')
                        if (router.hasRoute('not-found')) router.removeRoute('not-found')
                        router.addRoute(layoutRoute)
                        router.addRoute({ name: 'not-found', path: '/:pathMatch(.*)*', redirect: '/404' })
                        commit('setRoute', [layoutRoute])
                        commit('setMenu',res.menu)
                        commit('setActions',res.actions)
                        commit('setUserInfo',res.data)
                    }
                    resolve()
                }).catch(error => {
                    reject(error)
                })
            })
        },
        logout({commit}){
            return new Promise((resolve, reject) => {
                const adminToken = localStorage.getItem("adminToken")
                logout({adminToken:adminToken}).then(res => {
                    if(res.status == 200){
                        localStorage.setItem("adminToken", "")
                        commit('setRoute',[])
                        commit('setMenu',[])
                        commit('setActions',[])
                        commit('setUserInfo',{})
                    }
                    resolve()
                }).catch(error => {
                    reject(error)
                })
            })
        }
        
    }
}