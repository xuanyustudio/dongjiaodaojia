import request from '@/api/request'


//基本配置
export function index(params) {
	return request({ url: '/Baseconfig/index',method: 'post',data: params})
}
export function getInfo(params) {
	return request({ url: '/Baseconfig/getInfo',method: 'post',data: params})
}
