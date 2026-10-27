<template>
    <div>
        <el-row v-for="(item,i) in extendList" :key="i">
            <el-col v-if="item.type == 1" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-input v-model="form[item.field]" autoComplete="off" clearable  :placeholder="'请输入'+item.title"></el-input>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 2" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-select style="width:100%" :size="size" v-model="form[item.field]" clearable filterable placeholder="请选择">
						<el-option v-for="(vo,index) in JSON.parse(item.item_config)" :key="index" :label="vo.key" :value="['varchar','text','longtext'].includes(item.datatype)?vo.val:parseInt(vo.val)"></el-option>
					</el-select>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 3" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <el-select multiple style="width:100%" v-model="form[item.field]" filterable clearable placeholder="请选择">
                        <el-option v-for="(vo,index) in JSON.parse(item.item_config)" :key="index" :label="vo.key" :value="vo.val"></el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 4" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-radio-group v-model="form[item.field]">
                        <el-radio v-for="(vo,index) in JSON.parse(item.item_config)" :key="index" :label="['varchar','text','longtext'].includes(item.datatype)?vo.val:parseInt(vo.val)">{{vo.key}}</el-radio>
                    </el-radio-group>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 6" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-switch :active-value="1" :inactive-value="0" v-model="form[item.field]"></el-switch>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 8" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-input type="textarea" v-model="form[item.field]" autoComplete="off" clearable  :placeholder="'请输入'+item.title"></el-input>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 9" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-date-picker type="date" v-model="form[item.field]" autoComplete="off" clearable  :placeholder="'请输入'+item.title"></el-date-picker>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 10" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
					<el-date-picker type="daterange" v-model="form[item.field]" clearable range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
		        </el-form-item>
            </el-col>
            <el-col v-if="item.type == 13" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <ImagesUpload :size="size"  :uploadType="item.upload_type" fileType="image" :upload_config_id="upload_config_id" :image.sync="form[item.field]"></ImagesUpload>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 14" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <ImagesUpload :size="size"  fileType="images"  :upload_config_id="upload_config_id" :images.sync="form[item.field]"></ImagesUpload>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 15" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <ImagesUpload :size="size" fileType="file"  :file.sync="form[item.field]"></ImagesUpload>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 16" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <ImagesUpload :size="size" fileType="files" :files.sync="form[item.field]"></ImagesUpload>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 17" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form[item.field]" clearable :min="0" placeholder="计数器"/>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 18" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <Tag :tagList.sync="form[item.field]"></Tag>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 19" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <el-slider v-model="form[item.field]"></el-slider>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 20" :span="23">
                <el-form-item :label="item.title" :prop="item.field">
                    <el-input v-model="form[item.field]" autoComplete="off" clearable placeholder="请输入颜色选择器"/>
                </el-form-item>
            </el-col>
            <el-col v-if="item.type == 20" :span="1">
                <el-color-picker size="small" v-model="form[item.field]" show-alpha></el-color-picker>
            </el-col>
            <el-col v-if="item.type == 21" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <draggable v-model="form[item.field]" v-bind="{group:'item'}" :handle="'.'+item.field+'-handle'">
                    <el-row v-for="(vo,i) in form[item.field]" :key="i">
                        <el-col :span="10">
                            <el-form-item class="activeItem">
                                <el-input  v-model="vo.key" placeholder="选项名称"/>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item class="activeItem">
                                <el-input style="position:relative;left:5px;" v-model="vo.val" placeholder="选项值"/>
                            </el-form-item>
                        </el-col>
                        <el-col :span="4">
                            <el-button type="danger" size="mini" style="position:relative;left:15px"  icon="el-icon-close" @click="deleteItem(item.field,i)"></el-button>
                            <el-button :class="''+item.field+'-handle'" type="success" size="mini" style="position:relative;left:15px" icon="el-icon-rank"></el-button>
                        </el-col>
                    </el-row>
                    </draggable>
                    <div>
                        <el-button type="info" icon="el-icon-plus" style="padding:5px 7px" size="mini" @click="addItem(item.field)">追加</el-button>
                        <el-button v-if="form[item.field] && form[item.field].length > 0" type="warning" icon="el-icon-delete" style="padding:5px 7px" size="mini" @click="clearItem(item.field)">清空</el-button>
                    </div>
                </el-form-item>
            </el-col>
            <el-col  v-if="item.type == 23" :span="22">
                <el-form-item :label="item.title" class="map" :prop="item.field">
                    <el-input type="textarea"  v-model="form[item.field]" placeholder="请选择坐标位置" v-on:click.native="bddtmapOpen" readonly clearable></el-input>
                </el-form-item>
                <BaiduMap v-if="bddtDialogStatus" :show.sync="bddtDialogStatus" :address_detail.sync="form[item.field]"></BaiduMap>
            </el-col>
            <el-col v-if="item.type == 23" :span="1">
                <div class="el-input-group__append" @click="bddtmapOpen" style="height:49px;background-color:#fff;padding:0 13px;cursor:pointer">
                    <i style="font-size:20px" class="el-icon-location"></i>
                </div>
            </el-col>
            <el-col v-if="item.type == 28" :span="22">
                <el-form-item :label="item.title" class="map" :prop="item.field">
                    <el-input type="textarea"  v-model="form[item.field]" placeholder="请选择坐标位置" v-on:click.native="txdtmapOpen" readonly clearable></el-input>
                </el-form-item>
                <TxMap v-if="txdtDialogStatus" :show.sync="txdtDialogStatus" :address_detail.sync="form[item.field]"></TxMap>
            </el-col>
            <el-col v-if="item.type == 28" :span="1">
                <div class="el-input-group__append" @click="txdtmapOpen" style="height:49px;background-color:#fff;padding:0 13px;cursor:pointer">
                    <i style="font-size:20px" class="el-icon-location"></i>
                </div>
            </el-col>
            <el-col v-if="item.type == 24" :span="22">
                <el-form-item :label="item.title" class="map" :prop="item.field">
                    <el-input type="textarea" v-model="form[item.field]" placeholder="请选择坐标位置" v-on:click.native="gddtmapOpen" readonly clearable></el-input>
                </el-form-item>
                <GaodeMap v-if="gddtDialogStatus" :show.sync="gddtDialogStatus" :address_detail.sync="form[item.field]"></GaodeMap>
            </el-col>
            <el-col v-if="item.type == 24" :span="1">
                <div class="el-input-group__append" @click="gddtmapOpen" style="height:49px;background-color:#fff;padding:0 13px;cursor:pointer">
                    <i style="font-size:20px" class="el-icon-location"></i>
                </div>
            </el-col>
            <el-col v-if="item.type == 25" :span="24">
                <el-form-item :label="item.title" :prop="item.field">
                    <WangEditor :isClear="isClear" :upload_config_id="upload_config_id" :wangEditorContent.sync="form[item.field]"></WangEditor>
                </el-form-item>
            </el-col>
		</el-row>
    </div>
</template>

<script>
import ImagesUpload from '@/components/common/ImagesUpload.vue'
import BaiduMap from '@/components/map/BaiduMap.vue'
import GaodeMap from '@/components/map/GaodeMap.vue'
import TxMap from '@/components/map/TxMap.vue'
import WangEditor from '@/components/editor/WangEditor.vue'
import TinymceEditor from '@/components/editor/TinymceEditor.vue'
import Tag from '@/components/common/Tag.vue'
import draggable from "vuedraggable"
export default {
    name:'extend',
    components: {
		ImagesUpload,
		BaiduMap,
		GaodeMap,
		TxMap,
		WangEditor,
		TinymceEditor,
        Tag,
        draggable
	},
    props: {
		extendList: {
			type: Array,
        },
        extendData:{
            type:Object
        },
        upload_config_id:{
            type:Number,
        },
        isClear:{
            type:Boolean
        },
        issubmit:{
            type:Boolean
        },
    },
    watch: {
        extendData(val){
            this.form = val
        },
        extendList(val){
            val.forEach(item=>{
                if(item.type == 21){
                   this.$set(this.extendData,item.field,[{}])
                }
            })
        },
        isClear(val){
            if(val){
                this.form = {}
            }
        },
        issubmit(val) {
            if(val){
                this.$emit('update:extendData',this.form)
            }
        },
    },
    data() {
        return {
            form: this.extendData,
            size:'small',
            isClearEditor:false,
            bddtDialogStatus:false,
			txdtDialogStatus:false,
			gddtDialogStatus:false,
        }
    },
    mounted () {
        if(this.extendList.length > 0){
            this.extendList.forEach(item=>{
                if([3,5,14,16,18].includes(item.type)){
                    this.setDefaultVal(item.field)
                }
                if(item.type == 21 && !this.extendData[item.field]){
                   this.$set(this.extendData,item.field,[])
                }
            })
        }
    },
    methods: {
        bddtmapOpen(){
			this.bddtDialogStatus = true
		},
		txdtmapOpen(){
			this.txdtDialogStatus = true
		},
		gddtmapOpen(){
			this.gddtDialogStatus = true
        },
        setDefaultVal(key){
			if(this.form[key] == null || this.form[key] == ''){
				this.form[key] = []
			}
		},
        addItem(key){
            this.form[key].push({})
		},
		deleteItem(key,index){
			this.form[key].splice(index,1)
		},
		clearItem(key){
			this.form[key] = []
		},
    },
}
</script>
<style scoped lang="scss">
@import '@/assets/scss/common.scss'
</style>
