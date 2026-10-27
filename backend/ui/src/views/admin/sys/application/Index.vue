<template>
  <div>
    <el-card shadow="never" class="card">
      <div id="charts_one" style="width:100%;min-height:500px">
          <!--按钮开始-->
          <div class="btn-group" style="margin-bottom:10px;">
            <div>
                <el-button type="success" size="mini" icon="el-icon-plus"  @click="add()">新增</el-button>
                <el-button type="primary" size="mini"  icon="el-icon-edit-outline" :disabled="single" @click="editor(ids)">修改</el-button>
                <el-button type="warning" size="mini"  icon="el-icon-delete" :disabled="single" @click="buildApplication(ids)">生成应用</el-button>  
                <el-button type="danger" size="mini"  icon="el-icon-delete" :disabled="single" @click="deleteApplication(ids)">卸载</el-button>  
            </div>
            <table-tool :searchVisible.sync="searchVisible"  :fieldList="fieldList" @refesh_list="loadlist" :searchTool="false"></table-tool>
          </div>
          <!--表格数据开始-->
						<el-table highlight-current-row @current-change="selection"  row-key="app_id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">            
            <el-table-column align="center" v-if="fieldList[0].checked" property="app_id" label="编号" width="70"></el-table-column>
            <el-table-column align="center" v-if="fieldList[1].checked" property="application_name" label="应用名"></el-table-column>
            <el-table-column align="center" v-if="fieldList[2].checked" property="app_dir" label="应用目录"></el-table-column>
            <el-table-column align="center" v-if="fieldList[3].checked" property="app_type" label="应用类型">
               <template slot-scope="scope">
                <el-tag type="primary" v-if="scope.row.app_type == '1'" size="mini" effect="dark">后台应用</el-tag>
                <el-tag type="success" v-if="scope.row.app_type == '2'" size="mini" effect="dark">api应用</el-tag>
                <el-tag type="warning" v-if="scope.row.app_type == '3'" size="mini" effect="dark">cms应用</el-tag>
                <el-tag type="info" v-if="scope.row.app_type == '4'" size="mini" effect="dark">空应用</el-tag>
              </template>
            </el-table-column>
            <el-table-column align="center" v-if="fieldList[4].checked" property="status" label="生成状态">
               <template slot-scope="scope">
                <el-tag type="primary" v-if="scope.row.status == '1'" size="mini" effect="dark">正常</el-tag>
                <el-tag type="danger" v-if="scope.row.status == '0'" size="mini" effect="dark">禁用</el-tag>
              </template>
            </el-table-column>
            <el-table-column align="center" property="domain" label="访问域名">
              <template slot-scope="scope">
                  <a :href="scope.row.domain" target="_blank">{{scope.row.domain}}</a>
              </template>
            </el-table-column>
            <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" align="center" label="操作" width="180">
              <template slot-scope="scope">
                <el-button size="mini" type="primary" @click="editor(scope.row)"><i class="el-icon-edit-outline" />修改</el-button>
                <el-button size="mini" slot="reference" @click="deleteApplication(scope.row)" type="danger"><i class="el-icon-delete" />卸载</el-button>
              </template>
            </el-table-column>
          </el-table> 
          <Page :total="page_data.total" :page.sync="page_data.page" :limit.sync="page_data.limit" @pagination="loadlist" />
      </div>
    </el-card>

    <!--添加-->
    <Add :show.sync="dialog.addDialogStatus"  size="small" @refesh_list="loadlist"></Add>
    <!--修改-->
    <Update :show.sync="dialog.updateDialogStatus" :info="info"  size="small" @refesh_list="loadlist"></Update>  
  </div>
</template>
<script>
import {applicationList,getApplicationInfo,deleteApplication,buildApplication} from '../sys'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from './Add.vue'
import Update from './Update.vue'
import { confirm } from '@/utils/common'
export default {
  components: { Search,Page,Add,Update,TableTool},
  data() {
    return {
      dialog: {
        addDialogStatus : false,
        updateDialogStatus : false,
      },
      fieldList:[
        {lable:'编号',checked:true},
        {lable:'应用名',checked:true},
        {lable:'应用目录',checked:true},
        {lable:'应用类型',checked:true},
        {lable:'状态',checked:true},
      ],
      ids: {},
      single:true,
      multiple: true,
      list: [],
      info:{},
      loading: false,
      searchVisible:true,
      page_data: {
        limit: 10,
        page: 1,
        total: 50,
      },
    };
  },
  methods: {
    add() {
       this.dialog.addDialogStatus = true
    },
    editor(row) {
      let id = row.app_id ? row.app_id : this.ids.join(',')
      getApplicationInfo({app_id:id}).then(res => {
        if(res.status == 200){
            this.dialog.updateDialogStatus = true;
            this.info = res.data
        }else{
            this.$message.error('获取信息失败!')
        }
      })
    },
    buildApplication(row) {
      confirm({content:'确定生成应用吗'}).then(() => {
        let id = row.app_id ? row.app_id : this.ids.join(',')
        buildApplication({app_id:id}).then(res => {
          if(res.status == 200){
              this.$message({message: '生成成功', type: 'success'})
          }
        })
      })
    },
    deleteApplication(row) {
        confirm({content:'确定要卸载应用吗'}).then(() => {
           let id = row.app_id ? row.app_id : this.ids.join(',')
            if(id == 1){
              this.$message.error('该应用禁止卸载!')
            }
            deleteApplication({app_id:id}).then(res => {
              if(res.status == 200){
                  this.$message({message: '操作成功', type: 'success'})
                  this.loadlist()
              }else{
                  this.$message.error('操作失败!')
              }
            });
        }).catch(() => {});
    },
    loadlist(val) { 
      let param = {limit:this.page_data.limit,page:this.page_data.page}
      if(val) param = Object.assign(param, val)
      this.loading = true
      applicationList(param).then(res => {
        if(res.status == 200){
            this.list = res.data.data
            this.page_data.total = res.data.total
            this.loading = false
        }
      });
    },
    selection(selection) {
      this.ids = {app_id:selection.app_id}
      this.single = false
    },

  },
  mounted() {
    this.loadlist();
  },
};
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>