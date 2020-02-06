<template>
    <div>
        <h1 class="alignC">{{$t('p.feedback.title')}}</h1>
        <el-form :model="feedback" ref="feedbackForm" label-width="120px" class="mAuto w600" label-position="top">
            <el-form-item :label="$t('p.feedback.content')" prop="content"
                          :rules='[{ required: true, message: $t("p.feedback.contentReq"), trigger: "blur" }]'>
                <el-input type="textarea" v-model="feedback.content" :maxlength="2000"></el-input>
            </el-form-item>
            <el-form-item :label="$t('p.feedback.email')" prop="userEmail"
                          :rules='[{type: "email", message: $t("p.feedback.emailReq"), trigger: "blur,change"}]'>
                <el-input v-model="feedback.userEmail"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm">{{$t('p.common.sendBtn')}}</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
export default{
    data() {
        return {
            feedback: {
                content: null,
                userEmail: null
            }
        }
    },
    methods: {
        async submitForm() {
            try {
                this.$refs['feedbackForm'].validate(async valid => {
                    if (!valid) {
                        return false;
                    }
                    const response = await this.post('/feedback', this.feedback);
                    if (response.status === 0) {
                        this.feedback = {content: null, userEmail: null};
                        this.$alert(this.$t('p.feedback.feedbackSuccess'), this.$t('p.feedback.feedbackSuccessText'), {confirmButtonText: 'OK'});
                    }
                });
            } catch (e) {
            }
        },
    }
}
</script>
