<template>
    <div id="all">
         <el-dialog title="百度地图坐标选择器" width="600px" top="20px" :model-value="show" :before-close="closeForm" append-to-body>
            <el-input id="suggestId" size="small" suffix-icon="el-icon-location" v-model="address" autoComplete="off" clearable  placeholder="请输入搜索地址"></el-input>
            <div id="allmap"></div>
            <template #footer><div class="dialog-footer">
				<el-button size="small" type="primary" @click="submit" >
					<span>确 定</span>
				</el-button>
				<el-button size="small" @click="closeForm">取 消</el-button>
			</div></template>
         </el-dialog>
    </div>
</template>

<script>
   export default {
       props: {
            show: {
                type: Boolean,
                default: false
            },
            address_detail:{
                type:String,
                default:null
            },
            picker:{
                type:String
            }
        },
        data(){
            let points =  this.address_detail ? JSON.parse(this.address_detail) : {lng:'',lat:'',address:''}
            return {
               lng:points.lng,
               lat:points.lat,
               address:points.address,
            }
        },
        mounted(){
            this.loadScript()
        },
        methods: {
            loadScript() {
                var script = document.createElement("script");
                script.type = "text/javascript";
                window.initMap = () => {
                    this.init()
                };
                script.src ="http://api.map.baidu.com/api?v=2.0&ak=" +this.mapKey +"&callback=initMap"
                document.body.appendChild(script);
            },
            init(){
                var a = {lng:this.lng,lat:this.lat,of: "inner"}
                var map = new BMap.Map("allmap");
                map.enableScrollWheelZoom(false)
                if(this.lng && this.lat){
                    var point = new BMap.Point(this.lng,this.lat);
                    map.centerAndZoom(point,16)
                    var myIcon = new BMap.Icon("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADQAAABJCAMAAABFGvXGAAACf1BMVEUAAAD/PyL/PyL/PyL/PyL/PyL/PyL/PyL/PyL/PyL/PyL/PyL/PyL/PyP/PyL/PyL+PiL/PyL/PyL/PyP/PyL/PyL/PyL/PyL/PyL4OyP/PiL/PyL/PyL/PyLzOiX5PCX9QCj/PyLQJiX0RkTkNC39PyXsNST/PyL/PyL/PyLyRUTgNDPdMjHzRELxQ0HzRUPTJyTZKyf1RULeLyf0Qz30Qzz8PST/PyLWLS3rPz/RKCjMJCTZLy/mOjnUKirPJSXLIiLzRkXpPDvWLCvgNDP0R0XsPz3ZLyzgNDPVKijRJSTtPzvcMS71R0TjNjPZLCnyREHpOzjiNDDtPzvkNS7kNTDsPDXYKiPiLyPqNy3zQTncLCTpMyTxOinsNif0QTf1Qzz5QjTxOyr4QC/tQT/mOjnwQkDmOTfPJSLdLyr0RULyQj3nOTbWKibTKCT0RD/yQTrxQz/lNCzbLSjyPzX2RkHXKSL2RDzgMSjkMiXiMCP4RDrrNyn/////XVv/S0v/W1n/SEf/YF7/UVD/VVP/WFb/REP/TUz/YV//Xlz/Pj7/T07/RkX/QED/Ozv/Skn/WVj/OTn/VlX/U1L/QkL/QUH/ODf5UlH4UU/vQUD7V1b2TEv0SUj/+vr3Tk3/2dn9XVz8W1r6TEv7R0b7REPvPTzzPDvoPDvnOjnpODjnMDDjLS3dKir/vr7/qqn/pqX/mZn/ZWT6VVP9VFP//Pz/8PD/4eH/3t3/zs7/w8P/ra3/iIj/fX3/e3r5UU/+TUzsPTz5PDz+Nzf4NjbrNjbpNjbyNjXuNjXlMjLjMjLxMTHsMTDlLCz/9vb/6ej/yMf/ubj/lJT/kI//gH/9WFcH01InAAAAfXRSTlMAAgYEFgoIDB4TDhgzJBFBKiYbFD1FRDg7Ni8tEChcUUAa6tmJSkQxIA/++fTg1tDFr6qaimVNIv79/f379/f29PLy7efj4+Ld3NnV1MvKyMTDvLmmopqMgXd1c21oYV5PRz0h7url3sC3tLKrq6iimJaVkYaBgHt4XVtUUgcS3xIAAAQzSURBVEjHjZdldxNBFIaZ3Y1nI43QhFChhVLc3d3d3d3dijRIi2uclCoV3N3d7Qcxuwls7mxWno9zz3Pu3XlnMieN0oAQTVM8NI0QWZUwNLpCA8Nj8Ok0lKKHaK2RWbi109i8oUPzxnbqsrt7rsOooZGsorEs6DQwGoyUX8KUR4LRgau6siaLtIaQ1tI1LxopLkmhOBJtu71phk+LUPo2upz28fISEeWhvPkso0vXDFGWLq1ulaQl0mp9E71Fi0SOVj8hVFoiQXGofU53B7YIx94+VCxDsG1zO6NB0FncDjvy1kI76IVoxwTsyBNql8laKCRIuo3xUkXiq816IxKGWxDeW6pMbJaN/6zkcO2Ce1Vwa5mTtSTjQppZ0b2qiG8oyNAlJJoZFlEn/VlitRvoRKOusf0qiXUpyOC/ChVODoqqT69dvXrtiWj51ujGbCHXilrc8zSplBXx3P9NWj2zmzAUwtPNb3Ua8utiUZI7N4hSbIuZ2wpknBY9BHjCO0nrMazFJzfujgOmLROfgfXT94pS+LYfFIOjnU3x/lHMsNtg/XER4Cco3h5kxR+FpUG3z6byA0oPzwIuW70mbSOt6fJBwAMolcHq5W5mXqqFy2VQukdI2eZcDZb6ngPLD6H0AEq1LnMGJy19di6VG1C6Dor+vm6uE8WMq/CnUvo11bl4ExQrlrsLTFosrYn5ATdAI1iLTeS3nDbMDPshVwXnO1EKT3PacLiocFHtMYj/+sXkbI/8RKl2bqbdhyWdfkTFMYKb18ru3r3/6Ca5XjHE5dFzB5Zi8sNHVBKewh0IhG+7z9P6uTrneeuszMR9R7qMqeF9qghPcpvxdJxEGRa1qVTjVLbOctocFCdxW5H/Qo30YmqyUaKVbUTlKUUqh3TLbMo3SraaVH1AkepxQiOulaNFmyolp6oN/iJGeDaQUZ9fpyTVbXInXg2hlW1M9XFZqsc0F74Iw2e1p0+VnFPVZ67Vy//6CxLtYzvXHZahborLw/po4tFlvMNrpJ2a4d3wLoied51+Xg9pqcdM67/tBgNa2I5vz0vwdqWrMf8IkpbG5BlccyEtNYOznTaThnCSYe34eCItH/OtQkRQogz28W/SOW/GuxrbDUJExIAt+r08KuJlv3k5TcBwYC+MLTd/EEsfOrs9LY1gF8CAjG3ke9J5PxKfH4YcDpymnb0I52SvOdYCeH7EYa2tPwmo75g2IniaPM3epTrvmmWLz484rNlXUqUrM6QigmF1qD/zn/oO0hHBsPq//ue87p/lhBFJhjW94Z/U0NltxhHJK8mwRn1OOJ9HkVdcJqxdvV8FMK96zyGuuGxY6xo4qQFHBK+4QlifAoFPyhGRYQUCyhGRYa348kVVRDCsAQOyiFukHJZ+2/Qcsx5GpDwg6/WyYDg1Axpycw1wOGUJURrwv1OtJq38BbsNQs0bptLnAAAAAElFTkSuQmCC", new BMap.Size(50,70));
                    map.addOverlay(new BMap.Marker(a,{icon:myIcon}))
                }else{
                    var geolocation = new BMap.Geolocation();
                    geolocation.getCurrentPosition(function(r) {
                        map.centerAndZoom(r.point, 16); //根据浏览器定位,设置城市和地图级别。
                    })
                }
                var that = this;
                //地图点击事件。获取点击位置经纬度位置
                map.addEventListener('click', function(e) {
                    that.lng = e.point.lng
                    that.lat = e.point.lat
                    map.clearOverlays();
                    map.addOverlay(new BMap.Marker(e.point))
                    var point = new BMap.Point(e.point.lng, e.point.lat);
                    var gc = new BMap.Geocoder();
                    gc.getLocation(point, function (rs) {
                        var addComp = rs.addressComponents;
                        that.address = addComp.province + addComp.city + addComp.district + addComp.street
                    })
                })
    
                var ac = new BMap.Autocomplete( //建立一个自动完成的对象
                    {
                        "input": "suggestId",
                        "location": map
                    });
                ac.setInputValue(that.address)
                var myValue;
                ac.addEventListener("onconfirm", function(e) { //鼠标点击下拉列表后的事件
                    var _value = e.item.value;
                    myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
                    that.address = myValue     
                    map.clearOverlays(); //清除地图上所有覆盖物
                    function myFun() {
                        var pp = local.getResults().getPoi(0).point; //获取第一个智能搜索的结果
                        that.lng = pp.lng
                        that.lat = pp.lat
                        map.centerAndZoom(pp, 16);
                        var myIcon = new BMap.Icon("data:image/png;base64,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", new BMap.Size(50,70));
                        map.addOverlay(new BMap.Marker(pp,{icon:myIcon})); //添加标注
                    }
                    var local = new BMap.LocalSearch(map, { //智能搜索
                        onSearchComplete: myFun
                    });
                    local.search(myValue);
                })
            },
            submit(){
                let address = {address:this.address,lng:this.lng,lat:this.lat}
                this.$emit('update:address_detail', JSON.stringify(address))
                this.closeForm()
		    },
            closeForm(){
                this.$emit('update:show', false)
            }
       },
   }
</script>
<style scoped>
   #allmap{
        width: 100%;
        height: 400px;
        margin-top: 1px;
    }
</style>
