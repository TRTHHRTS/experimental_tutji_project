<template>
    <div>
        <h1 class="alignC">{{$t('p.faq.title')}}</h1>
        <div class="alignC" v-if="loading">
            <i class="el-icon-loading"></i>
        </div>
        <div class="mAuto" style="width: 900px;">
            <el-button v-if="$store.getters.isOperator"
                       v-on:click="faqsDlgVis=true;newFaqLoading=true;"
                       type="success" class="mb10" :loading="newFaqLoading">
                {{$t('p.faq.add')}}
            </el-button>
            <el-collapse>
                <el-collapse-item :title="faq.id + '. ' + faq.question" :name="faq.id" v-for="faq in faqs" :key="faq.id">
                    <div>{{ faq.answer }}</div>
                    <div id="actions">
                        <el-button v-if="$store.getters.isOperator"
                                   v-on:click="deleteFaq(faq.id)"
                                   type="warning" size="small" plain icon="el-icon-delete">{{$t('p.common.deleteBtn')}}
                        </el-button>
                    </div>
                </el-collapse-item>
            </el-collapse>
            <p class="mt5">{{$t('p.faq.feedback1')}}<router-link to="/feedback">{{$t('p.faq.feedback2')}}</router-link></p>
        </div>

        <el-dialog :title="$t('p.faq.add')" :visible.sync="faqsDlgVis">
            <el-form :model="newFaq" ref="faqForm">
                <el-form-item :label="$t('p.faq.q')" prop="q" :rules='[{ required: true, message: $t("p.faq.qReq"), trigger: "blur" }]'>
                    <el-input v-model="newFaq.q"></el-input>
                </el-form-item>
                <el-form-item :label="$t('p.faq.a')" prop="a" :rules='[{ required: true, message: $t("p.faq.aReq"), trigger: "blur" }]'>
                    <el-input type="textarea" v-model="newFaq.a"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="faqsDlgVis=false;newFaqLoading=false;">{{$t('p.common.closeBtn')}}</el-button>
                <el-button type="primary" @click="submitForm('faqForm')">{{$t('p.common.saveBtn')}}</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
export default {
    data() {
        return {
            faqs: [],
            faqsDlgVis: false,
            newFaqLoading: false,
            newFaq: {
                q: '',
                a: ''
            },
            loading: true
        }
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate(valid => {
                if (!valid) {
                    return false;
                }
                this.addFaq();
            });
        },
        async addFaq() {
            this.faqsDlgVis = false;
            let result = await this.post('/faq/new', this.newFaq);
            this.newFaqLoading = false;
            if (result.status === 0) {
                this.faqs.push(result.faq);
                this.newFaq = {a: '', q: ''};
            }
        },
        async deleteFaq(id) {
            try {
                await this.$confirm(this.$t('p.faq.deleteFaq'));
                let result = await this.post('/faq/delete', {ID: id});
                if (result.status === 0) {
                    _.remove(this.faqs, item => {
                        return item.id === id;
                    });
                    this.$forceUpdate();
                }
            } catch(e) {
                // ничего не делаем
            }
        }
    },
    async created() {
        try {
            this.faqs = await this.get('/faq');
        } finally {
            this.loading = false;
        }
    }
}
</script>
