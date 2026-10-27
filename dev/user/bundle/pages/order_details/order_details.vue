<template>
	<view class="flex-col page">

		<view class="flex-col group" style="flex-shrink:0;">
			<view class="justify-center group_1" style="flex-shrink:0;">
				<image
					src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844615651288038.png"
					class="image_1" />
				<text
					class="text">{{ order_status == 0 ? '等待买家付款' : order_status == 1 ? '等待技师确认' : order_status == 2 ? '待服务' : order_status == 3 ? '服务进行中' : order_status == 4 ? '已完成订单' : order_status == 5 ? '待评价订单' : '已取消订单' }}</text>
			</view>
			<text class="text_1" v-if="end_t !=='已过期'">剩{{ end_t }}自动关闭</text>
			<text class="text_1" v-if="end_t =='已过期'">已过期</text>
		</view>
		<view class="flex-col group_2" style="flex-shrink:0;">
			<view class="justify-between group_3">
				<view class="left-text-wrapper flex-col view" :class="{'select-active':teps==3}">
					<text class="text_2">技师出发</text>
				</view>
				<view class="left-text-wrapper flex-col" :class="{'select-active':teps==5}">
					<text class="text_2">开始服务</text>
				</view>
			</view>
			<view class="center-group justify-center view_2">
				<!-- <view class="flex-col items-center section_1">
					<view class="flex-col items-center text-wrapper">
					</view>
				</view> -->
				<view class="section_3 view_3 " :class="{'select-active':teps==1}">
					<view class="flex-col items-center text-wrapper" v-show="teps==1">
					</view>
				</view>
				<view class="section_2"> </view>
				<view class="section_3 view_3" :class="{'select-active':teps==2}">
					<view class="flex-col items-center text-wrapper" v-show="teps==2">
					</view>
				</view>
				<image
					src="https://project-user-resource-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844976579604976.png"
					class="image_2" />
				<view class="section_3" :class="{'select-active':teps==4}">
					<view class="flex-col items-center text-wrapper" v-show="teps==4">
					</view>
				</view>
				<image
					src="https://project-user-resource-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844976577539029.png"
					class="image_3" />
				<view class="section_3" :class="{'select-active':teps==6}">
					<view class="flex-col items-center text-wrapper" v-show="teps==6">
					</view>
				</view>
			</view>
			<view class="justify-between steps-text">
				<view class="flex-row">
					<text class="text_6" :class="{'text-active':teps==1}">下单</text>
					<text class="text_7" :class="{'text-active':teps==2}">接单</text>
				</view>
				<view class="flex-row group_6">
					<text :class="{'text-active':teps==4}">技师到达</text>

				</view>
				<view class="flex-row group_6">

					<text class="text_9" :class="{'text-active':teps==6}">服务完成</text>
				</view>
			</view>
		</view>
		<view class="flex-col group_7" style="flex-shrink:0;">
			<view class="justify-center group_8" style="flex-shrink:0;">
				<view class="flex-col items-center text-wrapper_1" @click="showCancel=true" v-if="order_status <4">
					<text>取消订单</text>
				</view>
				<view class="flex-col items-center text-wrapper_1" @click="showPay=true"
					style="margin:0rpx 20rpx;border-color:#00aaff;background: #00aaff;">
					<text>加单</text>
				</view>
				<view class="flex-col items-center text-wrapper_2" @click="show" v-if="order_status == 0">
					<text>立即支付</text>
				</view>
			</view>
			<view class="flex-col section_4" style="flex-shrink:0;">
				<view class="flex-col" style="flex-shrink:0;">
					<view class="flex-row group_10">
						<view class="section_5"> </view>
						<text class="text_12">服务项目</text>
					</view>
					<view class="flex-row group_11" style="flex-shrink:0;">
						<view class="flex-col section_6" :style="{'backgroundImage': `url(${cover})`}">
							<view class="flex-col text-wrapper_3" v-if="false">
								<text class="text_13">环境疲劳·激活能量</text>
							</view>
						</view>
						<view class="flex-col group_12">
							<text class="text_14">{{ name }}</text>
							<view class="group_13">
								<text class="text_15">￥</text>
								<text class="text_16">{{ pay_money }}</text>
							</view>
							<view class="center-group flex-row view_6">
								<image
									src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844603803770493.png"
									class="image_4" />
								<text class="text_17">时长{{ duration }}分钟</text>
							</view>
							<text class="text_18">服务时间：{{ appointment_time }}</text>
						</view>
					</view>
				</view>
				<view class="flex-col group_14">
					<view class="justify-between">
						<text class="text_19">出行交通费用</text>
						<text class="text_20">￥{{ fare }}</text>
					</view>
					<view class="justify-between group_16">
						<text class="text_21">优惠券</text>
						<text class="text_22">-￥{{ discount }}</text>
					</view>
				</view>
				<view class="flex-col group_17">
					<view class="divider"> </view>
					<view class="group_18 justify-between">
						<view class="flex-row">
							<image :src="avatar" class="image_5" />
							<view class="flex-col group_20">
								<view class="flex-row">
									<text class="text_23">{{ realname }}</text>
									<view class="flex-col items-end group_22">
										<view class="flex-col items-center text-wrapper_4">
											<text>认证</text>
										</view>
										<image
											src="https://project-user-resource-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844795953096993.png"
											class="image_6" />
									</view>
								</view>
								<view class="flex-row group_23">
									<!--  -->
									<u-icon v-for="(sitem,sindex) in parseInt(praise)" :key="sindex" name="star-fill"
										size="12" color="rgba(243, 151, 53, 1)"></u-icon>

									<text class="text_25">好评{{ praise }}分</text>
								</view>
							</view>
						</view>
						<!-- <view class="flex-col items-center text-wrapper_5" @click="call">
							<text>联系技师</text>
						</view> -->
					</view>
					<view class="divider"> </view>
				</view>
				<view class="group_18 flex-col">
					<view class="flex-row">
						<text class="text_27">{{ user_realname }}</text>
						<text class="text_28">{{ user_mobile }}</text>
					</view>
					<text class="text_29">{{ province }}{{ city }}{{ district }}{{ address }}</text>
				</view>
				<view class="flex-col">
					<view class="divider"> </view>
					<view class="group_18 flex-col">
						<view class="justify-between">
							<text class="text_30">实付款(含来回车费)</text>
							<view class="group_27">
								<text class="text_31">￥</text>
								<text class="text_32">{{ total_money }}</text>
							</view>
						</view>
						<view class="justify-between group_28">
							<text class="text_33">商品总额</text>
							<text class="text_34">￥{{ pay_money }}</text>
						</view>
						<view class="justify-between group_29">
							<text class="text_35">来回车费</text>
							<text class="text_36">￥{{ fare }}</text>
						</view>
					</view>
				</view>
				<view class="flex-col">
					<view class="divider"> </view>
					<view class="group_18 flex-col">
						<view class="justify-between">
							<text class="text_30">订单信息</text>

						</view>
						<view class="justify-between group_28">
							<text class="text_33">订单编号</text>
							<text class="text_34">{{ order_sn }}</text>
						</view>
						<view class="justify-between group_29">
							<text class="text_35">下单时间</text>
							<text class="text_36">{{ create_time }}</text>
						</view>
					</view>
				</view>
				<view class="divider"> </view>
				<view class="flex-col group_30">
					<text class="text_37">下单须知</text>
					<text class="text_38">1．如客户选择理疗师下单后，理疗师与该客户联系确定了服务时间、服务地址，理疗师按了出发，客户要取消订单的，该客户所付理疗师的交通费用不予退还。<br />
						2．理疗师到达服务地址后若联系不上客户，理疗师会等待20分钟；如20分钟后仍无法联系上，理疗师则会自行离开，并且该笔订单会自动取消，同时需扣除订单包含的车费和一半项目费。<br />
						3．客户选择公交／地铁下单的（包含1km免车费），理疗师出发10分钟内取消订单的扣取车费和20元空单费；出发10分钟后取消订单扣车费和一半项目费。<br />
						4．客户选择滴滴／出租车下单的，需收取来回车费，理疗师未按出发的费用全额退还给客户，已按出发的将支付相应的车费作为空单费。<br />
						5．客户无论选择公交／滴滴／出租车任一出行方式进行下单，若理<br />
						疗师已到达服务地址，客户取消订单，需扣除车费和一半项目费用，感谢您的支持和理解。<br /></text>
				</view>
				<text class="text_39">按时服务 | 质量保证 | 服务专业 | 条件退款</text>
				<view class="flex-col items-center text-wrapper_6" @click="calltel">
					<text>联系客服</text>
				</view>
			</view>
		</view>

		<u-popup :show="showCancel" @close="close" @open="open" mode="bottom">
			<view style="width:100%;background: #fff;">
				<view class="order-item" @click="checkedFund(index)" v-for="(item,index) in refundList">{{item.title}}
					<u-icon v-if="item.checked" name="checkbox-mark" color="#31d081" size="30"></u-icon>
				</view>
				<view class="order-item" v-if="showInput">
					<textarea :auto-height="true" maxlength="100" v-model="inputValue"
						placeholder="请输入您的退款理由5-100字"></textarea>
				</view>
				<view class="order-item"></view>
				<view style="width:90%;margin:0 auto;">
					<u-button type="warning" @click="hide" shape="circle" text="提交申请"></u-button>
				</view>
				<view class="order-item"></view>
			</view>
		</u-popup>

		<u-popup :show="showPay" @close="close1" @open="open1" mode="bottom">
			<view style="width:100%;background: #fff;">

				<view class="product-wrapper content-p" style="height:900rpx;overflow-y: auto;">
					<!-- 					<u-skeleton rows="10" :loading="loading" :title="false" rowsHeight="120">
					</u-skeleton> -->
					<view class="product-item flex-row" :key="i" v-for="(item, i) in productlList">
						<view class="left-section flex-col">
							<view class="text-wrapper flex-col">
								<u--image :showLoading="true" :src="item.cover" width=" 216rpx" height="182rpx"
									radius="4" :fade="true" duration="450" :lazy-load="true"></u--image>
								<text class="text_6">{{ item.label }}</text>
							</view>
						</view>
						<view class="right-group flex-col">
							<view class="flex-row justify-bw">
								<text class="text_8">{{ item.name }}</text>
								<!-- <text class="text_10">销量{{ item.sales }}</text> -->
							</view>
							<view class="group_7 flex-row">
								<view class="left-text-wrapper flex-col items-center" v-if="item.coupon">
									<text>领券立减</text>
								</view>
								<view class="right-text-wrapper flex-col items-center" v-if="item.discount">
									<text>限时折扣</text>
								</view>
							</view>
							<view class="group_8 flex-row">
								<image
									src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844603777503649.png"
									class="image_5" />
								<text class="text_14">时长{{ item.duration }}分钟</text>
							</view>
							<view class="group_9 flex-row">
								<view class="left-group">
									<text class="text_16">￥</text>
									<text class="text_18 price-font">{{ item.price }}</text>
								</view>
								<view class="center-group flex-col">
									<view class="group_10">
										<text class="text_20">￥</text>
										<text class="text_22 price-font1">{{item.original_price}}</text>
									</view>
									<view class="divider"> </view>
								</view>
								<u-number-box v-model="item.count" min="0" button-size="24" @change="changeNumber"
									:name="'A'+i" integer></u-number-box>
								<!-- <view class="right-text-wrapper_1 flex-col items-center">
									<text>立即下单</text>
								</view> -->
							</view>
						</view>
					</view>
				</view>

				<view style="width:90%;margin:0 auto;display: flex;justify-content: space-between;">
					<u-icon name="arrow-down" :label="payType" size="14" labelSize="16" labelPos="left"
						@click="sp=true"></u-icon>
					<u-button type="success" @click="createOrder" shape="circle" :text="'立即支付'+totalPayMoney+'元加单'"
						:disabled="totalPayMoney<=0" :customStyle="{'width':'60%'}">
					</u-button>
				</view>

				<!-- <view class="order-item" @click="checkedPay(index)" v-for="(item,index) in payList">{{item.name}}
					<u-icon v-if="item.checked" name="checkbox-mark" color="#31d081" size="30"></u-icon>
				</view>
				<view class="order-item"></view>
				<view style="width:90%;margin:0 auto;">
					<u-button type="warning" @click="createOrder" shape="circle" text="立即支付"></u-button>
				</view>
				<view class="order-item"></view> -->
			</view>
		</u-popup>

		<u-picker :show="sp" :columns="payList2" @close="sp=false" @confirm="confirmPay"></u-picker>

		<zPay ref="pay" :money="pay_money" :balance="balance" @pay="pay"></zPay>
	</view>
</template>

<script>
	import zPay from '@/components/z-pay/index.vue';
	import {
		pay
	} from '@/config/utils'
	export default {
		data() {
			return {
				showCancel: false,
				showInput: false,
				inputValue: '',
				showPay: false,
				sp: false,
				payType: '微信支付',
				payList2: [
					['账户余额', '微信支付'
					// #ifndef H5
					, '支付宝支付'
					// #endif
					]
				],
				payList: [{
						name: '账户余额',
						checked: false
					},
					{
						name: "微信支付",
						checked: true
					}
					// #ifndef H5
					, {
						name: "支付宝支付",
						checked: false
					}
					// #endif
				],
				refundList: [{
						title: '技师没有联系',
						isInput: false,
						checked: false
					},
					{
						title: '拍错了',
						isInput: false,
						checked: false
					},
					{
						title: '临时有事',
						isInput: false,
						checked: false
					},
					{
						title: '其他',
						isInput: true,
						checked: false
					}
				],

				teps: 1, //步骤
				order_sn: '',
				"id": 12,
				"pay_money": "",
				"order_status": 0,
				"appointment_time": "",
				"fare": "",
				"discount": "",
				"refund": null,
				"total_money": "",
				"create_time": "",
				"name": "",
				"duration": 60,
				"cover": "",
				"nickname": "",
				"realname": "",
				"praise": 0,
				"avatar": "",
				"end_t": '',
				balance: 0,
				technician_mobile: "",
				mobiles: '',
				province: '',
				city: '',
				district: '',
				address: '',
				service_id: '',
				address_id: '',
				spec_id: '',
				technician_id: '',
				user_mobile: '',
				user_realname: '',
				productlList: [],
				totalPayMoney: 0
			};
		},
		onLoad(options) {
			let {
				order_sn
			} = options;
			this.order_sn = order_sn;
			this.init();
			this.getList();

		},
		components: {
			zPay
		},
		methods: {
			confirmPay(e) {
				console.log('confirmPay', e);
				this.payType = e.value[0];
				this.sp = false;
			},
			changeNumber(e) {
				console.log('changeNumber', e)
				var index = parseInt(e.name.replace('A', ''));
				this.productlList[index].count = e.value;
				this.totalPayMoney = 0;
				this.productlList.forEach(item => {
					if (item.count > 0)
						this.totalPayMoney += (item.price * item.count);
				})
			},
			// 获取首页列表数据
			getList() {
				this.$http.post('api/order/speclist', {
					page: 1,
					orderBy: 0
				}).then(res => {
					let {
						list
					} = res;

					console.log('service原始数据',res)

					var newList = [];
					res.forEach(item => {
						newList.push({
							"id": item.id,
							"name": item.name,
							"original_price": item.original_price,
							"price": item.price,
							"cover": item.cover,
							"discount": 0,
							"sales": 0,
							"praise": 0,
							"duration": item.duration,
							"label": '',
							"coupon": 0,
							"count": 0
						});
					})
					this.productlList = newList;
					console.log('项目列表', newList)
					//this.total = total;
				})
			},
			checkedFund(index) {
				this.refundList.forEach(function(item) {
					item.checked = false;
				})

				this.refundList[index].checked = true;
				this.showInput = this.refundList[index].isInput;
				this.inputValue = '';
			},
			checkedPay(index) {
				this.payList.forEach(function(item) {
					item.checked = false;
				})

				this.payList[index].checked = true;
				this.payType = this.payList[index].name;
			},
			open() {

			},
			close() {
				this.showCancel = false;
			},
			open1() {

			},
			close1() {
				this.showPay = false;
			},
			async init() {
				// 获取客服信息 api/user/customerService
				this.$http.post('api/user/customerService').then(res => {
					console.log(res, 'xxxx')
					let {
						mobile
					} = res;
					this.mobiles = mobile;
				})
				// 获取余额
				this.$http.post('api/user/userInfo').then(res => {
					this.balance = res.balance;
				})
				let data = await this.$http.post('api/order/orderDetails', {
					order_sn: this.order_sn
				});
				console.log('订单详情', data)
				// for (let i in data) {
				// 	this[i] = data[i];
				// }

				this.order_sn = data.order_sn;
				this.id = data.id;
				this.pay_money = data.pay_money;
				this.order_status = data.order_status;
				this.appointment_time = data.appointment_time;
				this.fare = data.fare;
				this.discount = !data.discount?0:data.discount;
				this.refund = data.refund;
				this.total_money = data.total_money;
				this.create_time = data.create_time;
				this.address_id = data.address_id;
				this.service_id = data.service_id;
				this.spec_id = data.spec_id;
				this.technician_id = data.technician_id;
				this.user_realname = data.user_realname;
				this.user_mobile = data.user_mobile;
				this.province = data.province;
				this.city = data.city;
				this.district = data.district;
				this.address = data.address;
				this.name = data.name;
				this.realname = data.realname;
				this.duration = data.duration;
				this.cover = data.cover;
				this.nickname = data.nickname;
				this.praise = data.praise;
				this.avatar = data.avatar;
				this.technician_mobile = data.technician_mobile;



				this.teps = this.order_status == 0 ? 1 : this.order_status == 1 ? 2 : this.order_status == 2 ? 3 : this
					.order_status == 3 ? 5 : 6;
				this.end_t = this.showtime(this.appointment_time);
				let timer = null;
				if (this.end_t !== '已过期') {
					timer = setInterval(() => {
						if (this.end_t === '已过期') {
							clearInterval(timer);
							return;
						}
						this.end_t = this.showtime(this.appointment_time)
					}, 1000)
				}
			},
			call() {
				if (this.technician_mobile) {
					uni.makePhoneCall({
						phoneNumber: this.technician_mobile
					});
					return;
				}
				uni.showToast({
					icon: 'none',
					title: '该技师暂无号码'
				})
			},
			calltel() {
				if (this.mobiles) {
					uni.showModal({
						content: '是否拨打客服电话',
						success: (res) => {
							if (res.confirm) {
								uni.makePhoneCall({
									phoneNumber: this.mobiles
								});
							}
						}
					})
					return;
				}
				uni.showToast({
					icon: 'none',
					title: '暂无客服联系号码'
				})
			},
			add0(a) {
				a = Number(a)
				return a < 10 ? '0' + a : a;
			},
			showtime(endtime) {
				var nowtime = new Date(), //获取当前时间
					endtime = new Date(endtime.replace(/-/g, '/')); //定义结束时间
				var lefttime = endtime.getTime() - nowtime.getTime(), //距离结束时间的毫秒数
					leftd = Math.floor(lefttime / (1000 * 60 * 60 * 24)), //计算天数
					lefth = Math.floor(lefttime / (1000 * 60 * 60) % 24), //计算小时数
					leftm = Math.floor(lefttime / (1000 * 60) % 60), //计算分钟数
					lefts = Math.floor(lefttime / 1000 % 60); //计算秒数
				if (lefttime <= 0) {
					return '已过期'
				}
				return this.add0(leftd) + "天" + this.add0(lefth) + ":" + this.add0(leftm) + ":" + this.add0(
					lefts); //返回倒计时的字符串
			},
			hide() {
				uni.showModal({
					content: '是否确认取消该订单？',
					success: (res) => {
						let {
							confirm
						} = res;
						if (confirm) { 

							//cancelOrder
							this.$http.post('api/order/confirmCancel', {
								order_sn: this.order_sn,
								refreason: (!this.inputValue ? this.refundList.find(_ => _.checked)
									.title : this.inputValue)
							}).then(res => {
								uni.showToast({
									icon: 'none',
									title: '取消成功'
								});
								this.showCancel = false;
								this.init();
							})
						}
					}
				})
			},
			show() {
				this.$refs.pay.showPopup = true;
			},
			fill(a) {
				return 10 > a ? '0' + (0 | a) : a
			},
			getYear(type) {

				var date = new Date(),
					year = date.getFullYear(),
					month = date.getMonth() + 1,
					day = date.getDate(),
					hour = date.getHours() < 10 ? date.getHours() : date.getHours(),
					minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(),
					second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
				month >= 1 && month <= 9 ? (month = "0" + month) : "";
				day >= 0 && day <= 9 ? (day = "0" + day) : "";

				var timer = '';
				if (type == 'year')
					timer = year + '-' + month + '-' + day;
				else if (type == 'hour')
					timer = hour;
				else if (type == 'min')
					timer = minute;

				return timer;
			},
			async createOrder() {
				var that = this;
				var year = that.getYear('year');
				var hour = that.getYear('hour');
				if (parseInt(hour) >= 23) {
					hour = '00';
				} else {
					hour = parseInt(hour) + 1;
				}

				if (hour < 10)
					hour = '0' + hour;
				// var minute = that.getYear('minute');
				// if(parseInt(minute)<30)
				// minute = 00;
				var spec_ids = '';
				var spec_count = '';
				var service_id = '';
				that.productlList.forEach(item => {
					if (item.count > 0) {
						spec_ids += item.id + ',';
						spec_count += item.count + ',';
						// service_id+=item.service_id+',';
					}
				})

				if (spec_ids && spec_count) {
					spec_ids = spec_ids.substr(0, spec_ids.length - 1);
					spec_count = spec_count.substr(0, spec_count.length - 1);
				}else{
					uni.showToast({
						title:'请至少选择一个项目',
						icon:'error'
					})
					return;
				}

				var order_info = {
					// "service_id": service_id,
					"spec_id": spec_ids,
					"spec_count": spec_count,
					"technician_id": that.technician_id,
					"address_id": that.address_id,
					"appointment_time": (year + ' ' + hour + ':00'),
				}
				
				console.log('请求参数',order_info)

				let order_sn = await this.$http.post('api/order/createOrderJZ', order_info);

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
				order_info.pay_money = this.totalPayMoney;
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
			async pay(e) {
				let {
					pay_way,
					pay_type
				} = e;

				var brow = 0;

				// #ifdef H5

				brow = 1;

				// #endif

				let info = await this.$http.post('api/pay/pay', {
					order_sn: this.order_sn,
					pay_way,
					form: 'order',
					brow: brow
				});
				let date = new Date();
				let create_time = date.getFullYear() + '-' + this.fill(Number(date.getMonth()) + 1) + '-' + this.fill(
						date.getDate()) + this.fill(date.getHours()) + ':' + this.fill(date.getMinutes()) + ':' + this
					.fill(date.getDate());

				uni.setStorageSync('order_info', {
					create_time,
					order_sn: this.order_sn,
					payType: pay_type,
					pay_money: this.pay_money
				})
				if (pay_way !== 1) {
					pay(info, pay_way === 2 ? 'wxpay' : 'alipay').then(res => {
						uni.navigateTo({
							url: '/bundle/pages/pay_success/pay_success'
						})
					});
					return;
				} else {
					uni.navigateTo({
						url: '/bundle/pages/pay_success/pay_success'
					})
				}
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
			}
		}
	};
</script>

<style scoped lang="scss">
	.page {
		padding: 0rpx 32rpx 100rpx;
		background-color: #f8f8f8;
		background-image: linear-gradient(169.4deg,
				rgb(49, 208, 129) 0%,
				rgb(49, 208, 129) -6.14%,
				rgb(24, 181, 103) 111.55%,
				rgb(24, 181, 103) 100%);
		// overflow-y: auto;

		.order-item {
			width: 90%;
			border-bottom: 2rpx solid #eee;
			margin: 0 auto;
			padding: 20rpx 0rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.image {
			margin-left: 16rpx;
			width: 16rpx;
			height: 28rpx;
		}

		.group {
			margin-top: 50rpx;

			.group_1 {
				color: rgb(255, 255, 255);
				font-size: 40rpx;
				line-height: 48rpx;
				white-space: nowrap;

				.image_1 {
					margin: 6rpx 0;
					width: 36rpx;
					height: 36rpx;
				}

				.text {
					margin-left: 16rpx;
				}
			}

			.text_1 {
				margin-top: 12rpx;
				align-self: center;
				color: rgb(255, 255, 255);
				font-size: 24rpx;
				line-height: 34rpx;
				white-space: nowrap;
			}
		}

		.group_2 {
			margin-top: 56rpx;

			// padding-left: 28rpx;
			.steps-text {
				padding-top: 10rpx;
			}

			.group_3 {
				padding: 0 110rpx;
				color: rgb(24, 181, 103);
				font-size: 24rpx;
				line-height: 34rpx;
				white-space: nowrap;

				.left-text-wrapper {
					padding: 6rpx 0 12rpx;
					background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844615701279305.png');
					background-size: 100% 100%;
					background-repeat: no-repeat;
					height: 52rpx;

					.text_2 {
						margin: 0 12rpx;
					}

					&.select-active {
						background-image: url('../../static/teps-2.png');

					}
				}

				.view {
					margin-left: 115rpx;
				}
			}

			.view_2 {
				color: rgb(24, 181, 103);
				font-size: 24rpx;
				line-height: 26rpx;
				white-space: nowrap;
			}

			.group_6 {
				color: rgba(255, 255, 255, 0.6);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				.text-active {
					color: rgb(255, 255, 255);
				}

				.text_9 {}
			}

			.text_6 {
				color: rgba(255, 255, 255, 0.6);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;
				margin-left: 30rpx;

				&.text-active {
					color: rgb(255, 255, 255);
				}
			}

			.text_7 {
				margin-left: 66rpx;
				color: rgba(255, 255, 255, 0.6);
				font-size: 28rpx;
				line-height: 40rpx;
				white-space: nowrap;

				&.text-active {
					color: rgb(255, 255, 255);
				}
			}
		}

		.group_7 {
			margin-top: 72rpx;

			.group_8 {
				padding: 0 118rpx;

				.text-wrapper_1 {
					padding: 10rpx 0;
					color: rgb(255, 255, 255);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					border-radius: 32rpx;
					width: 200rpx;
					height: 64rpx;
					border: solid 2rpx rgb(255, 255, 255);
				}

				.text-wrapper_2 {
					margin-left: 48rpx;
					padding: 12rpx 0;
					color: rgb(24, 181, 103);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					background-image: linear-gradient(104.7deg,
							rgb(255, 255, 255) 0%,
							rgb(255, 255, 255) -10.51%,
							rgb(183, 255, 219) 114.74%,
							rgb(183, 255, 219) 100%);
					box-shadow: 4rpx 8rpx 12rpx 0px rgb(5, 139, 72);
					border-radius: 32rpx;
					width: 200rpx;
					height: 64rpx;
				}
			}

			.section_4 {
				margin-top: 48rpx;
				padding: 24rpx 22rpx 40rpx 24rpx;
				background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844615594288627.png');
				background-position: 0px 0px;
				background-size: 100% 100%;
				background-repeat: no-repeat;

				.group_14 {
					margin-top: 40rpx;

					.group_16 {
						margin-top: 16rpx;

						.text_21 {
							color: rgb(153, 153, 153);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.text_22 {
							color: rgb(102, 102, 102);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}
					}

					.text_19 {
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_20 {
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.group_17 {
					margin-top: 38rpx;
				}

				.group_30 {
					margin-top: 40rpx;

					.text_37 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.text_38 {
						margin: 24rpx 6rpx 0 4rpx;
						color: rgb(102, 102, 102);
						font-size: 24rpx;
						line-height: 34rpx;
						text-align: left;
					}
				}

				.text_39 {
					margin-top: 38rpx;
					align-self: center;
					color: rgb(153, 153, 153);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
				}

				.text-wrapper_6 {
					margin-top: 38rpx;
					padding: 6rpx 0;
					align-self: center;
					color: rgb(153, 153, 153);
					font-size: 28rpx;
					line-height: 40rpx;
					white-space: nowrap;
					border-radius: 28rpx;
					width: 176rpx;
					border: solid 2rpx rgb(228, 228, 228);
				}

				.group_10 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;

					.section_5 {
						margin: 6rpx 0;
						background-color: rgb(24, 181, 103);
						border-radius: 4rpx;
						width: 8rpx;
						height: 32rpx;
					}

					.text_12 {
						margin-left: 16rpx;
					}
				}

				.group_11 {
					margin-top: 24rpx;

					.section_6 {
						padding-top: 140rpx;
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						line-height: 34rpx;
						white-space: nowrap;
						border-radius: 12rpx;
						// background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844615922897731.png');
						background-size: 100% 100%;
						background-repeat: no-repeat;
						width: 230rpx;
						height: 196rpx;

						.text-wrapper_3 {
							padding: 10rpx 0 12rpx;
							background-color: rgba(0, 0, 0, 0.36);
							border-radius: 0px 0px 12rpx 12rpx;

							.text_13 {
								margin-left: 14rpx;
								margin-right: 12rpx;
							}
						}
					}

					.group_12 {
						margin-left: 24rpx;
						margin-right: 40rpx;
						flex: 1 1 auto;

						.text_14 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.group_13 {
							margin-top: 8rpx;
							white-space: nowrap;

							.text_15 {
								color: rgb(243, 151, 53);
								font-size: 24rpx;
								line-height: 28rpx;
							}

							.text_16 {
								color: rgb(243, 151, 53);
								font-size: 40rpx;
								line-height: 48rpx;
							}
						}

						.view_6 {
							margin-top: 20rpx;
							color: rgb(102, 102, 102);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
						}

						.text_18 {
							margin-top: 8rpx;
							color: rgb(153, 153, 153);
							font-size: 24rpx;
							line-height: 34rpx;
							white-space: nowrap;
						}
					}
				}

				.group_18 {
					padding: 40rpx 0 38rpx;

					.text_29 {
						margin-top: 8rpx;
						color: rgb(102, 102, 102);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text-wrapper_5 {
						padding: 8rpx 0;
						align-self: center;
						color: rgb(255, 255, 255);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
						background-color: rgb(24, 181, 103);
						border-radius: 28rpx;
						width: 160rpx;
						height: 56rpx;
					}

					.text_27 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.text_28 {
						margin-left: 16rpx;
						margin-top: 4rpx;
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.group_28 {
						margin-top: 16rpx;

						.text_33 {
							color: rgb(153, 153, 153);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.text_34 {
							color: rgb(102, 102, 102);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}
					}

					.group_29 {
						margin-top: 16rpx;

						.text_35 {
							color: rgb(153, 153, 153);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}

						.text_36 {
							color: rgb(102, 102, 102);
							font-size: 28rpx;
							line-height: 40rpx;
							white-space: nowrap;
						}
					}

					.image_5 {
						border-radius: 50%;
						width: 80rpx;
						height: 80rpx;
					}

					.group_20 {
						margin-left: 24rpx;

						.group_23 {
							margin-top: 6rpx;
							color: rgb(153, 153, 153);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;

							.image_7 {
								width: 80rpx;
								height: 24rpx;
							}

							.image_8 {
								margin-left: 4rpx;
								width: 52rpx;
								height: 24rpx;
							}

							.text_25 {
								margin-left: 8rpx;
							}
						}

						.text_23 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
							white-space: nowrap;
						}

						.group_22 {
							margin: 4rpx 0 4rpx 8rpx;
							padding: 2rpx 0;
							color: rgb(255, 255, 255);
							font-size: 20rpx;
							line-height: 28rpx;
							white-space: nowrap;
							width: 90rpx;
							position: relative;

							.text-wrapper_4 {
								padding: 2rpx 0;
								background-color: rgb(243, 151, 53);
								border-radius: 16rpx;
								width: 78rpx;
							}

							.image_6 {
								border-radius: 50%;
								width: 36rpx;
								height: 36rpx;
								position: absolute;
								left: 0;
								bottom: 0;
							}
						}
					}

					.text_30 {
						color: rgb(0, 0, 0);
						font-size: 32rpx;
						font-weight: 500;
						line-height: 44rpx;
						white-space: nowrap;
					}

					.group_27 {
						white-space: nowrap;
						height: 44rpx;

						.text_31 {
							color: rgb(0, 0, 0);
							font-size: 24rpx;
							font-weight: 500;
							line-height: 34rpx;
						}

						.text_32 {
							color: rgb(0, 0, 0);
							font-size: 32rpx;
							font-weight: 500;
							line-height: 44rpx;
						}
					}
				}

				.divider {
					margin-left: 4rpx;
					background-image: repeating-linear-gradient(90deg,
							rgb(228, 228, 228),
							rgb(228, 228, 228) 1.2618296529968454%,
							transparent 1.2618296529968454%,
							transparent 2.5236593059936907%);
					height: 2rpx;
				}
			}
		}

		.center-group {
			padding: 0 4rpx;

			.section_1 {
				padding: 8rpx 0;
				background-color: rgba(255, 255, 255, 0.45);
				border-radius: 50%;
				width: 48rpx;
				height: 48rpx;

				.text-wrapper {
					height: 32rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 50%;
					width: 32rpx;
				}
			}

			.section_2 {
				margin-left: 6rpx;
				align-self: center;
				background-color: rgba(255, 255, 255, 0.45);
				border-radius: 2rpx;
				width: 70rpx;
				height: 4rpx;

			}

			.section_3 {
				margin-left: 14rpx;
				align-self: center;
				background-color: rgba(255, 255, 255, 0.5);
				border-radius: 50%;
				width: 24rpx;
				height: 24rpx;

				&.select-active {
					padding: 8rpx 8rpx;
					width: 48rpx;
					height: 48rpx;
				}

				.text-wrapper {
					height: 32rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 50%;
					width: 32rpx;
				}
			}

			.view_3 {
				margin-left: 10rpx;
			}

			.image_2 {
				margin-left: 10rpx;
				align-self: center;
				width: 178rpx;
				height: 16rpx;
			}

			.image_3 {
				margin-left: 14rpx;
				align-self: center;
				width: 174rpx;
				height: 16rpx;
			}

			.image_4 {
				margin: 6rpx 0 4rpx;
				width: 24rpx;
				height: 24rpx;
			}

			.text_17 {
				margin-left: 8rpx;
			}
		}
	}

	.select-active {}


	.product-wrapper {
		margin: 30rpx 0;

		.product-item {
			padding: 16rpx 22rpx 24rpx 24rpx;
			background-color: rgb(255, 255, 255);
			border-radius: 28rpx;
			margin: 20rpx 0;

			.left-section {
				//padding-top: 140rpx;
				color: rgb(255, 255, 255);
				font-size: 24rpx;
				line-height: 34rpx;
				white-space: nowrap;
				border-radius: 12rpx;
				//background-image: url('https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844604317986283.png');
				background-size: 100% 100%;
				background-repeat: no-repeat;
				width: 216rpx;
				height: 182rpx;

				.text-wrapper {
					padding: 13rpx 0 0 0;
					position: relative;

					.text_6 {
						text-align: center;
						width: 216rpx;
						position: absolute;
						bottom: 0;
						background-color: rgba(0, 0, 0, 0.36);
						border-radius: 0px 0px 12rpx 12rpx;
						//margin: 0 6rpx;
					}
				}
			}

			.right-group {
				margin-left: 24rpx;
				flex: 1 1 auto;

				.group_7 {
					margin-top: 8rpx;

					.left-text-wrapper {
						padding: 4rpx 0;
						color: rgb(255, 255, 255);
						font-size: 20rpx;
						line-height: 28rpx;
						white-space: nowrap;
						background-color: rgb(243, 151, 53);
						border-radius: 8rpx;
						width: 104rpx;
						height: 36rpx;
						text-align: center;
					}

					.right-text-wrapper {
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
						text-align: center;
					}
				}

				.group_8 {
					margin-top: 8rpx;
					color: rgb(102, 102, 102);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;

					.image_5 {
						width: 32rpx;
						height: 32rpx;
					}

					.text_14 {
						margin-left: 4rpx;
					}
				}

				.group_9 {
					margin-top: 16rpx;
					width: 100%;
					display: flex;
					justify-content: space-around;

					.left-group {
						margin-top: 14rpx;
						white-space: nowrap;
						height: 44rpx;

						.text_16 {
							color: rgb(255, 74, 74);
							font-size: 24rpx;
							line-height: 28rpx;
						}

						.text_18 {
							font-family: Price-Bold;
							color: rgb(255, 74, 74);
							font-size: 36rpx;
							line-height: 44rpx;
						}
					}

					.center-group {
						margin-left: 4rpx;
						margin-top: 20rpx;
						position: relative;

						.group_10 {
							line-height: 28rpx;
							white-space: nowrap;

							.text_20 {
								color: rgb(153, 153, 153);
								font-size: 24rpx;
								line-height: 28rpx;
							}

							.text_22 {

								color: rgb(153, 153, 153);
								font-size: 24rpx;
								line-height: 28rpx;
							}
						}

						.divider {
							background-color: rgb(151, 151, 151);
							width: 88rpx;
							height: 2rpx;
							position: absolute;
							left: 2rpx;
							right: 0;
							top: 50%;
							transform: translateY(-50%);
						}
					}

					.right-text-wrapper_1 {


						margin-left: 40rpx;
						margin-top: 6rpx;
						padding: 4rpx 0 2rpx;
						color: rgb(255, 255, 255);
						font-size: 24rpx;
						line-height: 34rpx;

						background-image: linear-gradient(112.4deg,
								rgb(53, 219, 160) 0%,
								rgb(53, 219, 160) -13.77%,
								rgb(24, 181, 103) 111.69%,
								rgb(24, 181, 103) 100%);
						border-radius: 20rpx;
						width: 128rpx;
						height: 40rpx;
						text-align: center;
					}
				}

				.text_8 {
					color: rgb(0, 0, 0);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
				}

				.text_10 {
					margin-bottom: 10rpx;
					color: rgb(153, 153, 153);
					font-size: 24rpx;
					line-height: 34rpx;
					white-space: nowrap;
					padding-right: 30rpx;
				}
			}
		}
	}
</style>
