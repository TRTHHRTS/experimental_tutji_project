<template>
    <div v-if="user">
        <h1 class="alignC">{{$t('p.mes.title')}}</h1>
        <div class="alignC" v-if="loading">
            <i class="el-icon-loading"></i>
        </div>
        <template v-if="!loading">
            <h2 v-if="messages.length <= 0" class="alignC">{{$t('p.mes.noDialogs')}}</h2>
            <div class="alignC">
                <el-button v-if="messages.length <= 0" v-on:click="$router.push('/')" type="primary">{{$t('p.common.backToMain')}}</el-button>
            </div>
            <div class="mt20">
                <el-card class="message-card mAuto mb10" v-for="message in messages" :key="message.rcpt_id">
                    <div class="floatR xs-text">{{$t('p.mes.lastMes')}}{{message.date | formatDateTimeFromNow}}</div>
                    <div><span class="lg-text"><strong>{{ message.username }}</strong></span></div>
                    <el-button v-on:click="$router.push('/messages/' + message.rcpt_id)" type="text" class="lg-text">{{$t('p.mes.lastMes') + message.text}}</el-button>
                    <div class="alignR">
                        <el-button v-on:click="deleteChannel(message.rcpt_id)" type="warning" size="small" plain icon="el-icon-delete"></el-button>
                    </div>
                </el-card>
            </div>
        </template>
    </div>
</template>
<style>
.message-card {
    width: 500px;
    margin-bottom: 10px;
}
.el-card__body {
    margin-top: 0;
}
</style>
<script>
import { mapState } from 'vuex'

export default{
    data(){
        return {
            loading: true,
            messages: []
        }
    },
    computed: mapState(['user', 'isModer', 'isAdmin']),
    methods: {
        async deleteChannel(rcptId) {
            try {
                await this.$confirm(this.$t('p.mes.delDialogQuestion'), this.$t('p.common.warning'), {
                    confirmButtonText: this.$t('p.mes.confirmDelete'), cancelButtonText: this.$t('p.common.cancelBtn'), type: 'warning'
                });
                alert('TODO here (rcptId=' + rcptId + ')');
            } catch (e) {
                this.$message({type: 'info', message: this.$t('p.mes.cancelDelete')});
            }
        }
    },
    async created() {
        this.loading = true;
        this.messages= await this.get('/messages');
        this.loading = false;
    }
}
</script>
