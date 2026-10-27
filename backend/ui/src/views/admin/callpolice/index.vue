<template>
  <div>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-card shadow="never"
                 class="card">
          <div id="charts_one"
               style="width:100%;min-height:80vh">
            <div class="btn-group"
                 style="margin-bottom:11px;">
              <div>
                <el-button type="danger"
                           :disabled="multiple"
                           size="mini"
                           icon="el-icon-delete"
                           @click="del(ids)"
                           v-if="checkPermission('/admin/Callpolice/delete.html')">删除</el-button>
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
                               property="lat"
                               label="纬度"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="lng"
                               label="经度"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="address"
                               label="地址"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="create_time"
                               label="时间"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  {{parseTime(scope.row.create_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="audio"
                               label="录音文件"
                               show-overflow-tooltip
                               width="">

                <template slot-scope="scope">
                  <audio :src="scope.row.audio"
                         controls="controls"></audio>

                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[6].checked"
                               property="technician.realname"
                               label="技师姓名"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               property="technician.mobile"
                               label="联系方式"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false"
                               label="操作"
                               align="center"
                               width="80">
                <template slot-scope="scope">
                  <div v-if="scope.row.id">
                    <el-button size="mini"
                               type="danger"
                               @click="del(scope.row)"
                               v-if="checkPermission('/admin/Callpolice/delete.html')"><i class="el-icon-delete" />删除</el-button>
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
  </div>
</template>
<script>
import {
  index,
  updateExt,
  getUpdateInfo,
  del,
} from '@/api/admin/callpolice'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from '@/views/admin/callpolice/add.vue'
import Update from '@/views/admin/callpolice/update.vue'
import Detail from '@/views/admin/callpolice/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
export default {
  name: 'callpolice',
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
        { lable: '纬度', checked: true },
        { lable: '经度', checked: true },
        { lable: '地址', checked: true },
        { lable: '时间', checked: true },
        { lable: '录音文件', checked: true },
        { lable: '技师姓名', checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
      updateInfo: {},
      detailInfo: {},
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