<template>
    <div class="form-group">
        <label for="kitchen" class="col-md-4 control-label">厨房</label>
        <div class="col-md-4">
            <input type="file" class="form-inline" name="chufang">
        </div>
    </div>
    <div class="form-group">
        <label for="saloon" class="col-md-4 control-label">大厅</label>
        <div class="col-md-4">
            <input type="file" class="form-inline" name="saloon">
        </div>
    </div>
    <div class="form-group">
        <label for="bedroom" class="col-md-4 control-label">主卧</label>
        <div class="col-md-4">
            <input type="file" class="form-inline" name="bedroom">
        </div>
    </div>
    <div class="form-group">
        <label for="toilet" class="col-md-4 control-label">卫生间</label>
        <div class="col-md-4">
            <input type="file" class="form-inline" name="toilet">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <button type="button" class="btn btn-default" @click.prevent="sendUpload" >上传</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Upload",
        props:[],
        data(){
            return{
                chufang:null,
                saloon:null,
                bedroom:null,
                toilet:null,
            }
        },
        methods:{
            sendUpload(){
                
            },
        }
    }
</script>

<style scoped>

</style>