<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<view style="width:96%;margin:0 auto;color:red;font-size: 30rpx;">
				*如果在服务过程中，技师要求私下交易，您可以保存证据并联系平台客服：15888095898，一经查实，平台将给予您免单处理
			</view>
			<view class="flex-col section_1">

				<view class="flex-row group_2">
					<image src="../../static/my/order-tag.png" class="image_1 image_2" />
					<text class="text_1">不满意重服务</text>
					<image src="../../static/my/order-tag.png" class="image_1 image_3" />
					<text class="text_2">满意为止</text>
				</view>
			</view>
			<view class="flex-col group_3" style="flex-shrink:0;">
				<view class="flex-row section_2">
					<view class="flex-col section_3" :style="{'backgroundImage': `url(${serve_info.cover})`}">

					</view>
					<view class="flex-col group_4">
						<text class="text_4">{{ serve_info.name }}</text>
						<view class="flex-row group_5">
							<view class="flex-col items-center text-wrapper_1">
								<text>领券立减</text>
							</view>
							<view class="flex-col items-center text-wrapper_2">
								<text>限时折扣</text>
							</view>
						</view>
						<view class="flex-row group_6">
							<image
								src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844603803770493.png"
								class="image_4" />
							<text class="text_7">时长{{ serve_info.duration }}分钟</text><text
								class="text_7">价格{{ serve_info.price }}元</text>
						</view>
						<text class="text_8" style="padding-top:10rpx;">已累计成交{{ serve_info.sales }}单</text>
					</view>
				</view>
				<view class="justify-between section_4"
					@click="toPath('/bundle/pages/user_address/user_address?type=select')">
					<view class="flex-col">
						<view class="flex-row group_8">
							<image
								src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844608064588645.png"
								class="image_5" />
							<text v-if="address_info.tel"
								class="text_9">{{ address_info.province }}{{ address_info.city }}{{ address_info.district }}{{ address_info.address }}</text>
							<text v-if="!address_info.tel" class="text_9">请选择地址</text>
						</view>
						<view class="flex-row group_9">
							<text>{{ address_info.realname }}</text>
							<text class="text_11">{{ address_info.tel }}</text>
						</view>
					</view>
					<image
						src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
						class="image_1 image_7" />
				</view>
				<view class="section_5 justify-between">
					<view class="left-group flex-row">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844607512197088.png"
							class="image_5" />
						<text class="text_12">服务技师</text>
					</view>
					<view class="flex-row group_10">
						<text>{{ technician.realname }}</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_1 image_9" />
					</view>
				</view>
				<view class="section_5 justify-between" @click="timeClick">
					<view class="left-group flex-row">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844608350397705.png"
							class="image_5" />
						<text class="text_12">服务时间<text style="font-size: 20rpx; color: #808080;">(只能大于当前时间)</text>
						</text>
					</view>
					<!-- 21333333333333333333333333333333333333333333333333333333333333333333 -->
					<view class="flex-row group_11">
						<text>{{ service_time }}</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_1 image_11" />
					</view>
				</view>
				<view class="flex-col section_6">
					<view class="justify-between group_12">
						<view class="flex-row group_13">
							<image
								src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844608652495015.png"
								class="image_5" />
							<text class="text_17">出行方式</text>
						</view>
						<view class="flex-row">
							<view v-for="(item,index) in valuation" :key="index"
								class="flex-col items-center text-wrapper_3"
								:class="{'active':travelTypeValue== index + 1}" @click="travelType(index + 1)">
								<text>{{ item.name }}</text>
							</view>
							<!-- <view class="flex-col items-center text-wrapper_4" :class="{'active':travelTypeValue==2}"
								@click="travelType(2)">
								<text>网约/出租</text>
							</view> -->
						</view>
					</view>
					<view class="justify-between section_7">
						<text class="text_20">全程共{{ kilometre }}km</text>
						<view class="group_15">

							<text class="text_21">来回车费</text>
							<text class="text_22">{{ kilometreValue }}</text>
							<text class="text_23">元</text>
						</view>
					</view>
				</view>
				<view class="section_5 justify-between" v-show="couponList.length" @click="showPopup = true">
					<view class="left-group flex-row">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844608789159711.png"
							class="image_5" />
						<text class="text_12">优惠券</text>
					</view>
					<view class="flex-row group_16">
						<view class="flex-col items-center text-wrapper_5" v-if="popup_info.name">
							<text>{{ popup_info.name }}</text>
						</view>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_1 image_14" />
					</view>
				</view>
				<view class="section_8 justify-between">
					<view class="flex-row group_17">
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611130353859.png"
							class="image_5" />
						<text class="text_26">订单备注</text>
					</view>
					<view class="flex-row group_18" @click="messageClick">
						<text class="text1" v-if="messageValue">{{messageValue}}</text>
						<text class="text1" v-else>选填</text>
						<image
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_1 image_16" />
					</view>
				</view>
				<!--支付方式start-->
				<u-radio-group v-model="payType" placement="column" style="display: block;">
					<view class="justify-between section_10" :class="!index ? 'section_9': ''"
						v-for="(item,index) in payList" :key="index" style="flex-shrink:0;">
						<view class="flex-row" style="flex-shrink:0;">
							<image
								:src="item.name == '账户余额' ? 'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844612123080341.png':item.name == '微信支付' ? 'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844612334581390.png' : 'https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844612491703390.png'"
								class="image_5" />
							<text class="margin_left">{{ item.name }}</text>
							<text class="text_30" v-if="!index">（余额：￥{{ Number(user.balance) }}）</text>
						</view>
						<u-radio :customStyle="{marginBottom: '8px'}" :key="index" :name="item.name">
						</u-radio>
					</view>
				</u-radio-group>
				<!--支付方式end-->
				<view class="flex-col group_22" style="flex-shrink:0;">
					<text class="text_33">温馨提示：服务开始前120分钟可以免费取消，不足120分钟取消订单平台将扣除您的20%的支付金额</text>

				</view>
				<view class="group_23 flex" style="flex-shrink:0;">
					<!-- <u-radio-group v-model="radioValue">
						<u-radio shape="circle" activeColor="#18b567"></u-radio>
					</u-radio-group> -->
					<u-checkbox-group v-model="checkboxValue1" placement="column">
						<u-checkbox :customStyle="{marginBottom: '8px'}" v-for="(item, index) in checkboxList1"
							:key="index" :label="item.name" :name="item.name" :checked="item.checked"
							activeColor="rgb(24, 181, 103)">
						</u-checkbox>
					</u-checkbox-group>
					<text class="text_34"></text>
					<text class="text_35">《用户使用协议》</text>
				</view>
			</view>
		</view>
		<view class="confirm_btn">
			<view class="justify-between section_12">
				<view class="flex-col">
					<view class="flex-row">
						<text class="text_36">合计</text>
						<view class="group_26">
							<text
								class="text_37">￥{{ Number(serve_info.price)*vip_discount + Number(kilometreValue) - Number(poupon_price)|numFilter }}</text>
							<text class="text_38"></text>
						</view>
					</view>
					<text v-if="user.is_vip == 1" class="text_39">已优惠 {{ poupon_price|numFilter}} +
						VIP优惠:{{(Number(serve_info.price)*(1 - vip_discount))|numFilter}}元</text>
					<text v-else
						class="text_39">已优惠{{ poupon_price+(Number(serve_info.price)-Number(serve_info.price)*vip_discount)|numFilter }}元</text>
				</view>
				<view class="flex-col items-center text-wrapper_6" @click="submit">
					<text>立即支付</text>
				</view>
			</view>
		</view>
		<!--留言-->
		<u-popup :show="message" @close="close" :round="30" :closeable='true'>
			<view style="height: 500rpx;padding: 80rpx 30rpx;">
				<u--textarea placeholder="请输入内容" border="surround" clearable count maxlength="200" height="120"
					v-model="messageValue"></u--textarea>
				<u-button text="保存" color="#18b567" 　style="width: 40%;margin: 30rpx auto;" @click="messageSave">
				</u-button>

			</view>

		</u-popup>
		<!--留言end-->
		<!--预约时间-->
		<u-popup :show="time" @close="close" :round="30" bgColor="#18B567">
			<view class="time-head">
				<view class="title"> 选择服务时间</view>
				<week-time @back="changeBack" @changeServiceTime="changeServiceTime" :id="technician_id"></week-time>
			</view>
		</u-popup>
		<!--预约时间end-->

		<!-- 选择优惠券 -->
		<u-popup :show="showPopup" @close="close1">
			<view class="popup">
				<view class="popup-header">
					<text>优惠券领取</text>
				</view>

				<view class="content">
					<scroll-view scroll-y="true" style="height: 710rpx;" scroll-with-animation="true">
						<view class="item flex" v-for="(item,index) in couponList" @click="selectPopup(item)">　
							<view class="left">
								<view class="num">
									<!-- <text>{{ item.money }}</text> -->
									<text style="font-size: 37rpx;" v-if="item.type==1">{{ item.name }}</text>
									<text v-else>¥{{ item.money }}</text>
								</view>
								<view class="condition">
									<text v-if="item.type==1"></text>
									<text v-else>{{ item.name }}</text>
								</view>
							</view>
							<view class="right flex">
								<view class="circular">
									<view class="circular-item" v-for="(item,index) in ['','','','']">
									</view>
								</view>
								<view class="explain">
									<view class="title">
										店铺优惠券</view>
									<view class="details">有效期至{{ item.end_time }}</view>
								</view>

							</view>
						</view>
					</scroll-view>
				</view>
			</view>
		</u-popup>

	</view>
</template>

<script>
	import weekTime from '@/components/weeks-calendars/weekTime.vue'
	import dateTime from '@/components/weeks-calendars/dateTime.vue'
	import tmWeekbar from "@/tm-vuetify/components/tm-weekbar/tm-weekbar"
	import tmCartBarFood from "@/tm-vuetify/components/tm-cartBarFood/tm-cartBarFood"
	import {
		pay
	} from '@/config/utils'
	export default {
		components: {
			tmCartBarFood,
			tmWeekbar,
			weekTime,
			dateTime
		},
		filters: {
			numFilter(value) {
				// 截取当前数据到小数点后两位
				let realVal = parseFloat(value).toFixed(2)
				return realVal
			}
		},
		data() {
			return {
				couponList: [],
				showPopup: false,
				checkboxValue1: ['本人已同意'],
				// 基本案列数据
				checkboxList1: [{
					name: '本人已同意',
					disabled: false,
					checked: true
				}],
				message: false, //留言弹出
				messageValue: "", //留言内容
				time: false, //预约弹出
				timeValue: '2021-10-1', //预约时间
				radioValue: false,
				travelTypeValue: 1,
				// 会员优惠价
				vipp_price: 0,
				payList: [{
						name: '账户余额'
					},
					{
						name: "微信支付"
					}
					// #ifndef H5
					, {
						name: "支付宝支付"
					}
					// #endif
				],
				// ['wxPay', 'aliPay', 'balance']
				payType: '',
				id: '',
				serve_info: {
					"id": '',
					"name": "",
					"price": "",
					"cover": "",
					"discount": '',
					"sales": 0,
					"praise": "",
					"duration": '',
					"coupon": 1
				},
				technician: {
					realname: '',
				},
				address_info: {
					"id": '',
					"realname": "",
					"mobile": "",
					"province": "",
					"city": "",
					"district": "",
					"address": "",
					"is_default": 0
				},
				valuation: [{
					kilometre: 0
				}],
				user: {
					"id": 0,
					"is_vip": 0,
					"balance": null,
					"vip_discount": "0"
				},
				message: false,
				service_time: '', // 时间
				poupon_price: 0,
				available: [], //优惠券
				coupon_id: 0,
				appointment_time_t: '',
				appointment_time_y: '',
				popup_info: {
					"id": 0,
					"name": "",
					"money": "",
					"discount": "",
					"condition": "",
					"type": 0,
					"service_id": 0,
					"start_time": "",
					"end_time": "",
					"reason": "",
					"address_id": '',
					"technician_lat": '',
					"technician_lng": '',

				},
				kilometre: '',
				kilometreValue: "",
				vip_discount: 1,
				technician_lng: 0,
				technician_lat: 0,
				technician_id: 0,
				fatherVal: '',
				isCreate: false
			};
		},
		watch: {
			address_info(newVal, oldVal) {
				console.log("地址获取")
				console.log(newVal)
				if (newVal.id) {
					this.address_id = newVal.id
					this.getDistance()
				}
			}
		},
		onLoad(options) {

			let sers = uni.getStorageSync('this.thsDay')
			console.log(sers, "sers")
			this.id = options.id;
			let date = new Date();
			this.appointment_time_y = this.fatherVal
			this.init();
		},
		onShow() {
			let address_info = uni.getStorageSync('address_info');
			console.log(address_info, 'info')
			if (address_info) {
				uni.removeStorageSync('address_info');
				let tel = address_info.mobile;
				address_info.tel = tel.slice(0, 3) + '****' + tel.slice(7)
				this.address_info = address_info;
			}

		},
		methods: {
			getthsDay(e) {
				console.log(e, "e.data")
				// this.fatherVal = e
			},
			//车费计算
			getKilometre(e) {
				console.log(this.valuation)
				console.log(this.travelTypeValue)
				//this.kilometreValue  =   this.kilometre *
			},
			selectPopup(item) {
				// console.log(this.user.is_vip);
				// return
				this.popup_info = item;
				let price = this.serve_info.price;
				this.coupon_id = item.id;
				if (item.type === 1) {
					// 折扣券
					this.poupon_price = price * (10 - Number(item.discount)) / 10;
				} else {
					// 满减券
					this.poupon_price = item.money;
				}
				console.log(this.poupon_price)
				this.showPopup = false;
			},
			fill(a) {
				return 10 > a ? '0' + (0 | a) : a
			},
			close1() {
				this.showPopup = false;
			},
			changeServiceTime(e) {
				console.log(e, "自组件的值")
				// e 就是用户选中的时间，今天的时间没有年，明天的时间有年
				this.service_time = e.time;
				this.appointment_time_y = e.serd
				this.fatherVal = this.appointment_time_y
				let arr = e.time.split(' ');
				this.appointment_time_t = arr[1];
				this.time = false;
			},
			init() {
				// 订单结算
				let order_info = uni.getStorageSync('order_info');
				let {
					service_id,
					spec_id,
					technician_id
				} = order_info;
				this.$http.post('api/order/settlement', {
					service_id,
					spec_id,
					technician_id
				}).then(res => {
					let {
						service,
						technician,
						valuation,
						user
					} = res;
					this.serve_info = service;
					this.technician = technician;
					console.log("=======")
					console.log(res)
					this.technician_lng = technician.lng
					this.technician_lat = technician.lat
					this.technician_id = technician.id
					this.valuation = valuation;
					this.user = user;
					if (user.is_vip) {
						this.vip_discount = Number(user.vip_discount) / 100
					}
					this.getDistance()
					// 获取优惠券
					this.$http.post('api/order/coupon', {
						service_id,
						money: service.price
					}).then(res => {
						console.log(res, 'xxxx');
						let {
							available
						} = res;

						this.couponList = available;
					})
				})
				// 获取地址列表
				this.$http.post('api/address/index').then(res => {
					if (res.length) {
						for (let data of res) {
							if (data.is_default == 1) {
								let tel = data.mobile;
								data.tel = tel.slice(0, 3) + '****' + tel.slice(7)
								this.address_info = data;
							}
						}
					}

				})
			},
			//重新计算
			getDistance() {
				// 获取地址列表
				console.log("重新计算")
				if (this.technician_lat && this.address_id) {
					this.$http.post('api/order/getDistance', {
						address_id: this.address_id,
						technician_lat: this.technician_lat,
						technician_lng: this.technician_lng
					}).then(res => {
						this.kilometre = res
						var valuationFlg = this.valuation[this.travelTypeValue - 1]
						console.log("==========")
						console.log('出发类型',this.valuation)
						console.log(this.kilometre - valuationFlg.kilometre, )
						if (this.kilometre <= valuationFlg.free) {
							this.kilometreValue = 0
							return
						}
						if ((this.kilometre - valuationFlg.kilometre) > 0) {
							console.log(valuationFlg.kilometre, '打印的值')
							this.kilometreValue = (valuationFlg.starting_price + (this.kilometre - valuationFlg
								// .kilometre) * valuationFlg.kilometre * valuationFlg.average_price).toFixed(2)
								.kilometre) * valuationFlg.average_price).toFixed(2)
						} else {
							this.kilometreValue = valuationFlg.starting_price
						}

					})
				}

			},
			changeBack(t) {

			},
			radioChange(e) {
				this.appointment_time_y = e;

			},
			messageClick() {
				this.message = true
			},
			timeClick() {
				this.time = true
			},
			close() {
				this.message = false
				this.time = false
			},
			messageSave() {
				this.message = false
			},
			travelType(e) {
				this.travelTypeValue = e
				this.getDistance()
			},
			async submit() {
				var that = this;
				if (that.isCreate) {
					uni.showToast({
						title: '请勿重复提交订单',
						icon: 'none'
					})
					return
				}

				console.log(this.checkboxValue1)
				//return
				let order_info = uni.getStorageSync('order_info');
				order_info.address_id = this.address_info.id;
				console.log(order_info, '打印的order_info')
				if (!order_info.address_id) {
					uni.showToast({
						title: '请先选择地址',
						icon: 'none'
					})
					return
				}

				if (this.checkboxValue1.length == 0) {
					uni.showToast({
						title: '请同意用户协议',
						icon: 'none'
					})
					return
				}
				//return

				order_info.address_id = this.address_info.id;
				order_info.remark = this.messageValue;

				// order_info.pay_money = this.serve_info.price;
				order_info.pay_money = Number((Number(this.serve_info.price) * this.vip_discount).toFixed(2)) + Number(
					this.kilometreValue) - Number(this.poupon_price)

				order_info.fare = Number(this.kilometreValue);
				order_info.discount = this.poupon_price + Number((Number(this.serve_info.price) - Number(this
						.serve_info.price) *
					this.vip_discount).toFixed(2));
				order_info.coupon_id = this.coupon_id;
				order_info.total_money = Number(this.serve_info.price) + Number(this.kilometreValue)

				// this.appointment_time_y 获取技师的服务时间的年月日
				// this.appointment_time_t 获取技师的服务的当前时间
				order_info.appointment_time = this.appointment_time_y + ' ' + this.appointment_time_t;
				// 下单时订单的请求参数：服务时间
				if (!this.appointment_time_t) {
					uni.showToast({
						icon: 'none',
						title: '请选择服务时间'
					})
					return;
				}
				

				if (!this.payType) {
					uni.showToast({
						icon: 'none',
						title: '请选择支付方式'
					})
					return;
				}
				// if (this.payType === '账户余额' && order_info.total_money > Number(this.user.balance)) {
				// 	uni.showToast({
				// 		icon: 'none',
				// 		title: '账户余额不足'
				// 	})
				// 	return;
				// }
				
				order_info.travelTypeValue = this.valuation[this.travelTypeValue - 1].id;
				
				console.log("创建订单",order_info)
				let order_sn = await this.$http.post('api/order/createOrder', order_info);

				if (order_sn == '') {

				}
				let pay_way = this.payType === '账户余额' ? 1 : this.payType === '微信支付' ? 2 : 3;

				var brow = 0;

				// #ifdef H5

				brow = 1;

				// #endif

				let info = await this.$http.post('api/pay/pay', {
					order_sn,
					pay_way,
					form: 'order',
					brow: brow
				});
				that.isCreate = true;
				order_info.payType = this.payType;
				order_info.order_sn = order_sn;
				let date = new Date();
				let create_time = date.getFullYear() + '-' + this.fill(Number(date.getMonth()) + 1) + '-' + this.fill(
						date.getDate()) + this.fill(date.getHours()) + ':' + this.fill(date.getMinutes()) + ':' + this
					.fill(date.getDate());
				order_info.create_time = create_time;
				console.log(info, "打印的info")
				console.log(order_sn, "打印deorder_sn")
				console.log(pay_way, "打印的pay_way")
				uni.setStorageSync('order_info', order_info);
				if (pay_way !== 1) {
					//if(pay_way == 3 ){
					// #ifndef H5
					let strUrl = info.codeUrl
					console.log(strUrl, '打印的strUrl')
					// plus.runtime.openURL(strUrl)
					pay(info, pay_way === 2 ? 'wxpay' : 'alipay').then(res => {

						// that.$http
						// 	.post('api/user/smsCode', {
						// 		mobile: this.phone,
						// 		message:''
						// 	})
						// 	.then(res => {
						// 		this.getCodeState();
						// 	});

						uni.navigateTo({
							url: '/bundle/pages/pay_success/new_file'
						})
					}).catch(e => {
						uni.navigateTo({
							url: '/pages/order/order'
						})
					});
					// uni.navigateTo({
					// 	url: '/bundle/pages/pay_success/new_file'
					// })
					//}

					// #endif

					// #ifdef H5
					this.callPay(info);

					// #endif

					return;
				} else {
					uni.navigateTo({
						url: '/bundle/pages/pay_success/pay_success'
					})
					// plus.runtime.openUrl(info.codeUrl)
				}
			},
			toPath(url) {
				uni.navigateTo({
					url,
				})
			},
			callPay(response) {
				if (typeof WeixinJSBridge === "undefined") {
					if (document.addEventListener) {
						document.addEventListener('WeixinJSBridgeReady', this.onBridgeReady(response), false);
					} else if (document.attachEvent) {
						document.attachEvent('WeixinJSBridgeReady', this.onBridgeReady(response));
						document.attachEvent('onWeixinJSBridgeReady', this.onBridgeReady(response));
					}
				} else {
					this.onBridgeReady(response);
				}
			},
			onBridgeReady(response) {
				let that = this;
				if (!response.package) {
					return;
				}

				console.log('微信公众号支付参数', {
					"appId": response.appId, //公众号名称，由商户传入
					"timeStamp": response.timestamp, //时间戳，自1970年以来的秒数
					"nonceStr": response.noncestr, //随机串
					"package": response.package,
					"signType": 'MD5', //微信签名方式：
					"paySign": response.sign //微信签名
				})

				WeixinJSBridge.invoke(
					'getBrandWCPayRequest', {
						"appId": response.appId, //公众号名称，由商户传入
						"timeStamp": response.timeStamp, //时间戳，自1970年以来的秒数
						"nonceStr": response.nonceStr, //随机串
						"package": response.package,
						"signType": 'MD5', //微信签名方式：
						"paySign": response.sign //微信签名
					},
					function(res) {
						if (res.err_msg === "get_brand_wcpay_request:ok") {
							// 使用以上方式判断前端返回,微信团队郑重提示：
							//res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
							uni.showLoading({
								title: '支付成功'
							});

							setTimeout(function() {
								uni.hideLoading();

								uni.navigateTo({
									url: '/bundle/pages/pay_success/new_file'
								})

							}, 1000);
						} else {
							uni.hideLoading();
						}
						WeixinJSBridge.log(response.err_msg);
					}
				);
			},
		}
	};
</script>

<style scoped lang="scss">
	.page {

		background-color: #f8f8f8;
		width: 100%;

		// overflow-y: auto;

		.popup {
			padding: 40rpx 32rpx;
			background-color: white;

			&-header {
				color: rgba(0, 0, 0, 1);
				font-size: 32rpx;
				font-weight: bold;

				text {
					position: relative;
				}

				text::before {
					position: absolute;
					top: 50%;
					transform: translateY(-50%);
					left: -10rpx;
					content: '';
					width: 8rpx;
					height: 32rpx;
					border-radius: 4rpx;
					background-color: rgba(24, 181, 103, 1);
				}
			}

			.content {
				// height: 900rpx;

				.item {
					width: 95%;
					margin: 20rpx auto;
					height: 180rpx;
					background: #EDF9F3;
					border-radius: 20rpx;

					.right {
						width: 65%;
						height: 180rpx;
						background: #18B567;
						border-radius: 0 20rpx 20rpx 0;
						float: right;
						position: relative;
						overflow: hidden;

						.circular {
							height: 180rpx;
							display: flex;
							margin-left: -16rpx;
							flex-direction: column;
							justify-content: space-around;

							.circular-item {
								height: 30rpx;
								width: 30rpx;
								border-radius: 50%;
								background: #EDF9F3;

							}
						}

						.explain {
							margin: auto 20rpx;

							.title {
								font-size: 32rpx;
								font-weight: bold;
								font-family: PingFangSC-Medium, PingFang SC;
								color: #FFFFFF;
							}

							.details {
								font-size: 28rpx;
								font-family: PingFang-SC-Regular, PingFang-SC;

								color: #FFFFFF;
							}

						}

						.receive {
							margin-left: 36rpx;
							padding: 10rpx 0 6rpx;
							align-self: center;
							color: rgb(24, 181, 103);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
							background-image: linear-gradient(124.4deg,
									rgb(255, 255, 255) 0%,
									rgb(255, 255, 255) -13.77%,
									rgb(189, 255, 222) 111.69%,
									rgb(189, 255, 222) 100%);
							box-shadow: 4rpx 6rpx 12rpx 0px rgb(1, 147, 74);
							border-radius: 27rpx;
							width: 108rpx;
							height: 56rpx;
							text-align: center;

						}

						.receive_img {
							width: 130rpx;


							image {
								width: 140rpx;
								height: 140rpx;
								position: absolute;
								bottom: -30rpx;
								right: -20rpx;
							}
						}
					}

					.left {
						width: 40%;
						text-align: center;
						margin: auto 0;

						.num {

							margin: auto;

							color: #18B567;

							text {
								font-family: Price-Bold;
								font-size: 70rpx;
								font-weight: bold;
							}

						}

						.condition {
							font-family: PingFangSC-Regular, PingFang SC;
							font-weight: 400;
							color: #505050;
						}
					}
				}
			}
		}

		.group {
			// height: 1624rpx;

			.section_1 {
				// padding: 38rpx 32rpx 146rpx;
				height: 220rpx;
				background-image: linear-gradient(128.9deg,
						rgb(49, 208, 129) 0%,
						rgb(49, 208, 129) -6.14%,
						rgb(24, 181, 103) 111.55%,
						rgb(24, 181, 103) 100%);
				border-radius: 0px 0px 60rpx 60rpx;


				.group_2 {
					padding: 0 30rpx;
					margin-top: 32rpx;
					color: rgb(255, 255, 255);
					font-size: 28rpx;
					line-height: 40rpx;


					.image_2 {
						margin: 4rpx 0;
					}

					.text_1 {
						margin-left: 8rpx;
					}

					.image_3 {
						margin: 4rpx 0 4rpx 24rpx;
					}

					.text_2 {
						margin-left: 8rpx;
					}
				}
			}

			.group_3 {
				margin-top: -100rpx;
				padding: 0 32rpx;

				position: relative;

				.section_2 {
					padding: 24rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx;

					// height: 320rpx;
					.section_3 {
						padding-top: 140rpx;
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						line-height: 34rpx;
						width: 216rpx;
						height: 192rpx;
						border-radius: 12rpx;
						// background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844604386470819.png');
						background-size: cover;
						background-repeat: no-repeat;


						.text-wrapper {
							padding: 8rpx 0 10rpx;
							background-color: rgba(0, 0, 0, 0.36);
							border-radius: 0px 0px 12rpx 12rpx;

							.text_3 {
								margin: 0 6rpx;
							}
						}
					}

					.group_4 {
						margin-left: 24rpx;
						flex: 1 1 auto;

						.text_4 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.group_5 {
							margin-top: 16rpx;

							.text-wrapper_1 {
								padding: 4rpx 0;
								color: rgb(255, 255, 255);
								font-size: 20rpx;
								line-height: 28rpx;
								white-space: nowrap;
								background-color: rgb(243, 151, 53);
								border-radius: 8rpx;
								width: 104rpx;
								height: 36rpx;
							}

							.text-wrapper_2 {
								margin-left: 6rpx;
								padding: 2rpx 0;
								color: rgb(243, 151, 53);
								font-size: 20rpx;
								line-height: 28rpx;
								white-space: nowrap;
								border-radius: 8rpx;
								width: 104rpx;
								height: 36rpx;
								border: solid 2rpx rgb(243, 151, 53);
							}
						}

						.group_6 {
							margin-top: 20rpx;
							padding: 0 4rpx;
							color: rgb(102, 102, 102);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;

							.image_4 {
								margin: 6rpx 0 4rpx;
								width: 24rpx;
								height: 24rpx;
							}

							.text_7 {
								margin-left: 8rpx;
							}
						}

						.text_8 {
							margin-top: 8rpx;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
						}
					}
				}

				.section_4 {
					margin-top: 24rpx;
					padding: 16rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx 24rpx 0px 0px;

					.image_7 {
						align-self: center;
					}

					.group_8 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;

						.text_9 {
							margin-left: 8rpx;
						}
					}

					.group_9 {
						margin-top: 6rpx;
						padding: 0 48rpx;
						color: rgb(102, 102, 102);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;

						.text_11 {
							margin-left: 16rpx;
						}
					}
				}

				.section_5 {
					padding: 24rpx 24rpx;
					background-color: rgb(255, 255, 255);

					.left-group {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;

						.text_12 {
							margin-left: 8rpx;
						}
					}

					.group_10 {
						color: rgb(24, 181, 103);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;

						.image_9 {
							margin: 4rpx 0 4rpx 16rpx;
						}
					}

					.group_11 {
						color: rgb(24, 181, 103);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;

						.image_11 {
							margin: 4rpx 0 4rpx 14rpx;
						}
					}

					.group_16 {
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						font-weight: 500;
						line-height: 34rpx;
						white-space: nowrap;

						.text-wrapper_5 {
							padding: 2rpx 0 4rpx;
							background-color: rgb(243, 151, 53);
							border-radius: 8rpx;
							width: 160rpx;
							height: 40rpx;
						}

						.image_14 {
							margin: 4rpx 0 4rpx 16rpx;
						}
					}
				}

				.section_6 {
					padding: 0 24rpx 24rpx;
					background-color: rgb(255, 255, 255);

					.group_12 {
						padding: 24rpx 0;

						.group_13 {
							color: rgb(0, 0, 0);
							font-size: 28rpx;
							font-weight: 500;
							line-height: 40rpx;
							white-space: nowrap;

							.text_17 {
								margin-left: 8rpx;
							}
						}

						.text-wrapper_3 {
							padding: 2rpx 0 4rpx;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							font-weight: 500;
							line-height: 34rpx;
							white-space: nowrap;
							background-color: rgb(244, 244, 244);
							border-radius: 8rpx;
							width: 140rpx;
							height: 40rpx;

							&.active {
								color: rgb(255, 255, 255);
								background-color: rgb(24, 181, 103);
							}
						}

						.text-wrapper_4 {
							margin-left: 16rpx;
							padding: 2rpx 0 4rpx;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							font-weight: 500;
							line-height: 34rpx;
							white-space: nowrap;
							background-color: rgb(244, 244, 244);
							border-radius: 8rpx;
							width: 140rpx;
							height: 40rpx;

							&.active {
								color: rgb(255, 255, 255);
								background-color: rgb(24, 181, 103);
							}
						}
					}

					.section_7 {
						margin-top: 20rpx;
						margin-left: 48rpx;
						padding: 12rpx 0;
						background-color: rgba(24, 181, 103, 0.08);
						border-radius: 16rpx;

						.text_20 {
							color: rgb(102, 102, 102);
							font-size: 28rpx;
							//line-height: 30rpx;
							//white-space: nowrap;
						}

						.group_15 {
							white-space: nowrap;
							height: 40rpx;
							color: rgb(80, 80, 80);
							font-size: 28rpx;
							line-height: 40rpx;

							.text_21 {
								color: rgb(80, 80, 80);
								font-size: 28rpx;
								line-height: 40rpx;
							}

							.text_22 {
								color: rgb(24, 181, 103);
								font-size: 28rpx;
								line-height: 40rpx;
							}

							.text_23 {
								color: rgb(80, 80, 80);
								font-size: 28rpx;
								line-height: 40rpx;
							}
						}
					}
				}

				.section_8 {
					padding: 24rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 0px 0px 24rpx 24rpx;

					.group_17 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.group_18 {
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;

						.text1 {
							text-align: right;
							width: 400rpx;
							text-overflow: ellipsis;
							overflow: hidden;
							text-overflow: ellipsis;
							white-space: nowrap;

						}

						.image_16 {
							margin: 4rpx 0 4rpx 16rpx;
						}
					}

					.section_11 {
						margin: 6rpx 8rpx 6rpx 0;
						background-color: rgb(255, 255, 255);
						border-radius: 50%;
						width: 28rpx;
						height: 28rpx;
						border: solid 3rpx rgb(102, 102, 102);
					}

					.text_26 {
						margin-left: 8rpx;
					}
				}

				.section_9 {
					margin-top: 24rpx;
					padding: 22rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx 24rpx 0px 0px;

					.text_29 {
						margin-left: 8rpx;
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_30 {
						margin-left: 8rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.section_10 {
					padding: 22rpx 24rpx;
					color: rgb(0, 0, 0);
					font-size: 28rpx;
					font-weight: 500;
					line-height: 40rpx;
					white-space: nowrap;
					background-color: rgb(255, 255, 255);

					.text_31 {
						margin-left: 8rpx;
					}
				}

				.margin_left {
					margin-left: 8rpx;
				}

				.view_7 {
					color: rgb(0, 0, 0);
					font-size: 28rpx;
					font-weight: 500;
					line-height: 40rpx;
					white-space: nowrap;
				}

				.group_22 {
					margin-top: 24rpx;
					padding-bottom: 76rpx;
					color: rgb(153, 153, 153);
					font-size: 24rpx;
					line-height: 34rpx;
					position: relative;

					.text_33 {
						margin-right: 6rpx;
						text-align: left;
					}

					.image_23 {
						border-radius: 50%;
						width: 28rpx;
						height: 28rpx;
						position: absolute;
						left: 170rpx;
						bottom: 0;
					}
				}

				.group_23 {
					margin-top: -30rpx;
					align-self: center;
					line-height: 34rpx;
					white-space: nowrap;
					position: relative;
					padding-bottom: 150rpx;

					.text_34 {
						color: rgb(0, 0, 0);
						font-size: 24rpx;
						line-height: 34rpx;
					}

					.text_35 {
						color: rgb(24, 181, 103);
						font-size: 24rpx;
						line-height: 34rpx;
					}
				}

				.image_18 {
					width: 44rpx;
					height: 44rpx;
				}

				.image_5 {
					width: 40rpx;
					height: 40rpx;
				}
			}

			.image_1 {
				width: 32rpx;
				height: 32rpx;
			}
		}

		.confirm_btn {
			bottom: 0;
			position: fixed;
			width: 100vw;
			padding-bottom: 10rpx;
			background-color: rgb(255, 255, 255);

			.section_12 {


				padding: 16rpx 32rpx;
				padding-bottom: env(safe-area-inset-bottom);
				background-color: rgb(255, 255, 255);
				box-shadow: 0px -6rpx 16rpx 0px rgba(0, 0, 0, 0.08);

				.text-wrapper_6 {
					padding: 22rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 42rpx;
					width: 240rpx;
					height: 88rpx;
				}

				.text_39 {
					margin-top: 8rpx;
					color: rgb(243, 151, 53);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
				}

				.text_36 {
					margin-top: 12rpx;
					color: rgb(80, 80, 80);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
				}

				.group_26 {
					margin-left: 8rpx;
					white-space: nowrap;
					height: 44rpx;

					.text_37 {
						color: rgb(243, 151, 53);
						font-size: 24rpx;
						line-height: 28rpx;
					}

					.text_38 {
						color: rgb(243, 151, 53);
						font-size: 36rpx;
						line-height: 44rpx;
					}
				}
			}
		}

		.time-head {

			height: 1200rpx;
			padding: 30rpx 30rpx;
			overflow-y: auto;

			.title {
				text-align: center;
				font-size: 32rpx;
				font-family: PingFangSC-Medium, PingFang SC;
				font-weight: 500;
				color: #FFFFFF;
				padding-bottom: 30rpx;
			}
		}
	}
</style>
