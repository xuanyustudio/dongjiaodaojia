<template>
	<view class="flex-col page">
		<view class="flex-col group">
			<image
				src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844711595881901.png"
				class="image_1" />
			<view class="flex-col group_1">
				<view class="flex-col">
					<view class="flex-row section_1">
						<text class="text_1">技师姓名</text>
						<input class="text_2" type="text" v-model.trim="realname" placeholder="请输入姓名" />
					</view>
					<view class="flex-row section_2">
						<text class="text_3">技师性别</text>
						<u-radio-group style="margin-left: 48rpx;" v-model="radiovalue1" placement="row">
							<u-radio :customStyle="{'marginBottom': '8px'}" v-for="(item, index) in radiolist1"
								:key="index" style="margin-right: 8rpx;" activeColor=" rgb(24, 181, 103)"
								:label="item.name" :name="item.name">
							</u-radio>
						</u-radio-group>

					</view>
					<view class="section_3 flex-row">
						<text class="text_6">联系方式</text>
						<input class="text_7" type="number" v-model.trim="mobile" placeholder="请输入联系方式" />

					</view>
					<view class="section_3 flex-row">
						<text class="text_8">技师年龄</text>
						<input class="text_9" type="number" v-model.trim="age" placeholder="请输入年龄" />
					</view>
					<view class="section_3 justify-between">
						<view class="flex-row">
							<text class="text_10">意向城市</text>
							<tm-pickersCity @confirm="selectedCity" btn-color="bg-gradient-deep-purple-accent"
								class="text_11">
								<text>{{ city ? city : '选择意向工作城市' }}</text>
							</tm-pickersCity>
							
						</view>
						<image 
							src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844611732717437.png"
							class="image_5" />
					</view>
					<view class="flex-col section_4">
						<text>请上传本人近期照片</text>
						<view class="flex-col group_4" @click="upload">
							<image
								src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844659951094858.png"
								class="image_6" />
							<image
								src="https://codefun-proj-user-res-1256085488.cos.ap-guangzhou.myqcloud.com/61ab188e1f655f0011bbaece/61e7d9270c0e3200129ec72c/16425844713277306992.png"
								class="image_7" />
							
							<image class="avatar" v-if="avatar" :src="avatar" mode=""></image>
						</view>
					</view>
					
					<view class="flex-col items-center button" style="text-align: center;" @click="submit">
						<text>提交</text>
					</view>
					
				</view>
				
				
			</view>
		</view>
		<!--城市选择-->
		<!-- <view class="yin" v-show="flag">
				<view class="dog">{{mssg}}</view>
		</view> -->
	</view>
</template>

<script>
	import tmPickersCity from '@/tm-vuetify/components/tm-pickersCity/tm-pickersCity.vue';
	// import { uploadImage } from '@/config/utils'
	import {
		mapState,
		mapMutations
	} from 'vuex';
	export default {
		components: {
			tmPickersCity
		},
		data() {
			return {
				radiolist1: [{
						name: '男',
						disabled: false
					},
					{
						name: '女',
						disabled: false
					}
				],
				mssg:'',
				// u-radio-group的v-model绑定的值如果设置为某个radio的name，就会被默认选中
				radiovalue1: '男',
				realname:'',
				mobile:'',
				city:'',
				age:"",
				avatar:'',
				desc:'',
				status:'',
				flag:false
			};
		},
		computed:{
			...mapMutations(['setUserInfo']),
		},
		onLoad() {
				// this.$http.post('api/user/technicianStatus',{
				// }).then(res =>{
					
				// 	console.log(res.desc,"打印的222222222222222222222")
				// 	console.log(res.status,"新接口得数据")
				// 	if(res.status == -1){
				// 		this.mssg = res.desc
				// 		// uni.showToast({
				// 		// 	icon:'none',
				// 		// 	// title:res.desc,
				// 		// })
				// 		console.log(res.desc,"等于-1的时候未申请")
				// 	}else if(res.status ==1){
				// 		this.mssg = res.desc
				// 		this.flag  = true
				// 		// uni.showToast({
				// 		// 	icon:'none',
				// 		// 	// title:res.desc
				// 		// })
				// 		console.log(res.desc,"等于1的时候审核通过")
				// 	}else if(res.status == 0){
				// 		this.mssg = res.desc
				// 		console.log(res.desc,"等于0的时候审核中")
				// 		this.flag  = true
				// 		// uni.showToast({
				// 		// 	icon:'none',
				// 		// 	// title:res.desc
				// 		// })
				// 	}
				// })
				
				
		},
		methods: {
			selectedCity(e) {
				this.city = e[0] + e[1] + e[2]
			},
			upload(){
				var _this = this;
				uni.chooseImage({
					count:1,
					success: (res) => {
						let { tempFilePaths } = res;
						let filePath = tempFilePaths[0]
						// uploadImage(filePath).then(res =>{
						// 	this.avatar = res;
						// })
						_this.uploadImg(filePath)
						console.log(tempFilePaths)
					}
				})
			},
			//上传图片
			uploadImg(e) {
				let _this = this
				let token = uni.getStorageSync('token')
				console.log('请求地址',_this.$cfg['httpUrl']+'index/uploadImage')
				uni.uploadFile({
					url: _this.$cfg['httpUrl']+'index/uploadImage',
					filePath: e,
					name: 'file',
					// header:{
					// 	token:_this.$db.get('token')
					// },
					success: (res) => {
						console.log("请求接口打的印",res)
						let _data = JSON.parse(res.data);
						// console.log(_data,"打印的接口数据")
						if(_data.code==1){
							// console.log(1,is,num)
							// 头像
							_this.avatar = _data.data
							console.log(_this.avatar,"打印的this.img")
							uni.hideLoading();
						}
					}
				});
			},
			submit(){
				if( !this.realname ){
					uni.showToast({
						icon:'none',
						title:'请输入技师姓名'
					});
					return;
				}
				if( !(/^1[3456789]\d{9}$/.test(this.mobile)) ){
					uni.showToast({
						icon:'none',
						title:'请输入正确格式的手机号'
					});
					return;
				}
				if( !this.age ){
					uni.showToast({
						icon:'none',
						title:'请输入技师年纪'
					});
					return;
				}
				if( !this.city ){
					uni.showToast({
						icon:'none',
						title:'请先选择意向工作城市'
					});
					return;
				}
				var userInfo = uni.getStorageSync('userInfo')
				// console.log(userInfo.token);
				var datas = {
					realname: this.realname,
					mobile: this.mobile,
					age: this.age,
					city: this.city,
					avatar: this.avatar,
					sex: this.radiovalue1 == '男' ? 1 : 2,
				}
				uni.request({
					url:'http://127.0.0.1:8088/public/index.php/api/user/technicianApply2',
					method:'POST',
					data:datas,
					// header:{
					// 	token:userInfo.token
					// },
					success(res) {
						// console.log(res);
						uni.showToast({
							icon:'none',
							title:res.data.msg
						})
						
						setTimeout(function(){
							uni.navigateBack()
						},1000)
					}
				})
				// return
				// this.$http.post('api/user/technicianApply',{
				// 	realname: '123',
				// 	mobile:'15236720662',
				// 	age: '12',
				// 	city: 'this.city',
				// 	avatar: 'this.avatar',
				// 	sex: 1,
					
				// }).then(res =>{
				// 	console.log(res);
				// 	uni.showToast({
				// 		icon:'none',
				// 		title:res.msg
				// 	})
				// })
			}
		}
	};
</script>

<style scoped lang="scss">
		@import '@/style/common.scss';
	
	.flex-row{
		display: flex;
	}
	
	.yin{
		width: 100%;
		height: 100%;
		position: fixed;
		background-color: rgba(0, 0, 0,0.8);
		z-index: 999;
		
	}
	.dog{
		width: 70%;
		height: 10%;
		background-color: white;
		border-radius: 8rpx;
		margin:  100rpx auto;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	
	
	
	.page {
		background-color: #f8f8f8;
		width: 100%;
		height: 100%;
		overflow-y: auto;

		.group {
			padding-bottom: 100rpx;
			flex: 1 1 auto;
			overflow-y: auto;

			.image_1 {
				width: 100vw;
				height: 53.5vw;
			}

			.group_1 {
				margin-top: -48rpx;
				padding: 0 32rpx;
				position: relative;

				.button {
					margin-top: 90rpx;
					padding: 22rpx 0;
					color: rgb(255, 255, 255);
					font-size: 32rpx;
					font-weight: 500;
					line-height: 44rpx;
					white-space: nowrap;
					background-color: rgb(24, 181, 103);
					border-radius: 44rpx;
				}

				.section_1 {
					padding: 28rpx 24rpx;
					background-color: rgb(255, 255, 255);
					border-radius: 24rpx 24rpx 0px 0px;

					.text_1 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_2 {
						margin-left: 48rpx;
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}
				}

				.section_2 {
					padding: 24rpx 24rpx 26rpx;
					color: rgb(0, 0, 0);
					font-size: 28rpx;
					font-weight: 500;
					line-height: 40rpx;
					white-space: nowrap;
					background-color: rgb(255, 255, 255);

					.text_3 {
						margin-top: 4rpx;
					}

					.image_2 {
						width: 44rpx;
						height: 44rpx;
					}

					.image_3 {
						margin-left: 48rpx;
					}

					.text_4 {
						margin-left: 4rpx;
						margin-top: 4rpx;
					}

					.image_4 {
						margin-left: 40rpx;
					}

					.text_5 {
						margin-left: 4rpx;
						margin-bottom: 4rpx;
					}
				}

				.section_3 {
					padding: 28rpx 24rpx;
					background-color: rgb(255, 255, 255);

					.text_6 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_7 {
						margin-left: 48rpx;
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_8 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_9 {
						margin-left: 48rpx;
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.image_5 {
						margin-bottom: 6rpx;
						width: 32rpx;
						height: 32rpx;
					}

					.text_10 {
						color: rgb(0, 0, 0);
						font-size: 28rpx;
						font-weight: 500;
						line-height: 40rpx;
						white-space: nowrap;
					}

					.text_11 {
						margin-left: 48rpx;
						color: rgb(153, 153, 153);
						font-size: 28rpx;
						line-height: 40rpx;
						width:450rpx;
							
					}
				}

				.section_4 {
					padding: 28rpx 23rpx 31rpx;
					color: rgb(0, 0, 0);
					font-size: 28rpx;
					font-weight: 500;
					line-height: 40rpx;
					white-space: nowrap;
					background-color: rgb(255, 255, 255);
					border-radius: 0px 0px 24rpx 24rpx;

					.group_4 {
						margin-top: 31rpx;
						padding: 71rpx 0 71rpx;
						position: relative;

						.image_6 {
							margin-left: 71rpx;
							width: 56rpx;
							height: 56rpx;
						}

						.image_7 {
							border-radius: 24rpx;
							width: 198rpx;
							height: 198rpx;
							position: absolute;
							left: 0;
							top: 0;
						}
						.avatar{
							border-radius: 24rpx;
							position: absolute;
							width: 198rpx;
							height: 198rpx;
							position: absolute;
							left: 0;
							top: 0;
							z-index: 9;
						}
					}
				}
			}
		}
	}
</style>
