<template>
  <div>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-card shadow="never"
                 class="card">
          <div id="charts_one"
               style="width:100%;min-height:80vh">
            <Search size='small'
                    :searchVisible="searchVisible"
                    :searchData.sync="searchData"
                    :searchForm="searchForm"
                    @refesh_list="index"></Search>
            <div class="btn-group"
                 style="margin-bottom:11px;">
              <div>
                <el-button type="primary"
                           :disabled="single"
                           size="mini"
                           icon="el-icon-edit"
                           @click="update(ids)"
                           v-if="checkPermission('/admin/User/update.html')">修改</el-button>
                <el-button type="info"
                           :disabled="single"
                           size="mini"
                           icon="el-icon-view"
                           @click="detail(ids)"
                           v-if="checkPermission('/admin/User/detail.html')">查看详情</el-button>
                <el-button type="warning"
                           size="mini"
                           icon="el-icon-download"
                           @click="dumpdata(ids)"
                           v-if="checkPermission('/admin/User/dumpdata.html')">导出</el-button>
              </div>
              <table-tool :searchVisible.sync="searchVisible"
                          :fieldList="fieldList"
                          @refesh_list="index"
                          :searchTool="true"></table-tool>
            </div>
            <el-table :row-class-name="rowClass"
                      @selection-change="selection"
                      @row-click="handleRowClick"
                      row-key="id"
                      :header-cell-style="{ background: '#eef1f6', color: '#606266' }"
                      v-loading="loading"
                      ref="multipleTable"
                      border
                      class="eltable"
                      :data="list"
                      style="width: 100%">
              <el-table-column align="center"
                               type="selection"
                               width="42"></el-table-column>
              <el-table-column align="center"
                               type=''
                               v-if="fieldList[0].checked"
                               property="id"
                               label="ID"
                               show-overflow-tooltip
                               width="70">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[1].checked"
                               property="pid"
                               label="推荐人id"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="nickname"
                               label="昵称"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="username"
                               label="用户名"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="realname"
                               label="真名"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="sex"
                               label="性别"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <el-tag type="primary"
                          v-if="scope.row.sex == '0'"
                          size="mini"
                          effect="dark">保密</el-tag>
                  <el-tag type="primary"
                          v-if="scope.row.sex == '1'"
                          size="mini"
                          effect="dark">男</el-tag>
                  <el-tag type="primary"
                          v-if="scope.row.sex == '2'"
                          size="mini"
                          effect="dark">女</el-tag>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[6].checked"
                               property="mobile"
                               label="手机号码"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[7].checked"
                               property="avatar"
                               label="头像"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <div class="demo-image__preview">
                    <el-image v-if="scope.row.avatar"
                              class="table_list_pic"
                              :src="scope.row.avatar"
                              :preview-src-list="[scope.row.avatar]"></el-image>
                  </div>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[8].checked"
                               property="status"
                               label="状态"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <el-tag type="danger"
                          v-if="scope.row.status == '0'"
                          size="mini"
                          effect="dark">禁止</el-tag>
                  <el-tag type="success"
                          v-if="scope.row.status == '1'"
                          size="mini"
                          effect="dark">正常</el-tag>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[9].checked"
                               property="is_promoter"
                               label="1推广人"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[10].checked"
                               property="balance"
                               label="余额"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[11].checked"
                               property="is_vip"
                               label="vip"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <span v-if="scope.row.is_vip == '0'">非会员</span>
                  <el-tag type="primary"
                          v-if="scope.row.is_vip == '1'"
                          size="mini"
                          effect="dark">会员</el-tag>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[12].checked"
                               property="create_time"
                               label="注册时间"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  {{parseTime(scope.row.create_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[13].checked"
                               property="praise"
                               label="好评率"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false"
                               label="操作"
                               align="center"
                               width="160">
                <template slot-scope="scope">
                  <div v-if="scope.row.id">
                    <el-button size="mini"
                               type="primary"
                               @click="update(scope.row)"
                               v-if="checkPermission('/admin/User/update.html')"><i class="el-icon-edit" />修改</el-button>
                    <!-- <el-button size="mini"
                               type="warning"
                               @click="dumpdata(scope.row)"
                               v-if="checkPermission('/admin/User/dumpdata.html')"><i class="el-icon-download" />导出</el-button> -->
                  </div>
                </template>
              </el-table-column>
            </el-table>
            <Page :total="page_data.total"
                  :page.sync="page_data.page"
                  :limit.sync="page_data.limit"
                  @pagination="index" />
          </div>
        </el-card>
      </el-col>
    </el-row>
    <!--添加-->
    <Add :show.sync="dialog.addDialogStatus"
         size="small"
         @refesh_list="index"></Add>
    <!--修改-->
    <Update :info="updateInfo"
            :show.sync="dialog.updateDialogStatus"
            size="small"
            @refesh_list="index"></Update>
    <!--查看详情-->
    <Detail :info="detailInfo"
            :show.sync="dialog.detailDialogStatus"
            size="small"
            @refesh_list="index"></Detail>
    <el-dialog title="导出进度条"
               :visible="dumpshow"
               :before-close="closedialog"
               width="500px">
      <el-progress :percentage="percentage"></el-progress>
    </el-dialog>
  </div>
</template>
<script>
import {
  index,
  updateExt,
  getUpdateInfo,
  del,
  dumpdata,
} from '@/api/admin/user'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from '@/views/admin/user/add.vue'
import Update from '@/views/admin/user/update.vue'
import Detail from '@/views/admin/user/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
import XLSX from "xlsx"
export default {
  name: 'user',
  components: {
    Search,
    Page,
    TableTool,
    Add,
    Update,
    Detail,
  },
  data () {
    return {
      dialog: {
        addDialogStatus: false,
        updateDialogStatus: false,
        detailDialogStatus: false,
      },
      fieldList: [
        { lable: 'ID', checked: true },
        { lable: '推荐人id', checked: true },
        { lable: '昵称', checked: true },
        { lable: '用户名', checked: true },
        { lable: '真名', checked: true },
        { lable: '性别', checked: true },
        { lable: '手机号码', checked: true },
        { lable: '头像', checked: true },
        { lable: '状态', checked: true },
        { lable: '1推广人', checked: false },
        { lable: '余额', checked: true },
        { lable: 'vip', checked: true },
        { lable: '注册时间', checked: true },
        { lable: '好评率', checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
      updateInfo: {},
      detailInfo: {},
      exceldata: [],
      dumppage: 1,
      ws: {},
      dumpshow: false,
      percentage: '',
      filename: '',
      loading: false,
      page_data: {
        limit: 20,
        page: 1,
        total: 20,
      },
      searchVisible: true,
      searchForm: [],
      searchData: {},
    }
  },
  methods: {
    index () {
      let param = { limit: this.page_data.limit, page: this.page_data.page }
      Object.assign(param, this.searchData, param2Obj(this.$route.fullPath))
      this.loading = true
      index(param).then(res => {
        this.list = res.data.data
        this.page_data.total = res.data.total
        this.loading = false
        if (this.page_data.page == 1) {
          this.searchForm = [
            { type: 'Input', label: '用户名', prop: 'username', width: '150px' },
            { type: 'Input', label: '手机号码', prop: 'mobile', width: '150px' },
          ]
        }
      })
    },
    updateExt (row, field) {
      if (row.id) {
        updateExt({ id: row.id, [field]: row[field] }).then(res => {
          this.$message({ message: '操作成功', type: 'success' })
        })
      }
    },
    add () {
      this.dialog.addDialogStatus = true
    },
    update (row) {
      let id = row.id ? row.id : this.ids.join(',')
      getUpdateInfo({ id: id }).then(res => {
        if (res.status == 200) {
          this.dialog.updateDialogStatus = true
          this.updateInfo = res.data
        }
      })
    },
    del (row) {
      confirm({ content: '确定要操作吗' }).then(() => {
        let ids = row.id ? row.id : this.ids.join(',')
        del({ id: ids }).then(res => {
          this.$message({ message: res.msg, type: 'success' })
          this.index()
        })
      }).catch(() => { })
    },
    detail (row) {
      this.dialog.detailDialogStatus = true
      this.detailInfo = { id: row.id ? row.id : this.ids.join(',') }
    },
    dumpdata () {
      confirm({ content: '确定要操作吗' }).then(() => {
        this.dumpshow = true
        this.confirmdumpdata()
      }).catch(() => { })
    },
    confirmdumpdata () {
      dumpdata({ page: this.dumppage }).then(res => {
        if (res.data.length > 0) {
          if (this.dumppage == 1) {
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
        } else {
          let wb = XLSX.utils.book_new()
          XLSX.utils.book_append_sheet(wb, this.ws, '用户列表')
          XLSX.writeFile(wb, this.filename)
          this.dumpshow = false
        }
      })
    },
    closedialog () {
      this.dumpshow = false
    },
    selection (selection) {
      this.ids = selection.map(item => item.id)
      this.single = selection.length != 1
      this.multiple = !selection.length
    },
    handleRowClick (row, rowIndex, event) {
      if (event.target.className !== 'el-input__inner') {
        this.$refs.multipleTable.toggleRowSelection(row)
      }
    },
    rowClass ({ row, rowIndex }) {
      for (let i = 0; i < this.ids.length; i++) {
        if (row.id === this.ids[i]) {
          return 'rowLight'
        }
      }
    },
  },
  mounted () {
    this.index()
  }
}
</script>
<style  lang="scss">
@import "@/assets/scss/common.scss";
</style>