<template>
    <div v-if="user">
        <h2 class="alignC">{{$t('p.mesCh.title', {username: username ? username : ""})}}</h2>
        <div class="alignC mb20">
            <el-button v-on:click="$router.push('/messages')" type="default" plain>{{$t('p.common.backBtn')}}</el-button>
            <!--<el-button v-on:click="" type="warning" plain>{{$t('p.common.deleteBtn')}}</el-button>-->
            <!--<el-button v-on:click="" type="danger" plain>{{$t('p.common.spamBtn')}}</el-button>-->
        </div>
        <div v-if="getMessagesLoad" class="alignC">
            <i class="el-icon-loading"></i>
        </div>
        <el-card id="msgList" v-if="messages" class="messages-list">
            <div v-for="message in messages" v-bind:class="{ alignR: !message.from_you }" class="mb5">
                <div class="xs-text">{{message.created_at | formatDateTime}}</div>
                <div>{{ (message.from_you ? $t('p.mesCh.youPrefix') : '') + message.message }}</div>
            </div>
        </el-card>
        <el-form :inline="true" class="w800 mAuto">
            <el-form-item>
                <el-input v-model="answer" class="w300" :placeholder="$t('p.mesCh.yourMessage')"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="sendMessage" icon="el-icon-message" :loading="sendMessageLoad">{{$t('p.common.sendBtn')}}</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<style>
.messages-list{
    width: 800px;
    height: 500px;
    overflow-wrap: break-word;
    overflow-y: scroll;
    white-space: normal;
    margin: 0 auto 10px auto;
}
</style>
<script>
import { mapState } from 'vuex'

export default{
    data() {
        return {
            messages: null,
            username: null,
            user_id: null,
            answer: null,
            getMessagesLoad: false,
            sendMessageLoad: false,
        }
    },
    computed: mapState(['user', 'isModer', 'isAdmin']),
    methods: {
        async sendMessage() {
            if (!this.answer) {
                return;
            }
            await this.post('/messages', {'text': this.answer, 'rcptId': this.user_id});
            this.answer = null;
            await this.getMessages();
            this.scrollToBottom();
        },
        async getMessages() {
            this.getMessagesLoad = true;
            let response = await this.get('/messages/'+this.$route.params.rcptId);
            this.getMessagesLoad = false;
            this.messages = response.messages;
            this.username = response.username;
            this.user_id = response.user_id;
        },
        scrollToBottom() {
            let list = document.getElementById('msgList');
            if (list) {
                let div = list.getElementsByClassName('el-card__body')[0];
                div.scrollTop = div.scrollHeight;
            }
        }
    },
    async mounted() {
        await this.getMessages();
        this.scrollToBottom();
    }
}
</script>
