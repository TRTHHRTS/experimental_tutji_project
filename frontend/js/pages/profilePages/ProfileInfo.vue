<template>
    <div>
        <div v-if="!user.email" class="mb20">
            <p><strong>{{$t('p.pInfo.emailNotSet')}}</strong></p>
            <el-button class="mt10" @click="setEmail">{{$t('p.pInfo.setEmail')}}</el-button>
        </div>
        <div v-if="user.email && !user.email_verified" class="mb20">
            <p><strong>{{$t('p.pInfo.emailNotVerified')}}</strong></p>
            <el-button @click="sendEmail" type="warning" :loading="emailSendLoading">{{$t('p.pInfo.confirm')}}</el-button>
        </div>
        <el-card class="w600 inline-block">
            <div slot="header" class="clearfix">
                <span>{{$t('p.pInfo.info')}}</span>
                <span v-if="!editable" @click="fillUserData" class="internal_link" style="float: right; padding: 3px 0">{{$t('p.pInfo.edit')}}</span>
            </div>
            <el-form :model="newUserData" label-width="150px" label-position="left" ref="changeDataForm">
                <el-form-item v-show="editable" prop="name" :label="$t('p.pInfo.name')"
                              :rules="[{required: true, min: 2, max: 100, message: $t('p.pInfo.nameReq'), trigger: 'change' }]">
                    <el-input v-model="newUserData.name"></el-input>
                </el-form-item>
                <el-form-item v-show="!editable" :label="$t('p.pInfo.name')">
                    <el-input v-bind:value="user.name" disabled></el-input>
                </el-form-item>

                <el-form-item v-show="editable" prop="phone" :label="$t('p.pInfo.phone')">
                    <el-input v-model="newUserData.phone" :maxlength="10" @keypress.native="isNumber">
                        <template slot="prefix">+7</template>
                    </el-input>
                </el-form-item>
                <el-form-item v-show="!editable" :label="$t('p.pInfo.phone')">
                    <el-input v-bind:value="user.phone | formatPhone" disabled>
                        <template slot="prefix">+7</template>
                    </el-input>
                </el-form-item>
                <div v-show="!editable && (!user.phone || !user.is_phone_confirmed)" class="xs-text warningText">
                    <b>Чтобы резервировать места на занятия, нужно ввести и <router-link to="/profile/phoneConfirmation">подтвердить</router-link> номер телефона</b>
                </div>

                <el-form-item v-show="editable" :label="$t('p.pInfo.gender')" prop="gender"
                              :rules="[{ required: true, message: $t('p.pInfo.genderReq'), trigger: 'blur' }]">
                    <el-select v-model="newUserData.gender" :placeholder="$t('p.pInfo.gender')">
                        <el-option :label="$t('p.pInfo.gender1')" :value="0"></el-option>
                        <el-option :label="$t('p.pInfo.gender2')" :value="1"></el-option>
                        <el-option :label="$t('p.pInfo.gender3')" :value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item v-show="!editable" :label="$t('p.pInfo.gender')">
                    <el-input v-bind:value="getUserGender" disabled></el-input>
                </el-form-item>

                <el-form-item v-show="editable" :label="$t('p.pInfo.birthday')" prop="birthday">
                    <el-date-picker v-model="newUserData.birthday" format="dd.MM.yyyy" value-format="yyyy-MM-dd HH:mm:ss" placeholder="Выберите"></el-date-picker>
                </el-form-item>
                <el-form-item v-show="!editable" :label="$t('p.pInfo.birthday')">
                    <el-date-picker :value="user.user_details.birthday" format="dd.MM.yyyy" value-format="yyyy-MM-dd HH:mm:ss" disabled></el-date-picker>
                </el-form-item>

                <el-form-item v-show="editable" class="alignR mb0">
                    <el-button type="primary" @click="sendEditUserData" :loading="changeDataLoading">{{$t('p.common.saveBtn')}}</el-button>
                    <el-button @click="cancelEditProfileData">{{$t('p.common.cancelBtn')}}</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="w300 inline-block" style="height: 350px; vertical-align: top;">
            <div slot="header" class="clearfix">
                <span>{{$t('p.pInfo.socialNetworks')}}</span>
            </div>
            <div v-if="!user.social_accounts || (!user.social_accounts.id_fb && !user.social_accounts.id_google && !user.social_accounts.id_vk)"
                 class="mb10">{{$t('p.pInfo.noAccs')}}
            </div>
            <template v-if="user.social_accounts">
                <a v-if="user.social_accounts.id_fb" class="btn_link" target="_blank" :href="'https://www.facebook.com/' + user.social_accounts.id_fb">Facebook</a><br/>
                <a v-if="user.social_accounts.id_google" class="btn_link" target="_blank" :href="'https://www.plus.google.com/' + user.social_accounts.id_google">Google</a><br/>
                <a v-if="user.social_accounts.id_vk" class="btn_link" target="_blank" :href="'https://www.vk.com/id' + user.social_accounts.id_vk">ВКонтакте</a>
            </template>
            <div class="mt10 xs-text">{{$t('p.pInfo.social')}}</div>
        </el-card>
    </div>
</template>
<style scoped>
</style>
<script>
import {isNumberMethod} from "../../mixin";

export default {
    data() {
        return {
            newUserData: {},
            editable: false,
            changeDataLoading: false,
            emailSendLoading: false
        }
    },
    mixins: [isNumberMethod],
    computed: {
        ...mapState(['user']),
        ...mapGetters([ 'getUserGender' ])
    },
    methods: {
        async setEmail() {
            try {
                const value = await this.$prompt(this.$t('p.pInfo.emailInput'), this.$t('p.pInfo.emailSetting'), {
                    confirmButtonText: this.$t('p.common.okBtn'),
                    cancelButtonText: this.$t('p.common.cancelBtn'),
                    inputPattern: /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/,
                    inputErrorMessage: this.$t('p.pInfo.invalidEmail')
                });
                const userInfo = await this.post('/profile/email', {email: value});
                this.$store.commit('setUserInfo', userInfo);
                this.$message.success(this.$t('p.pInfo.emailSuccess'));
            } catch(e) {}
        },
        async sendEmail() {
            try {
                this.emailSendLoading = true;
                const response = await this.post('profile/emailConfirmation');
                if (response === "0") {
                    this.$message.success(this.$t('p.pInfo.emailSent'));
                }
            } finally {
                this.emailSendLoading = false;
            }
        },
        fillUserData() {
            this.newUserData = {
                name: this.user.name,
                gender: this.user.user_details.gender,
                birthday: this.user.user_details.birthday,
                phone: this.user.phone
            };
            this.editable = true;
        },
        async sendEditUserData() {
            try {
                this.changeDataLoading = true;
                this.$refs["changeDataForm"].validate(async valid => {
                    if (!valid) {
                        return false;
                    }
                    const userInfo = await this.post('/profile/data', this.newUserData);
                    this.$store.commit('setUserInfo', userInfo);
                    this.newUserData = {};
                    this.editable = false;
                    this.$message.success(this.$t('p.pInfo.editSuccess'));
                });
            } catch (e) {
                this.$message.error(this.$t('p.pInfo.editError'));
            } finally {
                this.changeDataLoading = false;
            }
        },
        cancelEditProfileData() {
            this.newUserData = {};
            this.editable = false;
        },
    },
    async mounted() {
        document.title = this.$t('p.pInfo.pageTitle');
    }
}
</script>