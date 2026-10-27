<template>
	<view class="flex-col page">

		<view class="flex-col group">
			<view class="flex-col">
				<u-form>
					<u-form-item label="姓名" prop="realname" borderBottom ref="item1" labelWidth='80'>
						<u--input v-model="realname" border="none" placeholder="请输入姓名"> </u--input>
					</u-form-item>
					<u-form-item label="手机号" prop="mobile" borderBottom ref="item1" labelWidth='80'>
						<u--input v-model="mobile" border="none" type="number" placeholder="请输入手机号"> </u--input>
					</u-form-item>


					<u-form-item @click="showCity" label="服务地址" prop="district" borderBottom ref="item1"
						labelWidth='80'>
						<u--input v-model="districts" border="none" placeholder="请选择您的地址" disabled
							disabledColor="#FFFFFF"> </u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					<!-- 被修改的地方 -->
					<!-- 	<u-form-item  label="服务地址" prop="district" borderBottom ref="item1" labelWidth='80'>
						<u--input v-model="districts" border="none" placeholder="请选择您的地址" 
							disabledColor="#FFFFFF"> </u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item> -->


					<u-form-item label="门牌号" prop="address" borderBottom ref="item1" labelWidth='80'>
						<u--input v-model="address" border="none" placeholder="某酒店/XX房间(小区XX号)" disabledColor="#FFFFFF">
						</u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
				</u-form>
				<view class="justify-between group_5">
					<text class="text_11">设为默认地址</text>
					<u-switch v-model="is_default" activeColor="#5ac725"></u-switch>
				</view>
			</view>
			<view class="flex-col group_6">
				<view class="flex-col items-center button" @click="save()">
					<text>保存</text>
				</view>
				<view class="flex-col items-center button_1" @click="cancel()" v-if="addressId">
					<text v-if="addressId">删除</text>
				</view>
			</view>

			<!-- <Flame-chooseLocation v-model="show" mapkey="6OBBZ-X5AWW-ZYYRY-RRJ77-F7425-2LBF4" @confirm="confirm">
			</Flame-chooseLocation> -->

		</view>

		<amapchoose :show="show" :initLocation="lng+','+lat" keyMap="c7438bdd2886509994ae0c832c4172bb"
			AMapKeyWeb="f30314d5255acac205b476382a598faf" @placeChoose="choose"></amapchoose>
	</view>
</template>


<!--#ifdef H5-->
<!-- <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://map.qq.com/api/gljs?v=1.exp&key=6OBBZ-X5AWW-ZYYRY-RRJ77-F7425-2LBF4"></script> -->
<!--#endif-->

<script>
	import amapchoose from '@/components/amap-choose/amap-choose.vue'
	import {
		setLatLng
	} from '@/config/utils'
	export default {
		components: {
			amapchoose
		},
		data() {
			return {
				show: false,
				realname: '',
				mobile: '',
				district: '',
				address: '',
				is_default: false,
				location: '',
				districts: '',
				addressId: '',
				lat: '',
				lng: '',
				id: ""
			};
		},
		onLoad: function(options) {
			console.log(options)
			this.addressId = options.id ? Number(options.id) : '';
			if (options.id) {
				uni.setNavigationBarTitle({
					title: '编辑地址'
				});

				this.init();

			} else {
				uni.setNavigationBarTitle({
					title: '添加地址'
				});

			}
			
			var that = this;
			uni.getLocation({
				altitude: false,
				geocode: true,
				// type: 'gcj02',
				success: (res) => {
					that.lat = res.latitude;
					that.lng = res.longitude;
				}
			})

		},
		methods: {
			choose(e){
				console.log('选择地址',e);
				this.districts = e.address;
				this.lat = e.latitude.replace(' ','');
				this.lng = e.longitude.replace(' ','');
				
				let reg="(?<province>[^省]+自治区|.*?省|.*?行政区|.*?市)?(?<city>[^市]+自治州|.*?地区|.*?行政单位|.+盟|市辖区|.*?市|.*?县)?(?<county>[^(区|市|县|旗|海域|岛)]+区|.*?市|.*?县|.*?旗|.*?海域|.*?岛)?(?<village>.*)";
				
				var dt = e.address.match(reg);
				console.log('dt',dt);
				
				
				this.province = dt[1];
				this.city = dt[2];
				this.district = dt[4]; //dt[3]+dt[5].replace('-','');
				this.show = false;
			},
			confirm(val) {
				console.log('选择结果', val)
			},
			init() {
				this.$http.post('api/address/details', {
					id: this.addressId
				}).then(res => {
					console.log(res)
					for (let i in res) {
						this[i] = res[i];
					}
					this.is_default = Boolean(this.is_default);
					this.districts = this.province + this.city + this.district;
				})
			},
			showCity() {
				// #ifndef H5
				uni.chooseLocation({
					success: (res) => {
						console.log(res)
						this.districts = res.address;
						// let reg = /.+?(省|市|自治区|自治州|镇|县|区)/g;
						let reg="(?<province>[^省]+自治区|.*?省|.*?行政区|.*?市)?(?<city>[^市]+自治州|.*?地区|.*?行政单位|.+盟|市辖区|.*?市|.*?县)?(?<county>[^(区|市|县|旗|海域|岛)]+区|.*?市|.*?县|.*?旗|.*?海域|.*?岛)?(?<village>.*)";
						let arr = this.districts.match(reg);
						console.log(arr)
						this.province = arr[1];
						this.city = arr[2];
						if (arr[2]) {
							//this.district = arr[2]+(arr[4]?arr[4].replace('-',''):'');
							this.district = arr[4];
						} else {
							this.district = arr[1];
						}

						this.lat = res.latitude;
						this.lng = res.longitude;
					}
				})
				// #endif
				// #ifdef H5

				this.show = true;

				// #endif
				// this.$refs.mpvueCityPicker.show();
			},
			onChange() {},
			onCancel() {},
			onConfirm(e) {
				let arr = e.label.split('-');
				let province = arr[0];
				let city = arr[1];
				let district = arr[2];
				this.province = province;
				this.city = city;
				this.district = district;
				this.districts = `${province}${city}${district}`;
				setLatLng(`${province}${city}${district}`).then(res => {
					console.log(res)
				})
			},
			save() {
				if (!this.realname) {
					uni.showToast({
						icon: 'none',
						title: '请输入姓名'
					})
					return;
				}
				if (!this.mobile) {
					uni.showToast({
						icon: 'none',
						title: '请输入手机号'
					})
					return;
				}
				if (!this.district) {
					// uni.showToast({
					// 	icon:'none',
					// 	title:'请输入服务地址'
					// })
					// return;
				}
				if (!this.address) {
					uni.showToast({
						icon: 'none',
						title: '请输入门牌号'
					})
					return;
				}
				let obj = {
					id: 0,
					realname: this.realname,
					mobile: this.mobile,
					province: this.province,
					city: this.city,
					district: this.district,
					address: this.address,
					is_default: Number(this.is_default),
					lat: this.lat,
					lng: this.lng,
				}
				console.log(obj)
				if (this.addressId) {
					obj.id = this.addressId;
				};
				console.log(obj.id)
				let address = this.$http.post('api/address/editAddress', obj);
				let title = this.addressId ? '编辑成功' : '新增成功';
				uni.showToast({
					icon: 'success',
					title
				});
				uni.$emit('change', {
					isUpData: true,
				})
				setTimeout(() => {
					uni.navigateBack({
						delta: 1
					})
				}, 1000)
			},
			cancel() {
				uni.showModal({
					title: "",
					content: '是否确认删除此地址？',
					success: (res) => {
						if (res.confirm) {
							this.$http.post('api/address/del', {
								id: this.addressId
							}).then(res => {
								console.log(res)
								uni.showToast({
									icon: 'success',
									title: '删除成功',
									success: (res) => {
										uni.$emit('change', {
											isUpData: true,
										})
										setTimeout(() => {
											uni.navigateBack({
												delta: 1
											})
										}, 1000)
									}
								})
							})
						}
					}
				})
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		background-color: #ffffff;
		width: 100%;
		height: 100%;


		.group {
			padding: 0 30rpx 628rpx;
			flex: 1 1 auto;


			.group_6 {
				margin-top: 80rpx;
				padding-left: 66rpx;
				padding-right: 64rpx;

				.button {
					padding: 26rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 48rpx;
				}

				.button_1 {
					margin-top: 40rpx;
					padding: 26rpx 0;
					color: rgb(153, 153, 153);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(244, 244, 244);
					border-radius: 48rpx;
				}
			}

			.group_2 {
				padding: 30rpx 2rpx 26rpx;
				border-bottom: solid 2rpx rgba(0, 0, 0, 0.06);

				.text_1 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_3 {
					color: rgb(80, 80, 80);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_4 {
					margin-left: 96rpx;
				}

				.text_6 {
					margin-left: 64rpx;
				}
			}

			.group_3 {
				padding: 30rpx 2rpx 26rpx;
				border-bottom: solid 2rpx rgba(0, 0, 0, 0.06);
				position: relative;

				.text_7 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					position: absolute;
					left: 2rpx;
					top: 50%;
					transform: translateY(-50%);
				}

				.text_8 {
					color: rgb(80, 80, 80);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}
			}

			.group_4 {
				padding: 30rpx 2rpx 28rpx;
				border-bottom: solid 2rpx rgba(0, 0, 0, 0.06);

				.text_9 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_10 {
					margin-left: 64rpx;
					color: rgb(80, 80, 80);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
				}
			}

			.group_5 {
				padding: 24rpx 2rpx;
				color: rgb(0, 0, 0);
				font-size: 26rpx;
				font-weight: 500;
				line-height: 44rpx;
				white-space: nowrap;

				.text_11 {
					margin: 4rpx 0;
				}

				.switch {
					border-radius: 26rpx;
					width: 96rpx;
					height: 52rpx;
				}
			}
		}
	}
</style>
