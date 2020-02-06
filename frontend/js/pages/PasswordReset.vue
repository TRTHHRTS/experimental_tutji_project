<template>
    <div>
        <h2 class="alignC">{{$t('p.passReset.title')}}</h2>
        <el-form label-position="top" :model="modelForm" class="w600 mAuto" ref="newPassForm">
            <input type="hidden" name="token" v-model="modelForm.token">
            <el-form-item :label="$t('p.passReset.fEmail')" prop="email"
                          :rules='[{required: true, message: $t("p.passReset.fEmailReq"), trigger: "blur"},
                                {type: "email", message: $t("p.passReset.fEmailTrig"), trigger: "blur,change"}]'>
                <el-input v-model="modelForm.email"></el-input>
            </el-form-item>
            <el-form-item :label="$t('p.passReset.fPass')" prop="password"
                          :rules='[{ required: true, message: $t("p.passReset.fPassReq"), trigger: "blur" }]'>
                <el-input v-model="modelForm.password" type="password"></el-input>
            </el-form-item>
            <el-form-item :label="$t('p.passReset.fNewPass')" prop="password_confirmation"
                          :rules='[{ required: true, message: $t("p.passReset.fNewPassReq"), trigger: "blur" }]'>
                <el-input v-model="modelForm.password_confirmation" type="password"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" v-on:click="sendPassword('newPassForm')">{{$t('p.passReset.setBtn')}}</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
export default {
    props: ['token'],
    data(){
        return{
            modelForm: {},
        }
    },
    async mounted() {
        this.modelForm.token = this.token;
    },
    computed: mapState(['user']),
    methods: {
        async sendPassword(formName) {
            try {
                this.$refs[formName].validate(async valid => {
                    if (!valid) {
                        return false;
                    }
                    if (this.modelForm.password.length < 6 || this.modelForm.password_confirmation.length < 6) {
                        this.$alert(this.$t('p.passReset.fPassTrig'), this.$t('p.common.error'), {type: "error"});
                        return false;
                    }
                    if (this.modelForm.password !== this.modelForm.password_confirmation) {
                        this.$alert(this.$t('p.passReset.fNewPassTrig'), this.$t('p.common.error'), {type: "error"});
                        return false;
                    }
                    let response = await this.post('/password/reset', this.modelForm);
                    if (response.code === 0) {
                        this.$alert(response.data, this.$t('p.common.message'), {type: "info"});
                        this.$router.push('/');
                        if (this.user) {
                            this.user.has_password = true;
                        }
                    } else if (response.code === 4) {
                        this.$alert(response.data, this.$t('p.common.error'), {type: "error"});
                    }
                });
            } catch (e) {
                // TODO ?
            }
        }
    }
}
</script>
