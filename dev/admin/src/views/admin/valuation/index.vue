<template>
  <div>
    <el-card shadow="never"
             class="card">
      <el-form size="small"
               ref="form"
               :model="form"
               :rules="rules"
               label-width="90px">
        <el-tabs v-model="activeName"
                 @tab-click="handleClick">
          <el-tab-pane style="padding-top:10px"
                       label="公交"
                       name="公交">
            <el-row>
              <el-col :span="24">
                <el-form-item label="起步价"
                              prop="starting_price">
                  <el-input-number controls-position="right"
                                   style="width:200px;"
                                   autoComplete="off"
                                   v-model="form.starting_price"
                                   clearable
                                   :min="0"
                                   placeholder="请输入起步价" />
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="每公里均价"
                              prop="average_price">

                  <el-input-number controls-position="right"
                                   style="width:200px;"
                                   autoComplete="off"
                                   v-model="form.average_price"
                                   clearable
                                   :min="0"
                                   placeholder="请输入每公里均价" />
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="起步公里"
                              prop="kilometre">
                  <el-input-number controls-position="right"
                                   style="width:200px;"
                                   autoComplete="off"
                                   v-model="form.kilometre"
                                   clearable
                                   :min="0"
                                   placeholder="请输入起步公里" />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
          <el-tab-pane style="padding-top:10px"
                       label="的士"
                       name="的士">
            <el-row>
              <el-col :span="24">
                <el-form-item label="起步价"
                              prop="starting_price">

                  <el-input-number controls-position="right"
                                   style="width:200px;"
                                   autoComplete="off"
                                   v-model="form.starting_price"
                                   clearable
                                   :min="0"
                                   placeholder="请输入起步价" />
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="每公里均价"
                              prop="average_price">
                  <el-input-number controls-position="right"
                                   style="width:200px;"
                                   autoComplete="off"
                                   v-model="form.average_price"
                                   clearable
                                   :min="0"
                                   placeholder="每公里均价" />

                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="起步公里"
                              prop="kilometre">
                  <el-input-number controls-position="right"
                                   style="width:200px;"
                                   autoComplete="off"
                                   v-model="form.kilometre"
                                   clearable
                                   :min="0"
                                   placeholder="请输入起步公里" />
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
        </el-tabs>
        <el-form-item>
          <el-button size="small"
                     type="primary"
                     @click="submit">保存设置</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>
<script>
import { index, getInfo } from '@/api/admin/valuation'
export default {
  name: 'valuationindex',
  components: {
  },
  data () {
    return {
      form: {
        name: '',
        starting_price: '',
        average_price: '',
        create_time: '',
        kilometre: '',
      },
      loading: false,
      activeName: '公交',
      rules: {
      }
    }
  },
  mounted () {
    getInfo({ name: this.activeName }).then(res => {
      if (res.status == 200) {
        this.form = JSON.stringify(res.data) == '[]' ? {} : res.data
      }
    })
  },
  methods: {
    index () {
      getInfo({ name: this.activeName }).then(res => {
        if (res.status == 200) {
          this.form = JSON.stringify(res.data) == '[]' ? {} : res.data
          console.log(this.form)
        }

      })
    },
    handleClick (tab) {
      this.index()
      console.log(tab.name)
    },
    submit () {
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.loading = true
          index(this.form).then(res => {
            if (res.status == 200) {
              this.$message({ message: '操作成功', type: 'success' })
            }
          }).catch(() => {
            this.loading = false
          })
        }
      })
    },
  }
}
</script>
<style scoped lang="scss">
@import "@/assets/scss/common.scss";
</style>
