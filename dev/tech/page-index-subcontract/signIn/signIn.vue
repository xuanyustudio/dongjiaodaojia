<template>
	<!-- 签到 -->
	<view class="signIn">
		<!-- 当前位置 -->
		<view class="currentlocation">
			<view class="currentlocation-bg">
				<image :src="imgUrl+'location1.png'" mode=""></image>
			</view>
			<view class="currentlocation-top">
				<view class="img">
					<image :src="imgUrl+'location2.png'" mode=""></image>
				</view>
				<view class="currentlocation-top-text">
					当前位置
				</view>
			</view>


			<view class="currentlocation-center">
				<text>{{currentSite.city}}</text>
			</view>
			<view class="currentlocation-bottom">
				<text>{{currentSite.district}}{{currentSite.address}}</text>
			</view>



			<u-line />
			<!-- 点击签到 -->
			<view class="currentlocation-clicksignin">
				<view class="currentlocation-clicksignin-left">
					<view @click="chooseLocation">手动设置</view>
					<u-icon name="arrow-right" color="#46D17E" size="16"></u-icon>
				</view>
				<view class="currentlocation-clicksignin-right" @tap="clickSignin">
					签到
				</view>
			</view>

		</view>
		<!-- 地图 -->
		<view class="map-loctionMap">
			<view class="locationmap-top">
				<view class="locationmap-top-left">
					服务区域(半径{{radius}}km)
				</view>
				<view class="locationmap-top-counter">
					<view class="cut" @click="cut">
						<image :src="imgUrl+'add.png'" mode=""></image>
					</view>
					<view class="num">
						{{radius}}
					</view>
					<view class="add" @click="add">
						<image :src="imgUrl+'cut.png'" mode=""></image>
					</view>
				</view>
			</view>
			<u-line />
			<view class="locationmap-map">
				<!-- #ifdef H5 -->
				<view id="sign-area-map" class="h5-map"></view>
				<!-- #endif -->
				<!-- #ifndef H5 -->
				<map :circles="circles" :longitude="latAndLng.lng" :scale="scale" :latitude="latAndLng.lat"
					:markers="covers" />
				<!-- #endif -->
			</view>

		</view>
		<!-- #ifdef H5 -->
		<amapchoose :show="showMap" :initLocation="initLocation" keyMap="c7438bdd2886509994ae0c832c4172bb"
			AMapKeyWeb="f30314d5255acac205b476382a598faf" @placeChoose="onPlace" @changeShow="showMap = false">
		</amapchoose>
		<!-- #endif -->

		<!-- 签到成功遮蔽层 -->
		<!-- <view class="shield" v-if="signIn" @click="clickSignin">
			<view class="img">
				<image :src="imgUrl+'qdcg.png'" mode=""></image>
			</view>

		</view> -->
	</view>
</template>

<script>
	import {
		signIn,
		getMyIndex
	} from "./api.js"
	// #ifdef H5
	import amapchoose from "../../components/amap-choose/amap-choose.vue"
	// #endif
	export default {
		components: {
			// #ifdef H5
			amapchoose
			// #endif
		},
		onReady() {
			this.pageReady = true
			this.drawAreaMap()
		},
		onShow() {
			this.images()
		},
		computed: {
			currentSite() {
				return this.$store.state.currentSite
			},
			latAndLng() {
				const saved = uni.getStorageSync('syncAddr') || {}
				const storePos = this.$store.state.latAndLng || {}
				return {
					lat: storePos.lat || saved.lat || '',
					lng: storePos.lng || saved.lng || ''
				}
			},
			initLocation() {
				if (!this.latAndLng.lng || !this.latAndLng.lat) return ''
				return this.latAndLng.lng + ',' + this.latAndLng.lat
			}
		},
		data() {
			return {
				imgUrl: this.$store.state.imgUrl,
				showMap: false,
				pageReady: false,
				areaMap: null,
				areaCircle: null,
				signIn: false,
				radius: 1,
				latitude: 26.22,
				longitude: 111.63,
				scale: 13,
				circles: [{
					latitude: 26.22,
					longitude: 111.63,
					radius: 1000,
					strokeWidth: 2,
					color: "#FFB83C",
					fillColor: "#ffaa003c",
				}],
				covers: [{
					latitude: 26.22,
					longitude: 111.63,
					iconPath: this.$store.state.imgUrl + "dt.png",
					callout: {
						content: "",
						bgColor: "white",
						display: "ALWAYS",
						padding: "12rpx"
					},
					width: "40rpx",
					height: "40rpx"
				}]

			}
		},
		methods: {
			images() {
				let token = uni.getStorageSync("token")
				getMyIndex(token).then(res => {
					if (res.code == 1 && res.data && res.data.info) {
						const info = res.data.info
						this.radius = Number(info.radius) || 1
						this.circles[0].radius = 1000 * this.radius
						if (info.lat && info.lng) {
							this.applySite({
								lat: info.lat,
								lng: info.lng,
								province: info.province,
								city: info.city,
								district: info.district,
								address: info.address
							})
						}
					}
				}).catch(() => {}).finally(() => {
					this.drawAreaMap()
				})
			},
			chooseLocation() {
				// #ifdef H5
				this.showMap = true
				return
				// #endif
				this.handleLocation()
			},
			onPlace(e) {
				this.showMap = false
				const parsed = this.splitAddress(e.address || '')
				const text = value => Array.isArray(value) ? value.filter(Boolean).join('') : (value == null ? '' : String(value).trim())
				const province = text(e.province) || parsed.province
				const city = text(e.city) || parsed.city || province
				let district = text(e.district)
				if (!district || (parsed.district && district !== parsed.district && district.indexOf(parsed.district) >= 0)) {
					district = parsed.district || district
				}
				this.applySite({
					lat: String(e.latitude || '').replace(/\s/g, ''),
					lng: String(e.longitude || '').replace(/\s/g, ''),
					province,
					city,
					district,
					address: parsed.detail || e.address || ''
				})
			},
			splitAddress(text) {
				const site = { province: '', city: '', district: '', detail: text || '' }
				if (!text) return site
				const reg = /^(.*?(省|自治区|特别行政区))?(.*?(市|自治州|地区|盟))?(.*?(区|县|旗))?(.*)$/
				const m = String(text).match(reg)
				if (!m) return site
				site.province = m[1] || ''
				site.city = m[3] || site.province
				site.district = m[5] || ''
				site.detail = (m[7] || '').replace(/^-/, '')
				return site
			},
			applySite(site) {
				const latAndLng = { lat: site.lat || '', lng: site.lng || '' }
				const currentSite = {
					province: site.province || '',
					city: site.city || '',
					district: site.district || '',
					address: site.address || ''
				}
				this.$store.dispatch("actionsChangeLatAndLng", latAndLng)
				this.$store.dispatch("actionsChangecurrentSite", currentSite)
				uni.setStorageSync('syncAddr', {
					lat: latAndLng.lat,
					lng: latAndLng.lng,
					province: currentSite.province,
					city: currentSite.city,
					district: currentSite.district,
					address: currentSite.address
				})
				if (latAndLng.lat && latAndLng.lng) {
					this.circles[0].latitude = Number(latAndLng.lat)
					this.circles[0].longitude = Number(latAndLng.lng)
					this.covers[0].latitude = Number(latAndLng.lat)
					this.covers[0].longitude = Number(latAndLng.lng)
				}
				this.drawAreaMap()
			},
			ensureAmap() {
				if (typeof window !== 'undefined' && window.AMap) return Promise.resolve()
				if (this._amapReady) return this._amapReady
				this._amapReady = new Promise(resolve => {
					const old = document.getElementById('mapTest')
					if (old) {
						old.addEventListener('load', () => resolve())
						if (window.AMap) resolve()
						return
					}
					const script = document.createElement('script')
					script.id = 'mapTest'
					script.src = 'https://webapi.amap.com/maps?v=2.0&key=c7438bdd2886509994ae0c832c4172bb'
					script.onload = () => resolve()
					document.body.appendChild(script)
				})
				return this._amapReady
			},
			drawAreaMap() {
				// #ifndef H5
				return
				// #endif
				if (!this.pageReady) return
				this.ensureAmap().then(() => {
					this.$nextTick(() => {
						const el = document.getElementById('sign-area-map')
						if (!el || !window.AMap) return
						const lng = Number(this.latAndLng.lng) || 121.473701
						const lat = Number(this.latAndLng.lat) || 31.230416
						if (!this.areaMap) {
							this.areaMap = new window.AMap.Map(el, { zoom: 13, center: [lng, lat] })
						}
						if (this.areaCircle) this.areaMap.remove(this.areaCircle)
						this.areaCircle = new window.AMap.Circle({
							center: [lng, lat],
							radius: (Number(this.radius) || 1) * 1000,
							strokeColor: '#FFB83C',
							strokeWeight: 2,
							fillColor: '#ffaa00',
							fillOpacity: 0.24
						})
						this.areaMap.add(this.areaCircle)
						this.areaMap.setFitView([this.areaCircle])
					})
				})
			},
			//手动设置地址
			handleLocation() {
				uni.chooseLocation({
					success: (res) => {


						let latAndLng = {}
						latAndLng.lat = res.latitude
						latAndLng.lng = res.longitude
						// let currentSite = {}
						var regex = /^(北京市|天津市|重庆市|上海市|香港特别行政区|澳门特别行政区)/;
						var REGION_PROVINCE = [];
						var currentSite = {
							province: null,
							city: null,
							district: null,
							address: null
						};

						if (!regex.exec(res.address)) {
							regex = /^(.*?(省|自治区))(.*?)$/;

							REGION_PROVINCE = regex.exec(res.address);

							currentSite.province = REGION_PROVINCE[1];
							currentSite = this.regexAddressBean(REGION_PROVINCE[3], currentSite, res);
						} else {
							REGION_PROVINCE = regex.exec(res.address);
							currentSite.province = REGION_PROVINCE[1];
							currentSite = this.regexAddressBean(res.address, currentSite, res);
						}
						console.log(currentSite, "1：currentSite")

						this.$store.dispatch("actionsChangeLatAndLng", latAndLng);
						console.log('选择数据', currentSite)
						this.$store.dispatch("actionsChangecurrentSite", currentSite)

						uni.setStorageSync('syncAddr', {
							lat: latAndLng.lat,
							lng: latAndLng.lng,
							province:currentSite.province,
							city:currentSite.city,
							district:currentSite.district,
							address:currentSite.address
						})
					}
				});
			},
			regexAddressBean(address, addressBean, res) {
				var regex = /^(.*?[市州]|.*?地区|.*?特别行政区)(.*?[市区县])(.*?)$/g;
				var addxress = regex.exec(address);
				addressBean.city = addxress[1];
				addressBean.district = addxress[2];
				addressBean.address = addxress[3] + "(" + res.name + ")";
				return addressBean;
				console.log(addressBean, "5：打印的5")
			},
			clickSignin() {
				const site = this.$store.state.currentSite || {}
				const pos = this.latAndLng
				if (!pos.lat || !pos.lng) {
					uni.showToast({ icon: 'none', title: '请先设置位置' })
					return
				}
				signIn({
					province: site.province,
					city: site.city,
					district: site.district,
					address: site.address,
					lat: pos.lat,
					lng: pos.lng,
					radius: this.radius
				}).then(res => {
					uni.showToast({ icon: 'success', title: res.msg || '签到成功' })
					setTimeout(() => {
						uni.navigateBack()
					}, 800)
				})
			},
			// 区域减
			cut() {
				if (this.radius <= 1) return false
				this.radius--
				this.circles[0].radius = 1000 * this.radius
				this.drawAreaMap()
			},
			add() {
				this.radius++
				this.circles[0].radius = 1000 * this.radius
				this.drawAreaMap()
			},

		}
	}
</script>

<style lang="less">
	.signIn {
		width: 100%;
		height: 100%;

		.currentlocation {
			position: relative;
			background: #fff;
			width: 686rpx;
			height: 308rpx;
			border-radius: 16rpx;
			margin: 0 auto;
			padding-left: 24rpx;

			.currentlocation-bg {
				position: absolute;
				right: 0;
				top: 0;
				width: 172rpx;
				height: 172rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}

			.currentlocation-top {
				margin-top: 24rpx;
				display: flex;
				align-items: center;
				height: 88rpx;
				font-size: 32rpx;
				font-weight: bold;
				color: #262626;


				.img {
					width: 44rpx;
					height: 44rpx;
					margin-right: 8rpx;

					image {
						width: 100%;
						height: 100%;
					}
				}
			}

			.currentlocation-center {
				font-size: 28rpx;
				font-weight: 500;
				color: #262626;
			}

			.currentlocation-bottom {
				margin-top: 8rpx;
				font-size: 24rpx;
				font-weight: 400;
				color: #666666;
				margin-bottom: 20rpx
			}

			.currentlocation-clicksignin {
				height: 112rpx;
				width: 100%;
				display: flex;
				align-items: center;
				justify-content: space-between;

				.currentlocation-clicksignin-left {
					height: 100%;
					display: flex;
					align-items: center;
					font-size: 32rpx;
					font-weight: 500;
					color: #0ea07f;

					view {
						margin-top: -3rpx;
						margin-right: 10rpx;
					}
				}

				.currentlocation-clicksignin-right {
					width: 160rpx;
					height: 64rpx;
					background: #0ea07f;
					border-radius: 8rpx;
					margin-right: 24rpx;
					text-align: center;
					line-height: 64rpx;
					font-size: 32rpx;
					font-weight: 500;
					color: #FFFFFF;

				}
			}
		}


		.map-loctionMap {
			margin: 0 auto;
			width: 686rpx;
			height: 792rpx;
			margin-top: 26rpx;
			background: #fff;


			.locationmap-top {
				box-sizing: border-box;
				display: flex;
				align-items: center;
				justify-content: space-between;
				width: 100%;
				height: 96rpx;
				padding: 0 24rpx;
				font-size: 32rpx;
				font-weight: bold;
				color: #262626;

				.locationmap-top-counter {
					box-sizing: border-box;
					width: 176rpx;
					height: 56rpx;
					background: #F7F8F9;
					border-radius: 28rpx;
					display: flex;
					align-items: center;
					justify-content: space-between;
					padding: 0 20rpx;

					.add {
						width: 28rpx;
						height: 28rpx;
						margin-top: -8rpx;

						image {
							width: 100%;
							height: 100%;
						}
					}

					.cut {
						width: 28rpx;
						height: 28rpx;
						margin-top: -30rpx;

						image {
							width: 100%;
							height: 21.4%;
						}
					}

					.num {
						font-size: 28rpx;
						font-weight: 500;
						color: #000000;
					}
				}


			}

			.locationmap-map {
				box-sizing: border-box;
				padding: 24rpx 24rpx 32rpx 24rpx;
				width: 100%;
				height: 695rpx;

				map,
				.h5-map {
					width: 100%;
					height: 100%;
				}
			}
		}

		.shield {
			position: fixed;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			background: rgba(0, 0, 0, .6);
			display: flex;
			align-items: center;
			justify-content: center;

			.img {
				width: 379rpx;
				height: 390rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}
		}
	}
</style>
