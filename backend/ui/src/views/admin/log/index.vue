<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" :searchData.sync="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
								<el-button type="danger" :disabled="multiple" size="mini" icon="el-icon-delete"  @click="del(ids)" v-if="checkPermission('/admin/Log/delete.html')" >删除</el-button>
								<el-button type="info" :disabled="single" size="mini" icon="el-icon-view"  @click="detail(ids)" v-if="checkPermission('/admin/Log/detail.html')" >查看详情</el-button>
								<el-button type="warning"  size="mini" icon="el-icon-download"  @click="dumpdata(ids)" v-if="checkPermission('/admin/Log/dumpdata.html')" >导出</el-button>
							</div>
							<table-tool :searchVisible.sync="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table :row-class-name="rowClass" @selection-change="selection"  @row-click="handleRowClick"  row-key="id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type="selection" width="42"></el-table-column>
							<el-table-column align="center" type = '' v-if="fieldList[0].checked" property="id" label="编号" show-overflow-tooltip width="70">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="application_name" label="应用名" show-overflow-tooltip width="100">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="username" label="用户名" show-overflow-tooltip width="100">
							</el-table-column>
							<el-table-column align="left"  v-if="fieldList[3].checked" property="url" label="请求url" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[4].checked" property="ip" label="客户端ip" show-overflow-tooltip width="100">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[5].checked" property="create_time" label="创建时间" show-overflow-tooltip width="200">
								<template slot-scope="scope">
									{{parseTime(scope.row.create_time)}}
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[6].checked" property="type" label="类型" show-overflow-tooltip width="200">
								<template slot-scope="scope">
									<el-tag type="info" v-if="scope.row.type == '1'" size="mini" effect="dark">登录日志</el-tag>
									<el-tag type="warning" v-if="scope.row.type == '2'" size="mini" effect="dark">操作日志</el-tag>
									<el-tag type="danger" v-if="scope.row.type == '3'" size="mini" effect="dark">异常日志</el-tag>
								</template>
							</el-table-column>
							<el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="80">
								<template slot-scope="scope">
									<div v-if="scope.row.id">
										<el-button size="mini" type="danger" @click="del(scope.row)"  v-if="checkPermission('/admin/Log/delete.html')"><i class="el-icon-delete" />删除</el-button>
									</div>
								</template>
							</el-table-column>
						</el-table>
						<Page :total="page_data.total" :page.sync="page_data.page" :limit.sync="page_data.limit" @pagination="index" />
					</div>
				</el-card>
			</el-col>
		</el-row>
		<!--查看详情-->
		<Detail :info="detailInfo" :show.sync="dialog.detailDialogStatus" size="small" @refesh_list="index"></Detail>
		<el-dialog title="导出进度条" :visible="dumpshow" :before-close="closedialog" width="500px">
			<el-progress :percentage="percentage"></el-progress>
		</el-dialog>
	</div>
</template>
<script>
	import {
		index,
		del,
		dumpdata,
	} from '@/api/admin/log'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Detail from '@/views/admin/log/detail.vue'
	import { confirm,param2Obj } from '@/utils/common'
	import XLSX from "xlsx"
	export default {
		name:'log',
		components: {
			Search,
			Page,
			TableTool,
			Detail,
		},
		data(){
			return {
				dialog: {
					detailDialogStatus : false,
				},
				fieldList:[
					{lable:'编号',checked:true},
					{lable:'应用名',checked:true},
					{lable:'用户名',checked:true},
					{lable:'请求url',checked:true},
					{lable:'客户端ip',checked:true},
					{lable:'创建时间',checked:true},
					{lable:'类型',checked:true},
				],
				ids: [],
				single:true,
				multiple:true,
				list: [],
				detailInfo:{},
				exceldata:[],
				dumppage:1,
				ws:{},
				dumpshow:false,
				percentage:'',
				filename:'',
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
							{type:'Input',label:'用户名',prop:'username',width:'150px'},
							{type:'Select',label:'类型',prop:'type',data:[{"key":"登录日志","val":"1"},{"key":"操作日志","val":"2"},{"key":"异常日志","val":"3"}],width:'150px'},
						]
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
			dumpdata(){
				confirm({content:'确定要操作吗'}).then(() => {
					this.dumpshow = true
					this.confirmdumpdata()
				}).catch(() => {})
			},
			confirmdumpdata(){
				dumpdata({page:this.dumppage}).then(res => {
					if(res.data.length > 0){
						if(this.dumppage == 1){
							this.exceldata.push(res.header)
						}
						res.data.forEach((item) => {
							this.exceldata.push(Object.values(item))
						})
						this.percentage = res.percentage
						this.filename = res.filename
						this.ws = XLSX.utils.aoa_to_sheet(this.exceldata)
						this.dumppage = this.dumppage + 1
						this.confirmdumpdata()
					}else{
						let wb = XLSX.utils.book_new()
						XLSX.utils.book_append_sheet(wb, this.ws, '日志管理')
						XLSX.writeFile(wb, this.filename)
						this.dumpshow = false
					}
				})
			},
			closedialog(){
				this.dumpshow = false
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