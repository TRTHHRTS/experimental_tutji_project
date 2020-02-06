<template>
    <div>
        <i18n path="p.login.title" class="alignC mb10" tag="h1">
            <small place="small">{{$t('p.login.titleSmall')}}</small>
        </i18n>
        <div class="alignC mb20">
            <h4 class="mb10">{{$t('p.login.socialTitle')}}</h4>
            <a href="/redirect/google"><img src="../../images/google.svg" class="pointer" alt="Login with Google" width="32" height="32"/></a>
            <a href="/redirect/facebook"><img src="../../images/facebook.svg" class="pointer" alt="Login with Facebook" width="32" height="32"/></a>
            <a href="/redirect/vkontakte"><img src="../../images/vk.svg" class="pointer" alt="Login with Vkontakte" width="32" height="32"/></a>
        </div>

        <el-form label-position="top" :model="loginForm" class="w300 mAuto" @keyup.enter.native="sendLogin">
            <el-form-item>
                <el-input v-model="loginForm.email" :placeholder="$t('p.login.email')"></el-input>
            </el-form-item>
            <el-form-item class="mb0">
                <el-input v-model="loginForm.password" type="password" :placeholder="$t('p.login.pass')"></el-input>
                <el-button v-on:click="resetPassVis=true" type="text">{{$t('p.login.forgotPass')}}</el-button>
            </el-form-item>
            <el-form-item class="mb0">
                <el-checkbox v-model="loginForm.remember">{{$t('p.login.rememberMe')}}</el-checkbox>
            </el-form-item>
            <el-form-item class="alignC mb0">
                <el-button type="primary" @click="sendLogin" :loading="loginLoading">{{$t('p.login.loginBtn')}}</el-button>
                <el-button v-on:click="$router.push('/')">{{$t('p.common.cancelBtn')}}</el-button>
            </el-form-item>
            <el-form-item class="alignC">
                <el-button type="text" v-on:click="$router.push('/register')"><strong>{{$t('p.common.regBtn')}}</strong></el-button>
            </el-form-item>
        </el-form>

        <el-dialog title="Восстановление пароля" :visible.sync="resetPassVis">
            <el-form :model="resetPassForm" ref="resetForm" :inline="true" class="alignC">
                <el-form-item label="Адрес электронной почты" prop="email"
                              :rules='[{required: true, message: $t("p.login.resetPassEmailReq"), trigger: "blur"},
                                {type: "email", message: $t("p.login.resetPassEmailTrig"), trigger: "blur,change"}]'>
                    <el-input v-model="resetPassForm.email"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="forgotPassword('resetForm')">{{$t('p.login.resetSend')}}</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
export default {
    data() {
        return {
            errors: null,
            loginForm: {
                email: '',
                password: '',
                remember: true
            },
            resetPassVis: false,
            resetPassForm: {
                email: ''
            },
            loginLoading: false,
        }
    },
    computed: mapState(['user']),
    methods: {
        async forgotPassword(formName) {
            this.$refs[formName].validate(async valid => {
                if (!valid) {
                    return false;
                }
                let result = await this.post('password/email', {email: this.resetPassForm.email});
                if (result.status === 4) {
                    return this.$alert(result.email, this.$t('p.common.error'));
                } else if (result.status === 0) {
                    this.resetPassVis = false;
                    this.resetPassForm.email = '';
                    return this.$alert(this.$t('p.login.resetSentText'), this.$t('p.login.resetSent'));
                }
            });
        },
        async sendLogin() {
            try {
                this.loginLoading = true;
                let response = await this.post('/login', this.loginForm);
                if(response.status && response.status === 4) {
                    return this.$alert(response.data.content, response.data.title, {confirmButtonText: this.$t('p.common.okBtn')});
                }
                await this.$root.getNewToken();
                this.$store.commit('setUserInfo', response);
                this.$router.push('/');
                this.$message.success(this.$t('p.login.successLogin'));
            } finally {
                this.loginLoading = false;
            }
        }
    },
    created() {
        if (this.user) {
            this.$router.push('/');
        }
    }
}
</script>
