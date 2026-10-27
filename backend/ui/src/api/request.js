import axios from 'axios'
import { Message,MessageBox} from 'element-ui'
import store from '../store'

//请求地址
// axios.defaults.baseURL = 'http://smfwserver:7888/admin'
// axios.defaults.baseURL = 'http://175.178.72.131:3305/public/index.php/admin'
// axios.defaults.baseURL = 'http://127.0.0.3:3305/public/index.php/admin'
axios.defaults.baseURL = 'http://127.0.0.1:8088/public/index.php/admin'


//设置超时时间
axios.defaults.timeout = 5000
// post请求头
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded'
axios.defaults.withcredentials = true

//请求拦截(请求发出前处理请求)
axios.interceptors.request.use((config) => {
    const token = localStorage.getItem("adminToken")
    if(token){
        config.headers['Authorization'] =  token 
    }
    return config;
  }, (error) => {
    return Promise.reject(error);
  });

// 响应拦截器（处理响应数据）
axios.interceptors.response.use(
    (res) => {
        const ret = res.data
        
        if(ret.status !== 200 && !ret.key && !ret.code){
            if(ret.status == 101){
                MessageBox.confirm(ret.msg, '系统提示', {
                    confirmButtonText: '重新登录',
                    cancelButtonText: '取消',
                    type: 'warning'
                  }
                ).then(() => {
                    store.dispatch('logout').then(() => {
                        location.href = '/';
                    })
                })
            }else{
                Message({
                    showClose: true,
                    message: ret.msg || 'Server error',
                    type: 'error',
                    duration: 2000
                })
            }
            reject(ret.msg)
        }
        return res
    }, 
    (error) => {
        Message({
            showClose: true,
            message: error.message,
            type: 'error',
            duration: 2000
        })
        return Promise.reject(error);
    }
);

// 封装get方法
function get(url, data){
    return new Promise((resolve, reject) =>{
        axios.get(url, data).then(res =>{
            resolve(res.data);
        }).catch(err =>{
            reject(err.data);
        })
    });
}

// 封装post方法
function post(url, params){
    return new Promise((resolve, reject) =>{
        axios.post(url, params).then(res =>{
            resolve(res.data);
        }).catch(err =>{
            reject(err.data);
        })
    });
}

//对外接口
export function request({url,method, data}){
    if(method == 'get'){
        return get(url, data);
    }else if(method == 'post'){
        return post(url, data);
    }
}

export default request