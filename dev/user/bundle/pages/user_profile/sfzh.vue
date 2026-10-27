<template>
	<view>
		<view class="a">
			<view class="ser">
				<view class="ss">
					<view style="font-size:26rpx;">姓名</view>
					<input type="text" v-model="realname">
				</view>
				<view class="ss">
					<view style="font-size: 26rpx;">身份证号	</view>
					<view class="s">
						<input type="text"v-model="id_card" >
					</view>
				</view>
			</view>
		</view>
		<view class="l" @click="sd()">确定</view>
	</view>
</template>

<script>
	export default {
		data(){
			return{
				realname:'',//身份证姓名
				id_card:''//身份证件号
			}
			
		},
		onLoad() {
			
		},
		methods:{
				sd(){
					
					
								          
								                if(!(/^[1-9]\d{5}(18|19|20|(3\d))\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/.test(this.id_card)) ){
												uni.showToast({
																			icon: "none",
																			title:'请输入正确格式的身份证号码',
																			duration: 2000,
																			position: 'top'
																		})
																		return
								                }
					if(this.realname == '' || this.cardsn == ''){
						uni.showToast({icon:"none",title: "输入框不能为空"})
					}else{
						// 调取接口传输数据到后端
						this.$http.post("api/user/member_real",{
							realname:this.realname,
							id_card :this.id_card
						}).then(res =>{
							uni.showToast({
								icon:'none',
								title:"提交成功"
							})
							uni.switchTab({
								url:'/pages/index/index'
							})
						})
					}
				}
			}
	}
</script>

<style scoped lang="scss">
	.l{
		background-color:#0ba27f ;
		width: 90%;
		height: 90rpx;
		border-radius: 50rpx;
		margin: 40rpx auto;
		display: flex;
		align-items: center;
		justify-content: space-evenly;
		color: white;
	}
	page{
		background-color: #f6f6f6;
	}
	.a{
		background-color: white;
		width:90%;
		height: 250rpx;
		margin: 30rpx auto;
		display: flex;
		    align-items: center;
		    justify-content: space-evenly;
	}
	.ser{
		display: flex;
		width: 95%;
		height: 85%;
		flex-direction: column;
		    justify-content: space-between;
	}
	.ss{
		width: 100%;
		height: 47%;
		border-bottom: 1px solid black;
		display: flex;
		    flex-direction: column;
		    justify-content: space-between;
		
	}
	.s{
		display: flex;
		width: 100%;
		height: 100%;
		    align-items: center;
		    justify-content: space-between;
	}
	.ssss{
		width: 140rpx;
		display: flex;
		align-items: center;
		height: 40rpx;
		border: 1px solid black;
		justify-content: space-evenly;

	}
</style>