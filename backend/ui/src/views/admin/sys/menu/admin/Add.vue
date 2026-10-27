<template>
    <el-dialog title="创建菜单" width="580px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
        <el-form :size="size" ref="form" :model="form" :rules="rules" label-width="90px">
            <el-tabs v-model="activeName">
                <el-tab-pane style="padding-top:10px"  label="基本信息" name="first">    
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
                            <el-form-item label="访问路径" prop="controller_name">
                                <el-input v-model="form.controller_name" clearable @input="setComponent" placeholder="同控制器生成地址"  />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="12">
                            <el-form-item label="生成代码" prop="create_code">
                                <el-radio-group v-model="form.create_code" @change="changeCode">
                                    <el-radio :label="1">是</el-radio>
                                    <el-radio :label="0">否</el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="显示状态" prop="status">
                                <el-radio v-model="form.status" :label="1">显示</el-radio>
                                <el-radio v-model="form.status" :label="0">隐藏</el-radio>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="24">
                            <el-form-item label="组件路径" prop="component_path">
                                <el-input v-model="form.component_path" clearable placeholder="vue组件生成路径"  />
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
                    <el-row v-if="form.create_code == 1">
                        <el-col :span="24">
                            <el-form-item label="页面结构" prop="page_type">
                                <el-select style="width:100%" v-model="form.page_type" filterable placeholder="请选择页面显示结构">
                                    <el-option v-for="item in page_type_list" :key="item.type" :label="item.name" :value="item.type"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="form.status == 1">
                        <el-form-item label="icon图标" prop="icon">
                            <el-input v-model="form.icon" placeholder="点击选择图标" clearable>
                                <el-button type="success" slot="append" icon="el-icon-thumb"  @click="iconOpen('icon')">请选择</el-button>
                            </el-input> 
                        </el-form-item>
                    </el-row>
                </el-tab-pane>
                <el-tab-pane style="padding-top:10px"  label="拓展信息" name="second">
                    <el-row>
                        <el-form-item label="上传配置" prop="upload_config_id">
                            <el-select style="width:100%" v-model="form.upload_config_id" filterable placeholder="请选择">
                                <el-option v-for="(item,i) in upload_list" :key="i" :label="item.title" :value="item.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-row>
                    <el-row v-if="form.status == 1 && form.create_code == 1">
                        <el-col :span="24">
                            <el-form-item label="首页导航" prop="home_show">
                                <el-radio v-model="form.home_show" :label="1">显示</el-radio>
                                <el-radio v-model="form.home_show" :label="0">隐藏</el-radio>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="form.home_show == 1">
                        <el-col :span="24">
                            <el-form-item label="导航图标" prop="menu_pic">
                                <ImagesUpload size="small" uploadType="2" fileType="image" :image.sync="form.menu_pic"></ImagesUpload>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="24">
                            <el-form-item label="允许投稿" prop="is_post">
                                <el-radio v-model="form.is_post" :label="1">是</el-radio>
                                <el-radio v-model="form.is_post" :label="0">否</el-radio>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-tab-pane>
            </el-tabs>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button :size="size" :loading="loading" type="primary" @click="submit" >
                <span v-if="!loading">确 定</span>
                <span v-else>提 交 中...</span>
            </el-button>
            <el-button :size="size" @click="closeForm">取 消</el-button>
        </div>
        <Icon :iconshow.sync="iconDialogStatus" :currentIconModel.sync="currentIconModel" @selectIcon="selectIcon" size="small"></Icon>
    </el-dialog>
</template>

<script>
import {createMenu,getAppInfo,getUploadList} from '../../sys'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
import Treeselect from "@riophae/vue-treeselect"
import "@riophae/vue-treeselect/dist/vue-treeselect.css"
import Icon from '@/components/common/Icon.vue'
    export default {
        name:'addMenu',
        components: { Treeselect,Icon,ImagesUpload},
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
                    is_post:0,
                    home_show:0,
                    create_code:1,
                    connect:'mysql',
                    app_id:'',
                    page_type:1,
                    component_path:'',
                    table_name:'',
                    pk:'',
                },
                activeName: 'first',
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
                getUploadList({app_id:this.app_id}).then(res => {
                    if(res.status == 200){
                        this.upload_list = res.data
                        this.app_type = res.app_type
                    }
                })
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
            selectIcon(icon){
                this.form[this.currentIconModel] = icon
            },
            setComponent(val){
                getAppInfo({'controller_name':val}).then(res => {
                    if(res.status == 200){
                        if(res.app_name && val){
                            this.form.component_path = res.app_name+'/'+val.toLowerCase()+'/index.vue'
                        }
                        this.form.table_name = res.table_name.toLowerCase()
                        this.form.pk = res.pk.toLowerCase()
                    }
                })
            },
            changeCode(val){
                if(!val){
                    this.form.component_path = ''
                    this.form.create_table = 0
                    this.form.pk = ''
                    this.form.table_name = ''
                }
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