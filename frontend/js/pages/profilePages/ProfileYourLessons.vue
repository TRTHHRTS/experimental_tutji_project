<template>
    <div>
        <h3 class="alignC">{{ yourReserves.length > 0 ? $t('p.pYourLessons.title1') : $t('p.pYourLessons.title2') }}</h3>
        <el-table :data="yourReserves" stripe class="mb10 mAuto w100p" v-loading="yourReservesLoading"
                  :empty-text="$t('p.pYourLessons.empty')">
            <el-table-column :label="$t('p.pYourLessons.count')" width="150" align="center" header-align="center">
                <template slot-scope="scope">
                    <span><strong>{{scope.row.user_name}} </strong></span>
                    <span>{{$t('p.pYourLessons.countText', {count: scope.row.count})}}</span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pYourLessons.date')" width="200" align="center" header-align="center">
                <template slot-scope="scope">
                    <i class="el-icon-date"></i>
                    <span>{{scope.row.ldate | formatDate}}{{$t('p.pYourLessons.dateText')}}{{scope.row.ltime}}</span>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pYourLessons.status')" width="350" align="center" header-align="center">
                <template slot-scope="scope">
                    <div><i class="el-icon-bell"></i>{{reserveStatuses[scope.row.reserve_status]}}</div>
                    <el-button @click="cancelRecord(scope.row.rlid, false)" type="warning" size="mini" plain round>{{$t('p.pYourLessons.cancelRecord')}}</el-button>
                    <el-button @click="cancelRecord(scope.row.rlid, true)" type="danger" size="mini" plain round>{{$t('p.pYourLessons.cancelLesson')}}</el-button>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pYourLessons.lessonName')" min-width="200" align="center" header-align="center">
                <template slot-scope="scope">
                    <strong><span style="word-break: break-word" class="btn_link" v-on:click="$router.push('/lesson/'+scope.row.lesson_id)">{{scope.row.name}}</span></strong>
                </template>
            </el-table-column>
            <el-table-column :label="$t('p.pYourLessons.duration')" width="150" align="center" header-align="center">
                <template slot-scope="scope">
                    <span>{{scope.row.duration | formatDuration}}</span>
                </template>
            </el-table-column>
        </el-table>
        <h3 class="alignC">{{ lessons.length > 0 ? $t('p.pYourLessons.yourLessons') : $t('p.pYourLessons.noLessons') }} <i v-show="lessonsLoading" class="el-icon-loading"></i></h3>
        <el-card class="m5 w500 inline-block"
                 v-bind:class="STATUSES_COLOR[lesson.status]"
                 style="vertical-align: top;"
                 v-for="lesson in lessons" :key="lesson.id">
            <div slot="header" class="clearfix">
                <div class="xs-text floatR">
                    <div id="lessonStatusId" class="alignR">{{STATUSES[lesson.status]}} {{$t('p.pYourLessons.statusLesson')}}</div>
                    <span>{{$t('p.pYourLessons.created')}} {{lesson.created_at | formatDateTime}}</span>
                </div>
                <span v-if="lesson.status !== 3 && lesson.status !== 2" class="btn_link" style="word-break: break-word"
                      v-on:click="$router.push('/lesson/' + lesson.id)">
                    <b>{{lesson.name}} <template v-if="lesson.aging_name">({{lesson.aging_name}})</template></b>
                </span>
                <span v-else><b>{{lesson.name}} <template v-if="lesson.aging_name">({{lesson.aging_name}})</template></b></span>
            </div>
            <div v-if="lesson.short_desc"><span style="word-break: break-word">{{lesson.short_desc}}</span></div>
            <div v-else>{{$t('p.pYourLessons.noShortDesc')}}</div>
            <div class="mt5 alignR">
                <el-button v-if="lesson.status !== 1" @click="deleteLesson(lesson.name, lesson.id)"
                           type="danger" size="mini" icon="el-icon-delete" circle plain>
                </el-button>
                <el-button v-if="lesson.status !== 0" @click="$router.push('/lesson/' + lesson.id)"
                           type="primary" size="mini" icon="el-icon-search" circle>
                </el-button>
                <el-button v-if="lesson.status === 0" @click="$router.push('/edit/' + lesson.id)"
                           type="primary" size="mini" icon="el-icon-edit" plain circle>
                </el-button>
                <el-button @click="copyLesson(lesson.name, lesson.id)"
                           type="info" size="mini" icon="el-icon-tickets" circle plain>
                </el-button>
            </div>
        </el-card>
    </div>
</template>
<script>
export default {
    data(){
        return {
            lessons: [],
            yourReserves: [],
            yourReservesLoading: true,
            lessonsLoading: true
        }
    },
    computed: mapState(['STATUSES', 'STATUSES_COLOR', 'reserveStatuses']),
    methods: {
        ...mapActions(['loadCommonReserves', 'commonCancelRecord']),
        async loadLessons() {
            try {
                this.lessons = [];
                this.lessonsLoading = true;
                this.lessons = await this.get('/api/profile/lessons');
            } finally {
                this.lessonsLoading = false;
            }
        },
        async cancelRecord(reserveId, cancelAll) {
            await this.commonCancelRecord([this, reserveId, true, cancelAll]);
            this.loadLessons();
            this.yourReserves = (await this.loadCommonReserves([this, true, null, true])).data;
        },
        async deleteLesson(name, id) {
            try {
                await this.$confirm(this.$t('p.pYourLessons.deleteText', {name: name}), this.$t('p.pYourLessons.deleteTitle'), {
                    confirmButtonText: this.$t('p.pYourLessons.delete'),
                    cancelButtonText: this.$t('p.common.cancelBtn'),
                    type: 'warning'
                });
                const response = await this.post('/lesson/delete', {ID: id});
                if (response.status === 0) {
                    this.$message.success(this.$t('p.pYourLessons.deleteSuccess'));
                    return this.loadLessons();
                }
            } catch (e) {
                console.debug(e);
            }
        },
        async copyLesson(name, id) {
            try {
                await this.$confirm(this.$t('p.pYourLessons.copyText', {name: name}), this.$t('p.pYourLessons.copyTitle'), {
                    confirmButtonText: this.$t('p.pYourLessons.copyBtn'),
                    cancelButtonText: this.$t('p.common.cancelBtn'),
                    type: 'info'
                });
                const response = await this.post('/lesson/copy', {lessonId: id});
                if (response.status === 0) {
                    this.$router.push("/edit/" + response.id);
                }
            } catch (e) {
                console.debug(e);
            }
        }
    },
    created() {
        document.title = this.$t('p.pYourLessons.pageTitle');
    },
    async mounted() {
        await this.$root.waitAppDataLoading();
        // загружаем в фоне занятия
        this.loadLessons();
        try {
            this.yourReserves = (await this.loadCommonReserves([this, true, null, true])).data;
        } finally {
            this.yourReservesLoading = false;
        }
    },
}
</script>