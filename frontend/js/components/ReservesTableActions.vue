<template>
    <div>
        <template v-if="item.closed">
            <p v-html="$t('p.comps.rta.closed')"></p>
            <p v-if="item.reason" v-html="$t('p.comps.rta.reason', {reason: item.reason})"></p>
        </template>
        <template v-else>
            <el-button v-if="item.reserve_status === 10 && !isTeacher" @click="sendTeacherNotCome(item)" type="warning" size="mini" plain>
                {{$t('p.comps.rta.thisTeacherNotCome')}}
            </el-button>
            <template v-if="item.reserve_status === 21">
                <template v-if="isTeacher">
                    <el-button v-on:click="notAgree(item)" type="danger" size="mini" plain>{{$t('p.comps.rta.come')}}</el-button>
                    <el-button v-on:click="confirmNotCome(item)" type="success" size="mini" plain>{{$t('p.comps.rta.notCome')}}</el-button>
                </template>
                <template v-else>
                    <span>{{$t('p.comps.rta.waitAdmin')}}</span>
                </template>
            </template>
            <template v-if="item.reserve_status === 42">
                <template v-if="isTeacher">
                    <p>{{$t('p.comps.rta.answerToQuestion')}}</p>
                </template>
                <template v-else>
                    <p>{{$t('p.comps.rta.waitAdmin')}}</p>
                </template>
            </template>
            <template v-if="item.reserve_status === 43">
                <template v-if="isTeacher">
                    <p>{{$t('p.comps.rta.waitAdmin')}}</p>
                </template>
                <template v-else>
                    <p>{{$t('p.comps.rta.answerToQuestion')}}</p>
                </template>
            </template>
            <template v-if="(item.reserve_status === 42 && isTeacher) || (item.reserve_status === 43 && !isTeacher)">
                <el-button size="mini" @click="answerToAdmins(item)" :loading="answerLoading">Ответить</el-button>
            </template>
            <template v-if="item.reserve_status === 41">
                <div class="mb10">{{$t('p.comps.rta.help')}}</div>
                <el-button :loading="contactBtnLoading" v-on:click="contactToAdministration(item)" type="danger" size="mini" plain>{{$t('p.comps.rta.supportConnection')}}</el-button>
            </template>
        </template>
    </div>
</template>
<script>
export default {
    props: {
        item: {type: Object, required: true},
        isTeacher: {type: Boolean, default: false}
    },
    data() {
        return {
            answerLoading: false,
            contactBtnLoading: false,
        }
    },
    methods: {
        async sendTeacherNotCome(record) {
            try {
                await this.$confirm(this.$t('p.comps.rta.userNotCome', {name: record.user_name}), this.$t('p.comps.rta.question'), {
                    confirmButtonText: this.$t('p.common.yes'), cancelButtonText: this.$t('p.common.cancelBtn'), type: 'warning'
                });
                let response = await this.post('/profile/actions/userNotCome', {reserveId: record.rlid});
                if (response.status === 0)  {
                    this.$message.success(this.$t('p.comps.rta.changed'));
                    this.$emit('load', this.isTeacher);
                }
            } catch (e) {
                console.debug(e);
            }
        },
        async notAgree(record) {
            try {
                let dlg = await this.$prompt(this.$t('p.comps.rta.openPari'), this.$t('p.common.warning'),
                    {confirmButtonText: this.$t('p.comps.rta.confirm'), cancelButtonText: this.$t('p.common.cancelBtn'),});
                let params = {
                    reserveId: record.rlid,
                    message: dlg.value
                };
                let response = await this.post('/reserves/userNotAgree', params);
                if (response.status === 0) {
                    this.$message.success(this.$t('p.comps.rta.pariOpen'));
                    this.$emit('load');
                }
            } catch(e) {
                console.debug(e);
            }
        },
        async confirmNotCome(record) {
            try {
                await this.$confirm(this.$t('p.comps.rta.confirmNotPass', {lessonName: record.name}), this.$t('p.comps.rta.question'), {
                    confirmButtonText: this.$t('p.common.yes'), cancelButtonText: this.$t('p.common.no'), type: 'warning'
                });
                let response = await this.post('/reserves/confirmNotCome', {reserveId: record.rlid});
                if (response.status === 0)  {
                    this.$message.success(this.$t('p.comps.rta.changed'));
                    this.$emit('load');
                }
            } catch (e) {
                console.debug(e);
            }

        },
        async answerToAdmins(record) {
            try {
                this.answerLoading = true;
                let question = await this.get('/reserves/last_reserves_conversation', {params: {reserveId: record.rlid}});
                let dlg = await this.$prompt("Вопрос администрации: " + question, this.$t('p.comps.rta.supportConnection'),
                    {confirmButtonText: this.$t('p.comps.rta.ready'), cancelButtonText: this.$t('p.common.cancelBtn'), inputPlaceholder: "Введите ваш ответ"});
                let params = {
                    reserveId: record.rlid,
                    message: dlg.value
                };
                let response = await this.post('/reserves/answerToAdministration', params);
                if (response.status === 0) {
                    this.$message.success(this.$t('p.comps.rta.saved'));
                    this.$emit('load');
                }
            } catch (e) {
                console.debug("Canceled");
            } finally {
                this.answerLoading = false;
            }
        },
        async contactToAdministration(record) {
            try {
                this.contactBtnLoading = true;
                let messageText = this.$t('p.comps.rta.askQuestion');
                let messageTitle = this.$t('p.comps.rta.supportConnection');
                let dlg = await this.$prompt(messageText, messageTitle, {confirmButtonText: this.$t('p.comps.rta.ready'), cancelButtonText: this.$t('p.common.cancelBtn')});
                let params = {
                    reserveId: record.rlid,
                    message: dlg.value,
                    contact: true
                };
                let response = await this.post('/reserves/answerToAdministration', params);
                if (response.status === 0) {
                    this.$message.success(this.$t('p.comps.rta.saved'));
                    this.$emit('load');
                }
            } catch(e) {
                // TODO Ничего не делаем?
            } finally {
                this.contactBtnLoading = false;
            }
        }
    },
}
</script>
