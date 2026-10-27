import store from "../store/index.js"; //vuex  
// let server_url='http://massage.weixiaoba.vip/technician';//请求地址
// let server_url='http://moushilang.hdhzyl.cn/technician';//请求地址
// let server_url='https://www.baidu.shop/technician';//请求地址
// 本地联调新 Go 后端。改回线上时把 http 改回 https://，server_url 改回线上域名。
let http = 'http://';
let server_url = '127.0.0.1:8088/public/index.php/technician'; //请求地址
// let http = 'https://';
// let server_url = 'xxdj.tushengwenlv.com/public/index.php/technician';
// http://admin.gegedaojia.com/#/admin/Home/index.html
let token = '';
process.env.NODE_ENV === 'development' ? '192.168.0.1' : '';

function service(options = {}) {
	store.state.token && (token = store.state.token); //从vuex中获取登录凭证
	// let token ;
	// if(store.state.token ) {
	//  token = store.state.token 
	// }else{
	//  uni.getStorageSync({
	//  	    key: 'token',
	//  	    success: function (res) {
	//  			console.log(res)
	//  	        token = res.data
	//  	    }
	//  })
	// }
	let new_url = server_url;
	while (options.url.indexOf('../') > -1) {
		new_url='';
		options.url = options.url.replace('../', '');
		var items = server_url.split('/');
		items.forEach(function(item, index) {
			if (index < items.length-1) {
				new_url += item+'/'
			}
		})
	}

	options.url = `${http}${new_url}${options.url}`;
	console.log('token值',token) 
	console.log('请求地址', options.url)
	//配置请求头
	options.header = {
		'content-type': 'application/x-www-form-urlencoded',
		'token': `${token}` //Bearer 
	};

	return new Promise((resolved, rejected) => {
		//成功
		options.success = (res) => {

			if (Number(res.data.code) == 1) { //请求成功
				resolved(res.data, res.code, res.msg);
			} else {
				uni.showToast({
					icon: 'none',
					duration: 3000,
					title: `${res.data.msg}`
				});
				rejected(res.data); //错误
			}

		}
		//错误
		options.fail = (err) => {
			rejected(err); //错误
		}
		uni.request(options);

	});
}
export default service;
