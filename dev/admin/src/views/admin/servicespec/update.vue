<template>
  <div>
    <el-dialog title="修改"
               width="600px"
               class="icon-dialog"
               :model-value="show"
               @open="open"
               :before-close="closeForm"
               append-to-body>
      <el-form :size="size"
               ref="form"
               :model="form"
               :rules="rules"
               :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
        <el-row>
          <el-col :span="24">
            <el-form-item label="名称"
                          prop="name">
              <el-input v-model="form.name"
                        autoComplete="off"
                        clearable
                        placeholder="请输入名称"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="价格"
                          prop="price">
              <el-input prefix-icon="el-icon-money"
                        v-model="form.price"
                        autoComplete="off"
                        clearable
                        placeholder="请输入价格"><template #append>元</template></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="时长"
                          prop="duration">
              <el-input prefix-icon="el-icon-time"
                        v-model="form.duration"
                        autoComplete="off"
                        clearable
                        placeholder="请输入时长"><template #append>分钟</template></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="create_time"
                          prop="create_time">
              <el-date-picker value-format="yyyy-MM-dd HH:mm:ss"
                              type="datetime"
                              v-model="form.create_time"
                              clearable
                              placeholder="请输入create_time"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="排序"
                          prop="sort">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.sort"
                               clearable
                               :min="0"
                               placeholder="排序" />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-form-item label="分成"
                          prop="technician_rate">
              <el-input-number controls-position="right"
                               style="width:200px;"
                               autoComplete="off"
                               v-model="form.technician_rate"
                               clearable
                               :min="0"
                               placeholder="比例" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="类型类型"
                          prop="type">
              <el-select style="width:100%"
                         v-model="form.service_id"
                         filterable
                         clearable
                         placeholder="请选择">
                <el-option v-for="(item,i) in service_ids"
                           :key="i"
                           :label="item.key"
                           :value="item.val"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <template #footer><div
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
      </div></template>
    </el-dialog>
  </div>
</template>
<script>
import { update, getFieldList } from '@/api/admin/servicespec'
import { parseTime } from '@/utils/common'
export default {
  name: 'servicespecupdate',
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
    info: {
      type: Object,
    },
  },
  data () {
    return {
      form: {
        name: '',
        price: '',
        duration: '',
        create_time: '',
        update_time: '',
        service_id: '',
        technician_rate: ''
      },
      service_ids: [],
      loading: false,
      rules: {
      }
    }
  },
  watch: {
    show (val) {
      if (val) {
        getFieldList().then(res => {
          if (res.status == 200) {
            this.service_ids = res.data.service_ids
          }
        })
      }
    }
  },
  methods: {
    open () {
      this.form = this.info
      this.form.create_time = parseTime(this.form.create_time)
    },
    submit () {
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.loading = true
          update(this.form).then(res => {
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
