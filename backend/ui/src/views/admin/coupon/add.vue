<template>
  <div>
    <el-dialog title="添加"
               width="600px"
               class="icon-dialog"
               :visible.sync="show"
               @open="open"
               :before-close="closeForm"
               append-to-body>
      <el-form :size="size"
               ref="form"
               :model="form"
               :rules="rules"
               :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
        <el-row v-if="form.primary_id!=0">
          <el-col :span="24">
            <el-form-item label="原优惠券id"
                          prop="primary_id">
              <el-input v-model="form.primary_id"
                        autoComplete="off"
                        clearable
                        placeholder="请输入原优惠券id"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="优惠券名"
                          prop="name">
              <el-input v-model="form.name"
                        autoComplete="off"
                        clearable
                        placeholder="请输入优惠券名称"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="类型"
                          prop="type">
              <el-radio-group v-model="form.type">
                <el-radio :label="0">满减券</el-radio>
                <el-radio :label="1">折扣券</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row v-if="form.type==0">
          <el-col :span="24">
            <el-form-item label="优惠金额"
                          prop="money">

              <el-input controls-position="right"
                        style="width:200px;"
                        autoComplete="off"
                        v-model="form.money"
                        clearable
                        placeholder="优惠金额"><template slot="append">元</template></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row v-if="form.type==1">
          <el-col :span="24">
            <el-form-item label="折扣"
                          prop="discount">
              <el-input v-model="form.discount"
                        autoComplete="off"
                        style="width:200px;"
                        clearable
                        show-word-limit
                        placeholder="请输入折扣"><template slot="append">折</template></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="使用门槛"
                          prop="condition">
              <el-radio-group v-model="form.conditiontype">
                <el-radio :label="0">无门槛</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label=""
                          prop="condition">
              <el-radio-group v-model="form.conditiontype">
                <el-radio :label="1">订单满</el-radio>
              </el-radio-group>
              <el-input v-model="form.condition"
                        autoComplete="off"
                        clearable
                        style="width:280px;"
                        placeholder="请输入使用门槛"><template slot="append">元可用</template></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="可用项目"
                          prop="service_id">
              <el-autocomplete v-model="state"
                               :fetch-suggestions="querySearchAsync"
                               placeholder="请输入内容"
                               @select="handleSelect"></el-autocomplete>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="用券时间"
                          prop="start_time">
              <el-date-picker value-format="yyyy-MM-dd HH:mm:ss"
                              type="datetime"
                              v-model="form.start_time"
                              clearable
                              placeholder="请输入用券开始时间"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="截至时间"
                          prop="end_time">
              <el-date-picker value-format="yyyy-MM-dd HH:mm:ss"
                              type="datetime"
                              v-model="form.end_time"
                              clearable
                              placeholder="请输入截至时间"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer"
           class="dialog-footer">
        <el-button :size="size"
                   :loading="loading"
                   type="primary"
                   @click="submit">
          <span v-if="!loading">确 定</span>
          <span v-else>提 交 中...</span>
        </el-button>
        <el-button :size="size"
                   @click="closeForm">取 消</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { add, getService } from '@/api/admin/coupon'
export default {
  name: 'couponadd',
  components: {
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    size: {
      type: String,
      default: 'small'
    },
  },
  data () {
    return {
      form: {
        primary_id: '',
        name: '',
        type: 0,
        discount: '',
        condition: '',
        service_id: '',
        create_time: '',
        start_time: '',
        end_time: '',
        conditiontype: 0
      },
      loading: false,
      rules: {
        name: [
          { required: true, message: '优惠券名称不能为空', trigger: 'blur' },
        ],
        type: [
          { required: true, message: '类型不能为空', trigger: 'change' },
        ],
      },
      restaurants: [],
      state: '',
      timeout: null
    }
  },

  methods: {
    loadAll () {
      return [
        { "value": "三全鲜食（北新泾店）", "address": "长宁区新渔路144号" },
        { "value": "Hot honey 首尔炸鸡（仙霞路）", "address": "上海市长宁区淞虹路661号" }
      ];
    },
    querySearchAsync (queryString, cb) {
      var restaurants = this.restaurants;
      var results = queryString ? restaurants.filter(this.createStateFilter(queryString)) : restaurants;

      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        cb(results);
      }, 1000 * Math.random());
    },
    createStateFilter (queryString) {
      return (state) => {
        return (state.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    handleSelect (item) {
      this.form.service_id = item.id
      console.log(this.form)
    },
    //end
    open () {
      getService().then(res => {
        console.log(res.data)
        this.restaurants = res.data
      })

      console.log(this.restaurants)
    },
    submit () {
      if (this.form.conditiontype == 0) {
        this.form.condition = 0
      }
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.loading = true
          add(this.form).then(res => {
            if (res.status == 200) {
              this.$message({ message: '操作成功', type: 'success' })
              this.$emit('refesh_list')
              this.closeForm()
            }
          }).catch(() => {
            this.loading = false
          })
        }
      })
    },
    closeForm () {
      this.$emit('update:show', false)
      this.loading = false
      if (this.$refs['form'] !== undefined) {
        this.$refs['form'].resetFields()
      }
    },
  }
}
</script>
<style scoped lang="scss">
@import "@/assets/scss/common.scss";
</style>
