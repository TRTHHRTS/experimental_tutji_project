<template>
    <div>
        <h1 class="alignC">{{$t('p.lastNews')}}</h1>
        <div class="alignC" v-if="loading">
            <i class="el-icon-loading"></i>
        </div>
        <el-card v-for="record in news" :key="record.id" class="news_card" :class="'news_important_' + record.importance">
            <div class="alignR">{{record.created_at | formatDate}}</div>
            <h3><strong>{{record.title}}</strong></h3>
            <div>{{record.message}}</div>
        </el-card>
    </div>
</template>
<style>
.news_card {
    width: 800px;
    margin: 10px auto 0 auto;
}
</style>
<script>
export default{
    data(){
        return {
            news: null,
            loading: true
        }
    },
    async mounted() {
        this.news = await this.get('/news');
        this.loading = false;
    }
}
</script>
