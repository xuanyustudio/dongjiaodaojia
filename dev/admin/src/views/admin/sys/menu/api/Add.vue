<template>
    <el-dialog title="创建菜单" width="580px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
        <el-form  :size="size" ref="form" :model="form" :rules="rules" label-width="90px">
            <el-row>
                <el-form-item label="所属父类" prop="pid">
                    <Treeselect :default-expand-level="1" v-model="form.pid" :options="list" :normalizer="normalizer" :show-count="true" placeholder="选择上级菜单"/>
                </el-form-item>
            </el-row>
            <el-row>
                <el-col :span="12">
                    <el-form-item label="菜单名称" prop="title">
                        <el-input v-model="form.title" clearable placeholder="菜单名称"/>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="控制器名称" prop="controller_name">
                        <el-input v-model="form.controller_name" clearable  placeholder="同控制器生成地址"  />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-form-item label="生成代码" prop="create_code">
                        <el-radio-group v-model="form.create_code">
                            <el-radio :label="1">是</el-radio>
                            <el-radio :label="0">否</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="12">
                    <el-form-item label="数据表名" prop="table_name">
                        <el-input v-model="form.table_name" clearable placeholder="生成或者关联的数据表"  />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="主键ID" prop="pk">
                        <el-input v-model="form.pk" clearable placeholder="数据表主键"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="12">
                    <el-form-item label="创建数据表" prop="create_table">
                        <el-radio v-model="form.create_table" :label="1" checked>是</el-radio>
                        <el-radio v-model="form.create_table" :label="0">否</el-radio>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="选择链接库" prop="connect">
                        <el-select style="width:100%" v-model="form.connect" filterable placeholder="请选择数据表">
                            <el-option v-for="item in connects" :key="item" :label="item" :value="item"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <template #footer><div class="dialog-footer">
            <el-button :size="size" :loading="loading" type="primary" @click="submit" >
                <span v-if="!loading">确 定</span>
                <span v-else>提 交 中...</span>
            </el-button>
            <el-button :size="size" @click="closeForm">取 消</el-button>
        </div></template>
    </el-dialog>
</template>

<script>
import {createMenu} from '../../sys'
import Treeselect from 'vue3-treeselect'
import "vue3-treeselect/dist/vue3-treeselect.css"
    export default {
        name:'addApiMenu',
        components: { Treeselect},
        props: {
            show: {
                type: Boolean,
                default: false
            },
            size: {
                type: String,
                default: 'small'
            },
            app_id:{
                type:String,
                default:'1',
            },
            list:{
                type:Array,
            },
            connects:{
                type:Array,
            },
            page_type_list:{
                type:Array,
            }
        },
        data() {
            return {
                form: {
                    status:1,
                    create_table:1,
                    is_post:1,
                    home_show:0,
                    create_code:1,
                    connect:'mysql',
                    app_id:'',
                    page_type:1,
                    component_path:'',
                    table_name:'',
                    pk:'',
                    page_type:1,
                },
                app_type:'',
                upload_list:[],
                iconDialogStatus:false,
                currentIconModel:'',
                loading:false,
                rules: {
                    title: [{ required: true, message: '菜单名称不能为空', trigger: 'blur' }],
                    controller_name:[
                        {pattern:/^[a-zA-Z\/|0-9]+$/, message: '访问路径格式错误'}
                    ],
                },
            }
        },
        methods: {
            submit(){
                this.$refs['form'].validate(valid => {
                    if (valid) {
                        this.loading = true
                        createMenu(this.form).then(res => {
                            if(res.status == 200){
                                this.$message({message: '操作成功', type: 'success'})
                                this.$emit('refesh_list')
                                this.closeForm()
                            }else{
                                this.loading = false
                            }
                        }).catch(()=>{
                            this.loading = false
                        })
                    }
                })
            },
            open(){
                this.form.app_id = this.app_id
            },
            /** 转换菜单数据结构 */
            normalizer(node) {
                if (node.children && !node.children.length) {
                    delete node.children
                }
                return {
                    id: node.menu_id,
                    label: node.title,
                    children: node.children
                }
            },
            iconOpen(model){
                this.iconDialogStatus = true
                this.currentIconModel = model
            },
            closeForm(){
               this.$emit('update:show', false)
               this.loading = false
               if (this.$refs['form']!==undefined) {
                    this.$refs['form'].resetFields()
               }
            }
        },
    }
</script>