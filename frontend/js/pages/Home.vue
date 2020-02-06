<template>
    <div>
        <el-card v-if="system && system.has_news" class="news_block">
            <div class="mb10">
                <span>{{$t('p.lastNews')}}</span>
                <el-button v-on:click="$router.push('/news')" style="float: right; padding: 3px 0" type="text">{{$t('p.home.allNews')}}</el-button>
            </div>
            <div class="alignC">
                <i v-if="initDataLoading" class="el-icon-loading mAuto"></i>
            </div>
            <el-collapse v-if="news" accordion>
                <el-collapse-item :title="getTruncatedString(record.title, 50)" :name="record.id" v-for="record in news" :key="record.id">
                    <div>{{record.created_at | formatDate}}</div>
                    <div>{{ getTruncatedString(record.message, 500) }}</div>
                    <div id="actions">
                        <el-button @click="showNews(record)" size="small">{{$t('p.home.more')}}</el-button>
                        <el-button v-if="$store.getters.isOperator" @click="deleteNewsRecord(record.id)" type="warning" size="small" plain icon="el-icon-delete"></el-button>
                    </div>
                </el-collapse-item>
            </el-collapse>
        </el-card>

        <div class="fixed_center home_block">
            <h1 style="font-size: 400%;">{{$t('p.home.title')}}</h1>
            <h1>{{$t('p.home.subtitle')}}</h1>
            <el-form :inline="true" class="alignC">
                <el-form-item>
                    <el-input :placeholder="$t('p.filter.city')" :maxlength="50" v-model="pageFilter.cityName" class="w200 m10"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button v-on:click="$router.push('/filter')" type="danger" class="m10">{{$t('p.home.findLessons')}}</el-button>
                </el-form-item>
            </el-form>
        </div>
        <el-button @click="pauseVideo" icon="el-icon-view" circle class="floatR" style="position: relative; top: 10px; right: 10px;"></el-button>
        <div id="overlay" style="background-color: black;">
            <video autoplay muted loop id="bgVideo">
                <source :src="videoLink" type="video/mp4">
            </video>
        </div>
    </div>
</template>
<style lang="scss" scoped>
    #bgVideo {
        object-fit: cover;
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
    }
    .home_block {
        h1 {
            text-shadow: 1px 1px 2px rgba(136, 136, 136, 0.8), 0 0 1em rgba(100, 100, 100, 0.8);
        }
    }
    .fixobaixo {
        background: rgba(255, 255, 255, 0.8);
        position: fixed;
        width: 100%;
        bottom: 0;

        .dffdfs {
            background: fixed;
        }
    }
    .fixed_center {
        width: 100%;
        position:fixed;
        top: 50%;

        h1, h2, h3 {
            text-align: center;
            color: #EDF2FC;
        }
    }
    .news_block {
        width: 450px;
        float: left;
        opacity: 0.85;
        margin: 10px 10px 10px 0;
        position: relative;
        z-index: 10;

        #actions {
            float: right;
            margin: 10px 10px 10px 0;
        }
    }
</style>
<script>
import video from "../../images/backgrounds/bg.mp4";

export default{
    data(){
        return {}
    },
    computed: {
        overlayClass() {
            return "bg0";
            // TODO вернуть или переделать
            /*
            const min = 1;
            const max = 6;
            return "bg" + (Math.floor(Math.random() * (max - min)) + min);
            */
        },
        videoLink() {
            return video;
        },
        ...mapState(['pupilsRef', 'pageFilter', 'user', 'news', 'system', 'initDataLoading'])
    },
    methods: {
        pauseVideo() {
            const video = document.getElementById("bgVideo");
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        },
        async deleteNewsRecord(id) {
            try {
                await this.$confirm('Вы действительно хотите удалить эту новость?');
                try {
                    if (id !== null) {
                        let result = await this.post('/administrator/deleteNewsRecord', {ID: id});
                        if (result.status === 0) {
                            this.$store.commit('setNews', result.news);
                            this.$alert('Новость успешно удалена', 'Новость удалена');
                        }
                    }
                } catch (error) {
                    this.$alert('Ошибка при удалении новости с идентификатором ' + id, 'Ошибка');
                }
            } catch(e) {
                // ничего не делаем
            }
        },
        showNews(record) {
            this.$alert(record.message, record.title);
        },
        getTruncatedString(str, len) {
            return _.truncate(str, {'length': len})
        }
    }
}
</script>
