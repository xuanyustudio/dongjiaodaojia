<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" :searchData.sync="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
							</div>
							<table-tool :searchVisible.sync="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table :row-class-name="rowClass" @selection-change="selection"  @row-click="handleRowClick"  row-key="id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type="selection" width="42"></el-table-column>
							<el-table-column align="center"  v-if="fieldList[0].checked" property="order_sn" label="订单号" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="user_id" label="用户id" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="pay_money" label="支付金额" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[3].checked" property="pay_status" label="支付状态" show-overflow-tooltip width="">
								<template slot-scope="scope">
									<el-tag type="warning" v-if="scope.row.pay_status == '0'" size="mini" effect="dark">待支付</el-tag>
									<el-tag type="primary" v-if="scope.row.pay_status == '1'" size="mini" effect="dark">支付</el-tag>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[4].checked" property="member_name" label="会员名称" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[5].checked" property="month" label="开通时间长（月）" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[6].checked" property="create_time" label="开通时间" show-overflow-tooltip width="">
								<template slot-scope="scope">
									{{parseTime(scope.row.create_time)}}
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[7].checked" property="payment" label="支付方式" show-overflow-tooltip width="">
								<template slot-scope="scope">
									<span v-if="scope.row.payment == '1'" >余额</span>
									<span v-if="scope.row.payment == '2'" >微信</span>
									<span v-if="scope.row.payment == '3'" >支付宝</span>
								</template>
							</el-table-column>
						</el-table>
						<Page :total="page_data.total" :page.sync="page_data.page" :limit.sync="page_data.limit" @pagination="index" />
					</div>
				</el-card>
			</el-col>
		</el-row>
		<!--添加-->
		<Add  :show.sync="dialog.addDialogStatus" size="small" @refesh_list="index"></Add>
		<!--修改-->
		<Update :info="updateInfo" :show.sync="dialog.updateDialogStatus" size="small" @refesh_list="index"></Update>
		<!--查看详情-->
		<Detail :info="detailInfo" :show.sync="dialog.detailDialogStatus" size="small" @refesh_list="index"></Detail>
	</div>
</template>
<script>
	import {
		index,
		updateExt,
		getUpdateInfo,
		del,
	} from '@/api/admin/memberorder'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Add from '@/views/admin/memberorder/add.vue'
	import Update from '@/views/admin/memberorder/update.vue'
	import Detail from '@/views/admin/memberorder/detail.vue'
	import { confirm,param2Obj } from '@/utils/common'
	export default {
		name:'memberorder',
		components: {
			Search,
			Page,
			TableTool,
			Add,
			Update,
			Detail,
		},
		data(){
			return {
				dialog: {
					addDialogStatus : false,
					updateDialogStatus : false,
					detailDialogStatus : false,
				},
				fieldList:[
					{lable:'订单号',checked:true},
					{lable:'用户id',checked:true},
					{lable:'支付金额',checked:true},
					{lable:'支付状态',checked:true},
					{lable:'会员名称',checked:true},
					{lable:'开通时间长（月）',checked:true},
					{lable:'开通时间',checked:true},
					{lable:'支付方式',checked:true},
				],
				ids: [],
				single:true,
				multiple:true,
				list: [],
				updateInfo:{},
				detailInfo:{},
				loading: false,
				page_data: {
					limit: 20,
					page: 1,
					total:20,
				},
				searchVisible:true,
				searchForm:[],
				searchData:{},
			}
		},
		methods: {
			index(){
				let param = {limit:this.page_data.limit,page:this.page_data.page}
				Object.assign(param, this.searchData,param2Obj(this.$route.fullPath))
				this.loading = true
				index(param).then(res => {
					this.list = res.data.data
					this.page_data.total = res.data.total
					this.loading = false
					if(this.page_data.page == 1){
						this.searchForm = [
							{type:'Input',label:'用户id',prop:'user_id',width:'150px'},
							{type:'Select',label:'支付方式',prop:'payment',data:[{"val":"1","key":"余额"},{"val":"2","key":"微信"},{"val":"3","key":"支付宝"}],width:'150px'},
						]
					}
				})
			},
			updateExt(row,field){
				if(row.id){
					updateExt({id:row.id,[field]:row[field]}).then(res => {
						this.$message({message: '操作成功', type: 'success'})
					})
				}
			},
			add(){
				this.dialog.addDialogStatus = true
			},
			update(row){
				let id = row.id ? row.id : this.ids.join(',')
				getUpdateInfo({id:id}).then(res => {
					if(res.status == 200){
						this.dialog.updateDialogStatus = true
						this.updateInfo = res.data
					}
				})
			},
			del(row){
				confirm({content:'确定要操作吗'}).then(() => {
					let ids = row.id ? row.id : this.ids.join(',')
					del({id:ids}).then(res => {
						this.$message({message: res.msg, type: 'success'})
						this.index()
					})
				}).catch(() => {})
			},
			detail(row){
				this.dialog.detailDialogStatus = true
				this.detailInfo = {id:row.id ? row.id : this.ids.join(',')}
			},
			selection(selection) {
				this.ids = selection.map(item => item.id)
				this.single = selection.length != 1
				this.multiple = !selection.length
			},
			handleRowClick(row, rowIndex,event){
				if(event.target.className !== 'el-input__inner'){
					this.$refs.multipleTable.toggleRowSelection(row)
				}
			},
			rowClass ({ row, rowIndex }) {
				for(let i=0;i<this.ids.length;i++) {
					if (row.id === this.ids[i]) {
						return 'rowLight'
					}
				}
			},
		},
		mounted(){
			this.index()
		}
	}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>