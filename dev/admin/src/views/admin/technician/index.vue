<template>
  <div>
    <div style="margin-bottom: 11px">
      <el-row :gutter="12">
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box">
                <div class="card-left">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-s-goods"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num1 }}</div>
                <div class="text">认证技师数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box" style="background: #fff3e0">
                <div class="card-left" style="background: #ffab2b">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-s-finance"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num2 }}</div>
                <div class="text">待认证技师</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box" style="background: #eaf9e1">
                <div class="card-left" style="background: #6dd230">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-document-checked"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num3 }}</div>
                <div class="text">工作中</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box" style="background: #ffeaf4">
                <div class="card-left" style="background: #ff85c0">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-document-delete"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num4 }}</div>
                <div class="text">未工作</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-card shadow="never" class="card">
          <div id="charts_one" style="width: 100%; min-height: 80vh">
            <div style="margin-bottom: 11px">
              <label class="el-form-item__label">认证状态：</label>
              <el-radio-group v-model="radio1" size="small" @change="orderType">
                <el-radio-button label="1">认证通过</el-radio-button>
                <el-radio-button label="0">未认证</el-radio-button>
                <el-radio-button label="8">全部</el-radio-button>
              </el-radio-group>
            </div>
            <div style="margin-bottom: 11px">
              <label class="el-form-item__label">认证状态：</label>
              <el-radio-group v-model="radio2" size="small" @change="workType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="1">工作中</el-radio-button>
                <el-radio-button label="0">未工作</el-radio-button>
                <el-radio-button label="3">禁用</el-radio-button>
              </el-radio-group>
            </div>
            <Search
              size="small"
              :searchVisible="searchVisible"
              v-model:searchData="searchData"
              :searchForm="searchForm"
              @refesh_list="index"
            ></Search>
            <div class="btn-group" style="margin-bottom: 11px">
              <div>
                <!-- <el-button type="primary"
                           :disabled="single"
                           size="small"
                          
                           @click="update(ids)"
                           v-if="checkPermission('/admin/Technician/update.html')"><i class="el-icon-edit"></i>修改</el-button> -->
                <el-button
                  type="info"
                  :disabled="single"
                  size="small"
                 
                  @click="detail(ids)"
                  v-if="checkPermission('/admin/Technician/detail.html')"
                  ><i class="el-icon-view"></i>查看详情</el-button
                >
              </div>
              <table-tool
                v-model:searchVisible="searchVisible"
                :fieldList="fieldList"
                @refesh_list="index"
                :searchTool="true"
              ></table-tool>
            </div>
            <el-table
              :row-class-name="rowClass"
              @selection-change="selection"
              @row-click="handleRowClick"
              row-key="id"
              :header-cell-style="{ background: '#eef1f6', color: '#606266' }"
              v-loading="loading"
              ref="multipleTable"
              border
              class="eltable"
              :data="list"
              style="width: 100%"
            >
              <el-table-column
                align="center"
                type="selection"
                width="42"
              ></el-table-column>
              <el-table-column
                align="center"
                type=""
                v-if="fieldList[0].checked"
                property="id"
                label="ID"
                show-overflow-tooltip
                width="70"
              >
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[1].checked"
                property="nickname"
                label="昵称"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <!-- <el-table-column align="center"  v-if="fieldList[2].checked" property="username" label="用户名" show-overflow-tooltip width="">
							</el-table-column> -->
              <el-table-column
                align="center"
                v-if="fieldList[3].checked"
                property="realname"
                label="姓名"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <!-- <el-table-column align="center"  v-if="fieldList[4].checked" property="start_time" label="服务起始时间" show-overflow-tooltip width="">
								<template slot-scope="scope">
									{{parseTime(scope.row.start_time)}}
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[5].checked" property="end_time" label="服务结束时间" show-overflow-tooltip width="">
								<template slot-scope="scope">
									{{parseTime(scope.row.end_time)}}
								</template>
							</el-table-column> -->
              <el-table-column
                align="center"
                v-if="fieldList[6].checked"
                property="sex"
                label="性别"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-tag
                    type="primary"
                    v-if="scope.row.sex == '1'"
                    size="small"
                    effect="dark"
                    >男</el-tag
                  >
                  <el-tag
                    type="warning"
                    v-if="scope.row.sex == '2'"
                    size="small"
                    effect="dark"
                    >女</el-tag
                  >
                  <el-tag
                    type="info"
                    v-if="scope.row.sex == '0'"
                    size="small"
                    effect="dark"
                    >保密</el-tag
                  >
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[7].checked"
                property="mobile"
                label="手机号码"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[8].checked"
                property="avatar"
                label="头像"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <div class="demo-image__preview">
                    <el-image
                      v-if="scope.row.avatar"
                      class="table_list_pic"
                      :src="scope.row.avatar"
                      :preview-src-list="[scope.row.avatar]"
                    ></el-image>
                  </div>
                </template>
              </el-table-column>
              <!-- <el-table-column align="center"
                               v-if="fieldList[9].checked"
                               property="album"
                               label="相册"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <div v-if="scope.row.album && scope.row.album.indexOf('[{') != -1"
                       class="demo-image__preview">
                    <el-image style="margin-right:5px"
                              v-for="(item,i) in JSON.parse(scope.row.album)"
                              class="table_list_pic"
                              :src="item.url"
                              :key="i"
                              :preview-src-list="[item.url]"></el-image>
                  </div>
                </template>
              </el-table-column> -->
              <el-table-column
                align="center"
                v-if="fieldList[10].checked"
                property="status"
                label="状态"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-tag
                    type="primary"
                    v-if="scope.row.status == '1'"
                    size="small"
                    effect="dark"
                    >认证</el-tag
                  >
                  <el-tag
                    type="danger"
                    v-if="scope.row.status == '0'"
                    size="small"
                    effect="dark"
                    >未认证</el-tag
                  >
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[11].checked"
                property="balance"
                label="余额"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[12].checked"
                property="praise"
                label="好评"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[13].checked"
                property="credit"
                label="信用"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[14].checked"
                property="composite"
                label="综合评分"
                show-overflow-tooltip
                width=""
              >
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[15].checked"
                property="work_status"
                label="工作状态"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-tag
                    type="primary"
                    v-if="scope.row.work_status == '1'"
                    size="small"
                    effect="dark"
                    >工作</el-tag
                  >
                  <el-tag
                    type="info"
                    v-if="scope.row.work_status == '0'"
                    size="small"
                    effect="dark"
                    >休息</el-tag
                  >
                  <el-tag
                    type="warning"
                    v-if="scope.row.work_status == '2'"
                    size="small"
                    effect="dark"
                    >禁用</el-tag
                  >
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[16].checked"
                property="hframe"
                label="边框"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-button
                    type="primary"
                    v-if="scope.row.hframe == '1'"
                    size="small"
                    @click="changeHframe(scope.row)"
                    effect="dark"
                    >有</el-button
                  >
                  <el-button
                    type="info"
                    v-if="scope.row.hframe == '0'"
                    size="small"
                    @click="changeHframe(scope.row)"
                    effect="dark"
                    >无</el-button
                  >
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[17].checked"
                property="hrocket"
                label="火箭"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-button
                    type="primary"
                    v-if="scope.row.hrocket == '1'"
                    size="small"
                    @click="changeHrocket(scope.row)"
                    effect="dark"
                    >有</el-button
                  >
                  <el-button
                    type="info"
                    v-if="scope.row.hrocket == '0'"
                    size="small"
                    @click="changeHrocket(scope.row)"
                    effect="dark"
                    >无</el-button
                  >
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[18].checked"
                property="hhot"
                label="热门"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-button
                    type="primary"
                    v-if="scope.row.hhot == '1'"
                    size="small"
                    @click="changeHhot(scope.row)"
                    effect="dark"
                    >是</el-button
                  >
                  <el-button
                    type="info"
                    v-if="scope.row.hhot == '0'"
                    size="small"
                    @click="changeHhot(scope.row)"
                    effect="dark"
                    >否</el-button
                  >
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[19].checked"
                property="fquantity"
                label="模拟单量"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-button
                    size="small"
                    effect="dark"
                    @click="handleOpenDialog(scope.row)"
                    v-text="scope.row.fquantity"
                  ></el-button>
                </template>
              </el-table-column>
              <el-table-column
                align="center"
                v-if="fieldList[20].checked"
                property="tgrade"
                label="技师等级"
                show-overflow-tooltip
                width=""
              >
                <template #default="scope">
                  <el-select
                    v-model="scope.row.tgrade"
                    size="small"
                    effect="dark"
                    @change="changeTgrade(scope.row)"
                  >
                    <el-option
                      v-for="index of tgradecount"
                      :label="'等级' + index"
                      :value="index"
                      :key="index"
                    ></el-option>
                  </el-select>
                </template>
              </el-table-column>
              <el-table-column
                :fixed="$store.getters.device !== 'mobile' ? 'right' : false"
                label="操作"
                align="center"
                width="160"
              >
                <template #default="scope">
                  <div v-if="scope.row.id">
                    <!-- <el-button size="small"
                               type="primary"
                               @click="update(scope.row)"
                               v-if="checkPermission('/admin/Technician/update.html')"><i class="el-icon-edit" />修改</el-button> -->
                    <el-button
                      size="small"
                      type="info"
                      @click="detail(scope.row)"
                      v-if="checkPermission('/admin/Technician/detail.html')"
                      ><i class="el-icon-view" />查看详情</el-button
                    >
                  </div>
                </template>
              </el-table-column>
            </el-table>
            <Page
              :total="page_data.total"
              v-model:page="page_data.page"
              v-model:limit="page_data.limit"
              @pagination="index"
            />
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!--添加-->
    <Add
      v-model:show="dialog.addDialogStatus"
      size="small"
      @refesh_list="index"
    ></Add>
    <!--修改-->
    <Update
      :info="updateInfo"
      v-model:show="dialog.updateDialogStatus"
      size="small"
      @refesh_list="index"
    ></Update>
    <!--查看详情-->
    <Detail
      :info="detailInfo"
      v-model:show="dialog.detailDialogStatus"
      size="small"
      @closeForm="closeForm"
      @refesh_list="index"
    ></Detail>

    <!--自定义弹窗-->
    <el-dialog
      title="更改模拟单量"
      center
      v-model="outVisible"
      :before-close="outClose"
      append-to-body
    >
      <el-form :model="formData">
        <el-form-item label="模拟单量" prop="fquantity">
          <el-input v-model="formData.fquantity" size="small"></el-input>
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="changeFquantity()" type="primary">更新</el-button>
          <el-button @click="outVisible = false">返回</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import {
  index,
  update,
  updateExt,
  updateFquantity,
  getUpdateInfo,
  del,
  count,
} from "@/api/admin/technician";
import Search from "@/components/common/Search.vue";
import Page from "@/components/common/Page.vue";
import TableTool from "@/components/common/TableTool.vue";
import Add from "@/views/admin/technician/add.vue";
import Update from "@/views/admin/technician/update.vue";
import Detail from "@/views/admin/technician/detail.vue";
import { confirm, param2Obj } from "@/utils/common";
export default {
  name: "technician",
  components: {
    Search,
    Page,
    TableTool,
    Add,
    Update,
    Detail,
  },
  data() {
    return {
      outVisible: false,
      tgradecount: "",
      formData: { fquantity: "" },
      dialog: {
        addDialogStatus: false,
        updateDialogStatus: false,
        detailDialogStatus: false,
      },
      fieldList: [
        { lable: "ID", checked: true },
        { lable: "昵称", checked: true },
        { lable: "用户名", checked: true },
        { lable: "姓名", checked: true },
        { lable: "服务起始时间", checked: true },
        { lable: "服务结束时间", checked: true },
        { lable: "性别", checked: true },
        { lable: "手机号码", checked: true },
        { lable: "头像", checked: true },
        { lable: "相册", checked: true },
        { lable: "状态", checked: true },
        { lable: "余额", checked: true },
        { lable: "好评", checked: true },
        { lable: "信用", checked: true },
        { lable: "综合评分", checked: true },
        { lable: "工作状态", checked: true },
        { lable: "边框", checked: true },
        { lable: "火箭", checked: true },
        { lable: "热门", checked: true },
        { lable: "模拟单量", checked: true },
        { lable: "技师等级", checked: true },
      ],
      radio1: 1,
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
      searchData: {
        status: 1,
      },
      radio2: 8,
      count: {
        num1: 0,
        num2: 0,
        num3: 0,
        num4: 0,
      },
    };
  },
  methods: {
    //改变技师等级
    changeTgrade(row) {
      update(row).then((res) => {
        this.$message({ message: "操作成功", type: "success" });
      });
    },
    handleOpenDialog(row) {
      this.formData = row;
      this.outVisible = true;
    },
    changeFquantity() {
      updateFquantity(this.formData).then((res) => {
        this.outVisible = false;
        this.$message({ message: "操作成功", type: "success" });
      });
    },
    changeHframe(row) {
      if (row.hframe == "1") row.hframe = 0;
      else row.hframe = 1;
      update(row).then((res) => {
        this.$message({ message: "操作成功", type: "success" });
      });
    },
    changeHrocket(row) {
      if (row.hrocket == "1") row.hrocket = 0;
      else row.hrocket = 1;
      update(row).then((res) => {
        this.$message({ message: "操作成功", type: "success" });
      });
    },
    changeHhot(row) {
      if (row.hhot == "1") row.hhot = 0;
      else row.hhot = 1;
      update(row).then((res) => {
        this.$message({ message: "操作成功", type: "success" });
      });
    },
    orderType(e) {
      if (e != 8) {
        this.searchData.status = e;
        console.log(this.searchData);
      } else {
        delete this.searchData.status;
      }
      this.index();
    },
    workType(e) {
      if (e != 8) {
        this.searchData.work_status = e;
        console.log(this.searchData);
      } else {
        delete this.searchData.work_status;
      }
      this.index();
    },
    index() {
      let param = { limit: this.page_data.limit, page: this.page_data.page };
      Object.assign(param, this.searchData, param2Obj(this.$route.fullPath));
      this.loading = true;
      index(param).then((res) => {
        this.list = res.data.data;
        this.page_data.total = res.data.total;
        this.tgradecount = res.tgradecount;
        this.loading = false;
        if (this.page_data.page == 1) {
          this.searchForm = [
            { type: "Input", label: "姓名", prop: "realname", width: "150px" },
            {
              type: "Input",
              label: "手机号码",
              prop: "mobile",
              width: "150px",
            },

            // { type: 'Select', label: '工作状态', prop: 'work_status', data: [{ "key": "工作", "val": "1" }, { "key": "休息", "val": "0" }, { "key": "禁用", "val": "2" }], width: '150px' },
          ];
        }
      });
      count().then((res) => {
        this.count = res.data;
      });
    },
    updateExt(row, field) {
      if (row.id) {
        updateExt({ id: row.id, [field]: row[field] }).then((res) => {
          this.$message({ message: "操作成功", type: "success" });
        });
      }
    },
    add() {
      this.dialog.addDialogStatus = true;
    },
    update(row) {
      let id = row.id ? row.id : this.ids.join(",");
      getUpdateInfo({ id: id }).then((res) => {
        if (res.status == 200) {
          this.dialog.updateDialogStatus = true;
          this.updateInfo = res.data;
        }
      });
    },
    del(row) {
      confirm({ content: "确定要操作吗" })
        .then(() => {
          let ids = row.id ? row.id : this.ids.join(",");
          del({ id: ids }).then((res) => {
            this.$message({ message: res.msg, type: "success" });
            this.index();
          });
        })
        .catch(() => {});
    },
    detail(row) {
      this.dialog.detailDialogStatus = true;
      this.detailInfo = { id: row.id ? row.id : this.ids.join(",") };
    },
    selection(selection) {
      this.ids = selection.map((item) => item.id);
      this.single = selection.length != 1;
      this.multiple = !selection.length;
    },
    handleRowClick(row, rowIndex, event) {
      if (event.target.className !== "el-input__inner") {
        this.$refs.multipleTable.toggleRowSelection(row);
      }
    },
    rowClass({ row, rowIndex }) {
      for (let i = 0; i < this.ids.length; i++) {
        if (row.id === this.ids[i]) {
          return "rowLight";
        }
      }
    },
    closeForm() {
      this.index();
      console.log("000");
    },
  },
  mounted() {
    this.index();
  },
};
</script>
<style  lang="scss">
@import "@/assets/scss/common.scss";
.card-wrpper {
  display: flex;
  .card-box {
    width: 80px;
    height: 80px;
    background-color: #e4ecff;
    border-radius: 50%;
    margin-right: 20px;
    .card-left {
      margin: auto;
      margin-top: 10px;
      width: 60px;
      height: 60px;
      background-color: #4d7cfe;
      border-radius: 50%;
    }
  }
  .card-right {
    margin: auto 0;
    .num {
      font-size: 20px;
      font-weight: 400;
    }
    .text {
      font-size: 14px;
      color: #98a9bc;
      padding-top: 10px;
    }
  }
}
.table-head {
  background: #f0faff;
  padding: 10px 10px;
  text-align: center;
  color: #797979;
}
.table-list {
  .item-head {
    margin-top: 12px;
    background: #f5f5f5;
    padding: 10px 10px;
    display: flex;
    color: #797979;
  }
}
.flex {
  display: flex;
  justify-content: center;
  align-items: center;
}
.item-center {
  height: 100px;
  color: #797979;
  border: #f5f5f5 1px solid;
}
.grid-content {
  border: #f5f5f5 1px solid;
  // line-height: 100px;
  align-items: center;
}
.center {
  display: flex;
  align-items: center;
  height: 100px;
  justify-content: center;
}
</style>