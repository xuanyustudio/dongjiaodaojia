import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)
const store = new Vuex.Store({
    state: {
		phones:"",
		token:"",  
		openid:"",
		latAndLng:{
			lat:"",
			lng:"",
		},//经纬度
		currentSite:{  //技师首页获取的位置
			province:"" ,//省
			city:"",//市
			district:" ",//街道
			address:"",//详细地址
		}, //当前位置
		userInfo:"", //用户信息
		imgUrl:"http://nianerge.oss-cn-beijing.aliyuncs.com/nianerge/" // 阿里云图片地址路径
	},
    mutations: {
		huoquPhone(state,Phone) {
			// console.log(Phone);
			state.phones = Phone
			// console.log(Phone);
		},
		changeToken(state,Token){
			state.token = Token
		},
		changeOpenid(state,Openid){
			state.openid = Openid
		},
		// 更改技师详情地址
		changecurrentSite(state,res){
			let {province,city,district,address} = res
			state.currentSite.province = province
			state.currentSite.city = city
			state.currentSite.district = district
			state.currentSite.address = address
		},
		//更改技师位置经纬度
		changeLatAndLng(state,LatAndLng){
			console.log(LatAndLng)
			state.latAndLng.lat = LatAndLng.lat
			state.latAndLng.lng = LatAndLng.lng
			// state.currentSite.name = res.name
			// state.currentSite.address = res.address
		},
		changeUserInfo(state,userInfo){
			// console.log(userInfo)
			state.changeUserInfo = userInfo
		},
	},
    actions: {
		// 位置
		actionsChangecurrentSite(context,res){
			context.commit("changecurrentSite",res)
		},
		// 经纬度
		actionsChangeLatAndLng(context,res){
			context.commit("changeLatAndLng",res)
		}
	}
})
export default store