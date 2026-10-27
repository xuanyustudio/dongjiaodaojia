<template>
	<view>

	</view>
</template>
<script>
	import './geolocation.min.js'
	export default {
		data() {
			return {
				myMap: {}
			}
		},
		mounted() {
			//this.__init()
		},
		methods: {
			__init() {
				// if (origin.indexOf('https') === -1) throw '请在 https 环境中使用本插件。'
				if (!navigator || !navigator.geolocation) throw '地理位置服务不可用'

				const options = {
					enableHighAccuracy: true,
					timeout: 5000,
					maximumAge: 0
				};

				/** err 说明
							code 1  表示用户拒绝授权
							code 3  未获取到地址信息，可能是设备没有开启定位服务或者系统没有给浏览器定位权限
				 * */
				return new Promise((resolve, rejace) => {
					navigator.geolocation.getCurrentPosition((res) => {
						this.myMap = new qq.maps.Geolocation("QAWBZ-S2MWR-U5OWI-WZBUC-UXDSZ-AAFRJ",
							"地图标点");
						resolve(this)
					}, rejace, options)
				})
			},
			getLocation() {
				return new Promise((resolve, reject) => {
					this.myMap.getLocation(res => {
						resolve(res)
					}, err => {
						reject(err)
					})
				})
			},
			getIpLocation() {
				return new Promise((resolve, reject) => {
					this.myMap.getIpLocation(res => {
						resolve(res)
					}, err => {
						reject(err)
					})
				})

			},
			watchPosition(callBack) {
				this.myMap.watchPosition(res => {
					callBack(res)
				})
			},
			clearWatch() {
				this.myMap.clearWatch()
			}
		}
	}
</script>


<style scoped>

</style>
