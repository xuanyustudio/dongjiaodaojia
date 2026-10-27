<template>
    <div>
        <el-button-group>
            <el-tooltip class="item" effect="dark" :content="expand ? '菜单收起' : '菜单展开'" placement="top">
                <el-button v-if="expand_status" :icon="expand ? 'el-icon-top' : 'el-icon-bottom'" size="small" @click="toggleRowExpansion"></el-button>
            </el-tooltip>
            <el-button class="hidden-sm-and-down" v-if="searchTool" icon="el-icon-search" size="small" @click="searchShow"></el-button>
            <el-button class="hidden-sm-and-down"  icon="el-icon-refresh" size="small" @click="refesh_list"></el-button>
            <el-dropdown size="small" trigger="click">
            <span class="el-dropdown-link hidden-sm-and-down">
                <el-button icon="el-icon-menu" size="small"></el-button>
            </span>
                <el-dropdown-menu slot="dropdown"  class="dropdownMenu">
                    <div style="padding:10px;">
                        <ul>
                            <li v-for="item in fieldList" :key="item.lable"><el-checkbox v-model="item.checked">{{item.lable}}</el-checkbox></li>
                        </ul>
                    </div>
                </el-dropdown-menu>
            </el-dropdown>
        </el-button-group>
    </div>
</template>

<script>
export default {
    name:'TableTool',
    props: {
        fieldList: {
            type: Array,
            default: []
        },
        searchTool:{
            type:Boolean,
            default:true,
        },
        expand_status:{
            type:Boolean,
            default:false,
        },
        expand:{
            type:Boolean,
            default:false,
        },
        searchVisible:{
            type:Boolean,
            default:true
        }
    },
    methods: {
        searchShow() {
            this.$emit('update:searchVisible', !this.searchVisible)
        },
        refesh_list() {
            this.$emit('refesh_list')
        },
        toggleRowExpansion(){
            this.$emit('toggleRowExpansion')
        }
    },
}
</script>

<style lang="scss" scoped>

</style>