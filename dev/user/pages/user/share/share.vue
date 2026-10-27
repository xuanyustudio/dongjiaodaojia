<template>
	<view>
		<div style="width: 100%;">
		<!-- 	<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/m1.jpg" width="100%" height="auto" > -->
			<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/m2.jpg" width="100%" height="auto">
			<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/m3.jpg" width="100%" height="auto">
			<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/m4.jpg" width="100%" height="auto">
			<img src="https://yiyanyuese.oss-cn-beijing.aliyuncs.com/images/H5/m5.jpg" width="100%" height="auto">
		</div>
		<div class="center"> 
			<div class="center-mobile">
				<div class="input-mobile">
					<u--input
					    placeholder="请输入您的手机号"
					    border="none"
					    v-model="value"
					    @change="change"
						inputAlign="center"
						color="#d88589"
						fontSize="20"
						type="number"
						placeholder-style="color:#d88589"
					  ></u--input>
					 
				</div>
				 <div  class="receive" @click="submit">下载享享到家APP使用</div>
			</div>
		</div>
		<div class="text-center">
			<text>
				享享到家，是一家以采耳、推拿为主项，个人定制型的o2o平台，本平台提供专业、正规、安全、便捷的上门服务。您可自选技师并查看技师的详细介绍（距离、头像、状态、评论、从业经验），项目、下单、支付。技师收到你的支付订单会立马联系你的。 本平台承诺：不满意可退款（在本平台服务范围内，因技师的手法和态度不满意的，经调查属实后，平台无理由退款）
			</text>
		</div>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				value:'',
				rules: {
					// 字段名称
					mobile: [
						{
							required: true, 
							message: '请输入手机号',
							trigger: ['change','blur'],
						},
						{
							// 自定义验证函数，见上说明
							validator: (rule, value, callback) => {
								// 上面有说，返回true表示校验通过，返回false表示不通过
								// uni.$u.test.mobile()就是返回true或者false的
								return uni.$u.test.mobile(value);
							},
							message: '手机号码不正确',
							// 触发器可以同时用blur和change
							trigger: ['change','blur'],
						}
					]
				},
				pid:''

			}
		},
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
		   console.log(option)
			if(option.pid){
				console.log(option.pid)
				this.pid = option.pid
			}
		},
		methods: {
			change(e){
				
			},
			submit(){
				if(!this.value||this.value.length!=11){
					uni.showModal({
					    title: '提示',
					    content: '手机号输入错误！',
						showCancel:false,
					    success: function (res) {
					    }
					});
				}else{
					this.$http.post('api/user/login', {
							username: this.value,
							sms_code: 888888,
							type: 2,
							pid:this.pid
						})
						.then(res => {
							uni.showToast({
								title: '注册成功',
								duration: 2000,
								success: () => {
									setTimeout(() => {
										// uni.navigateBack();
										uni.navigateTo({
											url:"download/download"
										})
									}, 2000);
								}
							});
						});
				}
				
			}
		}
	}
</script>

<style>
	page{
		background: #ffeeef;
	},
    img{
        vertical-align:bottom
    }
	.center{
		width: 100%;
		
	}
	.center-mobile{
		height: 350rpx;
		background: #FFFFFF;
		margin: 0 25rpx;
		border-radius: 30rpx;
		position: relative;
		display: flex;
		justify-content: center;
		
		line-height: 100rpx;
	}
	.input-mobile{
		background: #ffeeef;
		position: absolute;
		right: auto;
		width: 90%;
		top: 50rpx;
		border-radius: 50rpx;
		height: 100rpx;
		text-align: center;
		display: flex;	
	}
	.receive{
		position: absolute;
		right: auto;
		width: 90%;
		top: 180rpx;
		color: #FFFFFF;
		background-image: linear-gradient(to right, #fb9c53, #f86047);
		border-radius: 50rpx;
		height: 100rpx;
		text-align: center;
		display: flex;	
		justify-content: center;
	}
	.text-center{
		color: #d88589;
		margin: 40rpx 30rpx;
		font-size: 26rpx;
	}
</style>
