<template>
    <div>
        <h1 class="alignC mb20" v-html="$t('p.reg.title')"></h1>
        <el-form label-position="top" :model="registerForm" class="w400 mAuto" ref="registerForm" @keyup.enter.native="sendLogin('registerForm')">
            <el-form-item prop="email"
                          :rules='[{required: true, message: $t("p.reg.fEmailReq"), trigger: "blur"},
                          {type: "email", message: $t("p.reg.fEmailTrig"), trigger: "blur"}]'>
                <el-input v-model="registerForm.email" :placeholder="$t('p.reg.fEmail')"></el-input>
            </el-form-item>
            <el-form-item prop="name"
                          :rules='[{required: true, message: $t("p.reg.fNameReq"), trigger: "blur"},
                          {min: 2, max: 100, message: $t("p.reg.fNameTrig"), trigger: "blur"},
                          {min: 2, max: 100, message: $t("p.reg.fNameTrig"), trigger: "change"}]'>
                <el-input v-model="registerForm.name" :placeholder="$t('p.reg.fName')"></el-input>
            </el-form-item>
            <el-form-item prop="phone"
                          :rules='[{required: true, message: $t("p.reg.fPhoneReq"), trigger: "blur" },
                          {min: 10, max: 10, message: $t("p.reg.fPhoneTrig"), trigger: "change"},
                          {min: 10, max: 10, message: $t("p.reg.fPhoneTrig"), trigger: "blur"}]'>
                <el-input v-model="registerForm.phone" :maxlength="10" @keypress.native="isNumber" :placeholder="$t('p.reg.fPhone')">
                    <template slot="prefix">+7</template>
                </el-input>
            </el-form-item>
            <el-form-item prop="password"
                          :rules='[{required: true, message: $t("p.reg.fPassReq"), trigger: "blur" },
                          {min: 6, message: $t("p.reg.fPassTrig"), trigger: "blur"},
                          {min: 6, message: $t("p.reg.fPassTrig"), trigger: "change"}]'>
                <el-input v-model="registerForm.password" type="password" :placeholder="$t('p.reg.fPass')"></el-input>
            </el-form-item>
            <el-form-item prop="password_confirmation"
                          :rules='[{required: true, message: $t("p.reg.fPassConfirmReq"), trigger: "blur" },
                          {min: 6, message: $t("p.reg.fPassConfirmTrig"), trigger: "blur"},
                          {min: 6, message: $t("p.reg.fPassConfirmTrig"), trigger: "change"}]'>
                <el-input v-model="registerForm.password_confirmation" type="password" :placeholder="$t('p.reg.fPassConfirm')"></el-input>
            </el-form-item>
        </el-form>
        <div class="w400 mAuto">
            <div class="mb20" style="line-height: 1.5;">
                <p>{{$t('p.reg.agree')}}</p>
                <p><el-checkbox v-model="personalAgree">
                    <router-link target="_blank" to="/dataAgreement">{{$t('p.reg.dataAgreement')}}</router-link>
                </el-checkbox></p>
                <p><el-checkbox v-model="agreementAgree">
                    <a target="_blank" :href="agreement">{{$t('p.reg.agreement')}}</a>
                </el-checkbox></p>
                <hr/>
                <router-link target="_blank" to="/privacyPolicy">{{$t('p.reg.privacyPolicy')}}</router-link>
            </div>
            <div class="alignC">
                <el-button type="primary" v-on:click="sendLogin('registerForm')" :loading="registerLoading">{{$t('p.common.regBtn')}}</el-button>
                <el-button v-on:click="$router.push('/')">{{$t('p.common.cancelBtn')}}</el-button>
            </div>
        </div>
    </div>
</template>
<script>
import {isNumberMethod} from "../mixin";
import agreement from "../../agreement.pdf";

export default {
    data(){
        return{
            registerForm: { email: '', name: '', phone: '', password: '', password_confirmation: ''},
            registerLoading: false,
            personalAgree: false,
            agreementAgree: false,
        }
    },
    computed: {
        agreement() {return agreement;},
        ...mapState(['system'])
    },
    mixins: [isNumberMethod],
    methods: {
        async sendLogin(formName) {
            this.$refs[formName].validate(async valid => {
                if (!valid) {
                    return false;
                }
                if (!this.personalAgree) {
                    this.$message.warning(this.$t('p.reg.needPersonalAgree'));
                    return false;
                }
                if (!this.agreementAgree) {
                    this.$message.warning(this.$t('p.reg.needAgreementAgree'));
                    return false;
                }
                try {
                    this.registerLoading = true;
                    await this.postWithErrors('/register', this.registerForm);
                    //store.commit('setUserInfo', response);
                    this.$router.push('/');
                    this.$alert(this.$t('p.reg.confirmEmailText'), this.$t('p.reg.confirmEmailTitle'), {confirmButtonText: this.$t('p.common.okBtn')});
                } catch (e) {
                    if (e.body) {
                        let msg = '';
                        _.forEach(e.body, (value, key) => {
                            if ("errors" === key) {
                                _.forEach(value, val => {
                                    if (val instanceof Array) {
                                        _.forEach(value, v => {
                                            msg += '<div>'+v+'</div>';
                                        })
                                    }
                                });
                            }
                        });
                        this.$message({
                            dangerouslyUseHTMLString: true,
                            customClass: 'w500',
                            message: msg,
                            type: 'error',
                            duration: 10000,
                            showClose: true
                        });
                    } else {
                        this.$alert(e, this.$t('p.common.error'), {dangerouslyUseHTMLString: true, confirmButtonText: this.$t('p.common.okBtn')});
                    }
                } finally {
                    this.registerLoading = false;
                }
            });
        },
    }
}
</script>
