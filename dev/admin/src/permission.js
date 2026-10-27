import router from './router'
import store from './store'
import { homePath } from '@/utils/home'
import NProgress from 'nprogress' // 进度条
import 'nprogress/nprogress.css' // 进度条样式

NProgress.configure({ showSpinner: false }) // 进度条配置

const whiteList = ['/admin/login'] // 免登录白名单

router.beforeEach((to, from, next) => {
    NProgress.start()
    if (to.meta.title) {
        if(store.getters.site_title){
            document.title = to.meta.title +'-'+ store.getters.site_title
        }else{
            document.title = to.meta.title
        }
    }

    const adminToken = localStorage.getItem("adminToken")
    const isEntry = to.path === '/admin/login' || to.path === '/' || to.path === '/404'
    const goHome = () => {
        const home = homePath()
        if (!home || to.path === home) next()
        else next({ path: home, replace: true })
    }
    if(adminToken){
        if (store.getters.addRoutes.length > 0) {
            if (isEntry) goHome()
            else next()
            return
        }
        store.dispatch('getUserInfo').then(() => {
            if (isEntry) goHome()
            else next({ ...to, replace: true })
        }).catch(() => {
            store.dispatch('logout').finally(() => {
                next('/admin/login')
            })
        })
    }else if(whiteList.indexOf(to.path) !== -1) {
        next()
    } else {
        next('/admin/login')
        NProgress.done()
    }
})

router.afterEach(() => {
    NProgress.done()
})