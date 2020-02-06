<template>
    <div class="ml20">
        <h2>{{$t('p.aMain.title')}}</h2>
        <div>
            <el-button @click="showNewsDlg=true" type="success">{{$t('p.aMain.addNews')}}</el-button>
        </div>
        <el-card class="mt10 w500">
            <div slot="header" class="clearfix">
                <span>{{$t('p.aMain.smsBalance')}}</span>
                <el-button @click="getSMSBalance()"  style="float: right" size="small" :loading="balanceLoading">{{$t('p.aMain.refresh')}}</el-button>
            </div>
            <template v-if="balance != null">
                <span v-html="$t('p.aMain.balance', {amount: balance})"></span>
            </template>
        </el-card>
        <el-dialog :title="$t('p.aMain.addNewsTitle')" :visible.sync="showNewsDlg" width="450px">
            <el-form :model="newNews" ref="newNewsForm">
                <el-form-item prop="title_ru" :rules="[{ required: true, message: $t('p.aMain.needTitle'), trigger: 'blur' }]">
                    <el-input v-model="newNews.title_ru" auto-complete="off" :placeholder="$t('p.aMain.dTitle')"></el-input>
                </el-form-item>
                <el-form-item prop="title_en" :rules="[{ required: true, message: $t('p.aMain.needTitle'), trigger: 'blur' }]">
                    <el-input v-model="newNews.title_en" auto-complete="off" :placeholder="$t('p.aMain.dTitle')"></el-input>
                </el-form-item>
                <el-form-item prop="content_ru" :rules="[{ required: true, message: $t('p.aMain.needText'), trigger: 'blur' }]">
                    <el-input v-model="newNews.content_ru" type="textarea" auto-complete="off" :placeholder="$t('p.aMain.dText')"></el-input>
                </el-form-item>
                <el-form-item prop="content_en" :rules="[{ required: true, message: $t('p.aMain.needText'), trigger: 'blur' }]">
                    <el-input v-model="newNews.content_en" type="textarea" auto-complete="off" :placeholder="$t('p.aMain.dText')"></el-input>
                </el-form-item>
                <el-form-item prop="importance">
                    <el-select v-model="newNews.importance" :placeholder="$t('p.aMain.newsImp')">
                        <el-option :label="$t('p.aMain.usual')" :value="0"></el-option>
                        <el-option :label="$t('p.aMain.imp')" :value="1" selected></el-option>
                        <el-option :label="$t('p.aMain.warning')" :value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item class="mb0">
                    <div class="xs-text alignC" style="line-height: 2;">{{$t('p.aMain.color')}}</div>
                    <div :class="'news_important_' + newNews.importance"></div>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="showNewsDlg=false">{{$t('p.common.closeBtn')}}</el-button>
                <el-button type="primary" @click="addNewsRecord('newNewsForm')">{{$t('p.common.saveBtn')}}</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<style>
.clearfix:before,
.clearfix:after {
    display: table;
    content: "";
}
.clearfix:after {
    clear: both
}
</style>
<script>
import {mapState} from 'vuex'

export default {
    data(){
        return {
            // флаг загрузки данных
            dataLoading: false,
            // признак показа диалога добавления новости
            showNewsDlg: false,
            // объект новой новости
            newNews: { importance: 0},
            // информация об СМС-балансе
            balance: null,
            // признак загрузки информации о балансе
            balanceLoading: false
        }
    },
    computed: mapState(['user']),
    async created() {
        try {
            this.dataLoading = true;
            await this.getSMSBalance();
        } finally {
            this.dataLoading = false;
        }
    },
    methods: {
        async addNewsRecord(formName) {
            try {
                this.$refs[formName].validate(async valid => {
                    if (!valid) {
                        return false;
                    }
                    let response = await this.post('/administrator/addNewsRecord', this.newNews);
                    if (response.status === 0) {
                        this.$message.success(this.$t('p.aMain.newsAdded'));
                        this.$store.commit('setNews', response.news);
                        this.showNewsDlg = false;
                        this.newNews = {importance: 0};
                    }
                });
            } catch (e) {
                this.showNewsDlg = false;
            }
        },
        async getSMSBalance() {
            try {
                this.balanceLoading = true;
                this.balance = null;
                let response = await this.get('/administrator/getBalance');
                if (response.status !== 4) {
                    if (response.error) {
                        return this.$message.error(this.$t('p.aMain.getBalanceError', {error: response.error}));
                    }
                    this.balance = response.result.balance_currency;
                }
            } finally {
                this.balanceLoading = false;
            }
        },
    }
}
</script>
