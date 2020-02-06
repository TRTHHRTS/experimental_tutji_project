<template>
    <div>
        <el-card class="m5 inline-block" style="vertical-align: top;">
            <div class="mb10"><strong>{{$t('p.pSettings.title')}}</strong></div>
            <div v-if="!user.has_password" class="mb5 xs-text" v-html="$t('p.pSettings.info')"></div>
            <el-button @click="sendPassword" :loading="isSecurityRequest" type="warning" plain>{{ user.has_password ? $t('p.pSettings.changePass') : $t('p.pSettings.setPass') }}</el-button>
            <div class="mt10 mb10"><strong>{{$t('p.pSettings.avatar')}}</strong></div>
            <img :src="user.user_details.photo_url" width="300" height="300" class="avatar"/>
            <el-upload action="/profile/avatar" :headers="uploadHeaders" :show-file-list="false"
                       :on-success="handleAvatarSuccess" :on-error="handleAvatarError" name="AVATAR">
                <el-button :loading="avatarLoading" type="success" plain>{{$t('p.pSettings.newAvatar')}}</el-button>
            </el-upload>
        </el-card>
        <el-card class="m5 w600 inline-block" style="vertical-align: top;">
            <div slot="header" class="clearfix">
                <span>{{$t('p.pSettings.notifications')}}</span>
                <i class="el-icon-loading ml20" v-if="isNotifyRequest"></i>
            </div>
            <template v-if="user.email && user.email_verified">
                <div><el-switch v-model="user.settings.notify_new_messages" :active-text="$t('p.pSettings.newMessages')"
                                class="mb10" @change="changeNotify('notify_new_messages')" :disabled="isNotifyRequest"></el-switch></div>
                <div><el-switch v-model="user.settings.notify_new_lesson_reviews" :active-text="$t('p.pSettings.newLessonReviews')"
                                class="mb10" @change="changeNotify('notify_new_lesson_reviews')" :disabled="isNotifyRequest"></el-switch></div>
                <div><el-switch v-model="user.settings.notify_scheduled_lessons" :active-text="$t('p.pSettings.newReserveSchedule')"
                                class="mb10" @change="changeNotify('notify_scheduled_lessons')" :disabled="isNotifyRequest"></el-switch></div>
            </template>
            <template v-else>
                <span><strong>Уведомления можно настроить только при заполненном и подтвержденном email.</strong></span>
            </template>
        </el-card>
    </div>
</template>
<style scoped>
.avatar {
    object-fit: contain;
    border: 1px solid #f3f3f3;
}
</style>
<script>
export default {
    data() {
        return {
            newAvatar: null,
            avatarLoading: false,
            single: null,

            isNotifyRequest: false,
            isSecurityRequest: false
        }
    },
    computed: {
        ...mapState(['user', 'system']),
        uploadHeaders() {
            return {'X-CSRF-TOKEN': window.token};
        }
    },
    methods: {
        handleAvatarSuccess(res, file) {
            this.avatarLoading = false;
            if (res) {
                // немного хака: для того, чтобы обновить картинку на странице
                this.$store.commit('setUserAvatar', res + "?t=" + new Date().getTime());
                this.$message.success(this.$t('p.pSettings.newAvatarSet'));
            }
        },
        handleAvatarError(err, file, fileList) {
            this.avatarLoading = false;
            if (err.status === 422 || err.status === 500) {
                return this.$alert(err.message, this.$t('p.common.error'), {confirmButtonText: this.$t('p.common.closeBtn')});
            }
            this.$message.error(this.$t('p.pSettings.newAvatarError'));
        },
        async sendPassword() {
            this.isSecurityRequest = true;
            let response = await this.post('/password/email', {email: this.user.email});
            if (response.status === 0) {
                this.$alert('Письмо с инструкциями отправлено вам на почту', 'Проверьте почту', {
                    confirmButtonText: 'Закрыть',
                    callback: action => {
                        this.isSecurityRequest = false;
                    }
                });
            }
        },
        async forgotPassword() {
            this.isSecurityRequest = true;
            let response = await this.post('/password/email', {email: this.user.email});
            if (response.status === 0) {
                this.$alert(this.$t('p.pSettings.mailInstruction'), this.$t('p.pSettings.checkEmail'), {
                    confirmButtonText: this.$t('p.common.closeBtn'),
                    callback: action => {
                        this.isSecurityRequest = false;
                    }
                });
            }
        },
        async changeNotify(id) {
            try {
                this.isNotifyRequest = true;
                await this.post('/profile/changeNotify', {'notifyId': id});
            } catch (e) {

            } finally {
                this.isNotifyRequest = false;
            }
        }
    }
}
</script>