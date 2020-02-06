<template>
    <el-dialog custom-class="profileDialog" :visible.sync="dialogVisible" width="600px" center @close="$emit('show-changed', false)">
        <div v-if="user" v-loading="isLoading">
            <h2 class="alignC mt0" v-html="$t('p.comps.profileDialog.userDialog', {name: user.name})"></h2>
            <div>
                <div class="inline-block mr5 pr5" style="border-right: 1px solid rgb(230, 235, 245)">
                    <div>
                        <div class="" style="max-width: 64px; max-height: 64px;">
                            <img v-if="user.photo_url" :src="user.photo_url" style="width:100%;height:100%;"/>
                            <img v-else src="../../images/avatar.png" style="width:100%;height:100%;"/>
                        </div>
                    </div>
                    <h5>{{$t('p.comps.profileDialog.creationDate')}}{{user.created_at | formatDate}}</h5>
                </div>
                <div class="inline-block" style="vertical-align: top;">
                    <div>
                        <el-tag v-if="user.email_verified" class="m5" type="info">{{$t('p.comps.profileDialog.emailConfirm')}}</el-tag>
                        <el-tag v-if="user.is_phone_confirmed" class="m5" type="info">{{$t('p.comps.profileDialog.phoneConfirm')}}</el-tag>
                    </div>
                    <h5>{{$t('p.comps.profileDialog.activeLessons', {count: user.lesson_count})}}</h5>
                </div>

            </div>
            <div class="alignC">
                <el-collapse class="mt10">
                    <el-collapse-item v-if="user.recommendations.length > 0" :title="$t('p.comps.profileDialog.recommend', {count: user.recommendations.length})" name="recommends">
                        <template v-for="rec in user.recommendations">
                            <div class="alignC p5" :key="rec.id"><a class="btn_link" v-on:click="$router.push('/lesson/'+rec.id)"><strong>{{ rec.name }}</strong></a></div>
                        </template>
                    </el-collapse-item>
                </el-collapse>
            </div>
        </div>
        <div class="alignC" v-else>
            <i class="el-icon-loading"></i>
        </div>
        <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="$emit('show-changed', false)">{{$t('p.common.closeBtn')}}</el-button>
            </span>
    </el-dialog>
</template>
<style lang="scss" scoped>
.profileDialog {
    .el-dialog__body {
        padding-top: 0;
    }
}
</style>
<script>
export default {
    props: ['isVisible', 'user', 'isLoading'],
    data() {
        return {
            dialogVisible: false
        }
    },
    watch : {
        isVisible(value) {
            this.dialogVisible = value;
        }
    },
    methods: {
        emitOpeningProfile(userId) {
            this.$emit('load', Number(userId));
        }
    }
}
</script>
