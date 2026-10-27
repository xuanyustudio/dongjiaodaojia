<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" v-model:searchData="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
								<el-button type="primary" :disabled="multiple" size="small"  @click="update(ids)" v-if="checkPermission('/admin/Technicianapply/update.html')" ><i class="el-icon-edit"></i>处理</el-button>
								<el-button type="danger" :disabled="multiple" size="small"  @click="del(ids)" v-if="checkPermission('/admin/Technicianapply/delete.html')" ><i class="el-icon-delete"></i>删除</el-button>
							</div>
							<table-tool v-model:searchVisible="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table :row-class-name="rowClass" @selection-change="selection"  @row-click="handleRowClick"  row-key="id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type="selection" width="42"></el-table-column>
							<el-table-column align="center" type = '' v-if="fieldList[0].checked" property="id" label="ID" show-overflow-tooltip width="70">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="realname" label="姓名" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="sex" label="性别" show-overflow-tooltip width="">
								<template #default="scope">
									<el-tag type="primary" v-if="scope.row.sex == '1'" size="small" effect="dark">男</el-tag>
									<el-tag type="warning" v-if="scope.row.sex == '2'" size="small" effect="dark">女</el-tag>
									<el-tag type="info" v-if="scope.row.sex == '0'" size="small" effect="dark">保密</el-tag>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[3].checked" property="mobile" label="手机号码" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[4].checked" property="age" label="年龄" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[5].checked" property="avatar" label="头像" show-overflow-tooltip width="">
								<template #default="scope">
									<div class="demo-image__preview">
										<el-image v-if="scope.row.avatar" class="table_list_pic" :src="scope.row.avatar"  :preview-src-list="[scope.row.avatar]"></el-image>
									</div>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[6].checked" property="create_time" label="注册时间" show-overflow-tooltip width="">
								<template #default="scope">
									{{parseTime(scope.row.create_time)}}
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[7].checked" property="city" label="区域" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[8].checked" property="user_id" label="用户ID" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[9].checked" property="status" label="状态" show-overflow-tooltip width="">
								<template #default="scope">
									<el-tag type="primary" v-if="scope.row.status == '1'" size="small" effect="dark">已入驻</el-tag>
									<el-tag type="info" v-if="scope.row.status == '0'" size="small" effect="dark">申请中</el-tag>
								</template>
							</el-table-column>
							<el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="160">
								<template #default="scope">
									<div v-if="scope.row.id">
										<el-button size="small" type="primary" @click="update(scope.row)"  v-if="checkPermission('/admin/Technicianapply/update.html')"><i class="el-icon-edit" />处理</el-button>
										<el-button size="small" type="danger" @click="del(scope.row)"  v-if="checkPermission('/admin/Technicianapply/delete.html')"><i class="el-icon-delete" />删除</el-button>
									</div>
								</template>
							</el-table-column>
						</el-table>
						<Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="index" />
					</div>
				</el-card>
			</el-col>
		</el-row>
		<!--添加-->
		<Add  v-model:show="dialog.addDialogStatus" size="small" @refesh_list="index"></Add>
		<!--查看详情-->
		<Detail :info="detailInfo" v-model:show="dialog.detailDialogStatus" size="small" @refesh_list="index"></Detail>
	</div>
</template>
<script>
	import {
		index,
		updateExt,
		update,
		del,
	} from '@/api/admin/technicianapply'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Add from '@/views/admin/technicianapply/add.vue'
	import Detail from '@/views/admin/technicianapply/detail.vue'
	import { confirm,param2Obj } from '@/utils/common'
	export default {
		name:'technicianapply',
		components: {
			Search,
			Page,
			TableTool,
			Add,
			Detail,
		},
		data(){
			return {
				dialog: {
					addDialogStatus : false,
					detailDialogStatus : false,
				},
				fieldList:[
					{lable:'ID',checked:true},
					{lable:'姓名',checked:true},
					{lable:'性别',checked:true},
					{lable:'手机号码',checked:true},
					{lable:'年龄',checked:true},
					{lable:'头像',checked:true},
					{lable:'注册时间',checked:true},
					{lable:'区域',checked:true},
					{lable:'用户ID',checked:true},
					{lable:'状态',checked:true},
				],
				ids: [],
				single:true,
				multiple:true,
				list: [],
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
							{type:'Input',label:'姓名',prop:'realname',width:'150px'},
							{type:'Input',label:'手机号码',prop:'mobile',width:'150px'},
							{type:'Select',label:'状态',prop:'status',data:[{"key":"已入驻","val":"1"},{"key":"申请中","val":"0"}],width:'150px'},
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
				confirm({content:'确定要操作吗'}).then(() => {
					let ids = row.id ? row.id : this.ids.join(',')
					update({id:ids}).then(res => {
						this.$message({message: res.msg, type: 'success'})
						this.index()
					})
				}).catch(() => {})
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