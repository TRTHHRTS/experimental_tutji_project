<template>
    <div>
        <h3 class="mb10">{{$t('p.pPhone.title')}}</h3>
        <template v-if="!codeSent">
            <div class="mb10">{{$t('p.pPhone.info')}} <strong>+7 {{user.phone | formatPhone}}</strong></div>
            <el-button v-if="!codeSent" @click="sendCode" type="primary" size="small" plain>{{$t('p.pPhone.sendCode')}}</el-button><br/>
        </template>
        <template v-else>
            <div>{{$t('p.pPhone.codeSent')}} <strong>+7 {{user.phone | formatPhone}}</strong></div>
            <el-input v-model="code" class="w300 p10" :placeholder="$t('p.pPhone.typeCode')"></el-input><br/>
            <div class="mb10">{{$t('p.pPhone.info2')}}</div>
            <el-button @click="confirmPhoneNumber" type="primary" :disabled="!code">{{$t('p.pPhone.confirm')}}</el-button>
            <el-button @click="sendCode">{{$t('p.pPhone.sendNewCode')}}</el-button>
        </template>
    </div>
</template>
<script>
export default {
    data(){
        return {
            codeSent: false,
            code: null
        }
    },
    computed: mapState(['user']),
    methods: {
        async sendCode() {
            let result = await this.post('sms/sendVerifyCode');
            if (result.status === 0) {
                this.codeSent = true;
            } else {
                this.codeSent = !!result.codeSent;
                let msg = result.message ? result.message : this.$t('p.pPhone.sendError');
                this.$alert(msg, this.$t('p.common.error'), {type: "error"});
            }
        },
        async confirmPhoneNumber() {
            let result = await this.post('sms/verifyCode', {'CODE': this.code });
            if (result.status === 0) {
                this.$store.commit('setPhoneConfirmation', true);
                this.$alert(this.$t('p.pPhone.confirmSuccessText'), this.$t('p.pPhone.confirmSuccess'), {type: "success"});
                this.$router.push('/profile');
            } else {
                let msg = result.message ? result.message : this.$t('p.pPhone.codeSendError');
                this.$alert(msg, this.$t('p.common.error'), {type: "error"});
            }
        }
    },
    async mounted() {
        document.title = this.$t('p.pPhone.pageTitle');
        if (!this.user || !this.user.phone || this.user.is_phone_confirmed) {
            this.$router.push("/profile");
        }
    }
}
</script>