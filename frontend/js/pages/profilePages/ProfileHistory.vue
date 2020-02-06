<template>
    <div>
        <div class="profile-history-panel">
            <h3 class="alignC">{{$t('p.pHistory.title')}}</h3>
            <el-checkbox v-model="paginatorReserves.showClosed" @change="loadReserves">{{$t('p.pHistory.showClosed')}}</el-checkbox>
            <el-table :data="reserves" stripe :empty-text="$t('p.common.recordsNotFound')" v-loading="reservesLoading">
                <el-table-column type="expand">
                    <template slot-scope="scope">
                        <reserves-table-actions :item="scope.row" @load="loadReserves"></reserves-table-actions>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.pHistory.recommend')" width="140" align="center" header-align="center">
                    <template slot-scope="scope">
                        <template v-if="!findInArrayById(user.recommendations, scope.row.lesson_id)">
                            <el-popover placement="right" :title="$t('p.pHistory.recommend1')" width="300" trigger="hover"
                                        :content="$t('p.pHistory.notRecommendYet')">
                                <el-button v-on:click="recommendLesson(scope.row.lesson_id)" slot="reference" type="primary" size="small" icon="el-icon-star-off" plain circle></el-button>
                            </el-popover>
                        </template>
                        <template v-if="findInArrayById(user.recommendations, scope.row.lesson_id)">
                            <el-popover placement="right" :title="$t('p.pHistory.cancelRecommend')" width="300" trigger="hover"
                                        :content="$t('p.pHistory.alreadyRecommend')">
                                <el-button v-on:click="notRecommendLesson(scope.row.lesson_id)" slot="reference" type="warning" size="small" icon="el-icon-star-on" plain circle></el-button>
                            </el-popover>
                        </template>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.pHistory.status')" width="220">
                    <template slot-scope="scope">
                        <i class="el-icon-bell"></i>
                        <span>{{reserveStatuses[scope.row.reserve_status]}}</span>
                    </template>
                </el-table-column>
                <el-table-column align="center" header-align="center" width="40">
                    <template slot-scope="scope">
                        <i v-if="scope.row.closed" class="el-icon-remove"></i>
                    </template>
                </el-table-column>
                <el-table-column min-width="300" prop="name" :label="$t('p.pHistory.lessonName')" header-align="center"></el-table-column>
                <el-table-column :label="$t('p.pHistory.teacher')" prop="user_name" align="center" header-align="center"></el-table-column>
                <el-table-column :label="$t('p.pHistory.date')" width="160" align="center" header-align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.ldate | formatDate}} в {{scope.row.ltime}} ({{scope.row.duration | formatDuration}})</span>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.pHistory.yourReview')" min-width="200" align="center" header-align="center">
                    <template slot-scope="scope">
                        <el-button v-if="!scope.row.review_id" @click="openReviewDialog(scope.row.lesson_id)" type="info" size="small" plain>{{$t('p.pHistory.leaveReview')}}</el-button>
                        <div v-else>
                            <div>{{ scope.row.message }}</div>
                            <el-rate v-model="scope.row.rating" disabled></el-rate>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <el-pagination v-if="reserves.length > 0" @current-change="loadReserves"
                           layout="total, prev, pager, next" class="alignC" :current-page.sync="paginatorReserves.currentPage"
                           :page-size="paginatorReserves.pageSize" :total="paginatorReserves.total">
            </el-pagination>

            <h3 class="alignC">{{$t('p.pHistory.title2')}}</h3>
            <el-checkbox v-model="paginatorYourLessonReserves.showClosed" @change="loadYouLessonReserves">
                {{$t('p.pHistory.showClosed2')}}
            </el-checkbox>
            <el-table :data="yourLessonReserves" stripe v-loading="yourLessonReservesLoading" :empty-text="$t('p.common.recordsNotFound')">
                <el-table-column type="expand">
                    <template slot-scope="scope">
                        <reserves-table-actions :item="scope.row" :isTeacher="true" @load="loadYouLessonReserves"></reserves-table-actions>
                    </template>
                </el-table-column>
                <el-table-column prop="name" :label="$t('p.pHistory.lessonName')" align="center" header-align="center"></el-table-column>
                <el-table-column align="center" header-align="center" width="40">
                    <template slot-scope="scope">
                        <i v-if="scope.row.closed" class="el-icon-remove"></i>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.pHistory.status')" align="center" header-align="center">
                    <template slot-scope="scope">
                        <i class="el-icon-bell"></i>
                        <span>{{reserveStatuses[scope.row.reserve_status]}}</span>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.pHistory.pupil')" align="center" header-align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.user_name}} ({{scope.row.count}})</span>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.pHistory.date')" align="center" header-align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.ldate | formatDate}} в {{scope.row.ltime}} ({{scope.row.duration | formatDuration}})</span>
                    </template>
                </el-table-column>
            </el-table>
            <el-pagination v-if="yourLessonReserves.length > 0" @current-change="loadYouLessonReserves"
                           layout="total, prev, pager, next" class="alignC" :current-page.sync="paginatorYourLessonReserves.currentPage"
                           :page-size="paginatorYourLessonReserves.pageSize" :total="paginatorYourLessonReserves.total">
            </el-pagination>
        </div>
        <!-- Диалог отзыва -->
        <el-dialog :title="$t('p.pHistory.review')" :visible.sync="reviewDlgVis">
            <el-form :model="newReview" ref="revForm" label-position="top">
                <el-form-item :label="$t('p.pHistory.reviewText')" prop="message"
                              :rules='[{ required: true, message: $t("p.pHistory.reviewTextReq"), trigger: "blur" }]'>
                    <el-input v-model="newReview.message" type="textarea"></el-input>
                </el-form-item>
                <el-form-item :label="$t('p.pHistory.rating')" prop="rating"
                              :rules='[{ required: true, type: "number", min: 1, max: 5, message: $t("p.pHistory.ratingReq"), trigger: "blur" }]'>
                    <el-rate v-model="newReview.rating"></el-rate>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="closeDialog">{{$t('p.common.cancelBtn')}}</el-button>
                <el-button type="primary" @click="saveReview" :loading="reviewSaving">{{$t('p.common.saveBtn')}}</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<style scoped lang="scss">
.profile-history-panel {
    table {
        min-width: 800px;
    }
    margin: 0 50px;
}
</style>
<script>
import ReservesTableActions from '../../components/ReservesTableActions.vue';

export default {
    data() {
        return {
            newReview: {},
            reviewSaving: false,
            reviewDlgVis: false,

            reservesLoading: true,
            paginatorReserves: {currentPage: 1, pageSize: 10, showClosed: true, total: 0},
            reserves: [],
            yourLessonReservesLoading: true,
            paginatorYourLessonReserves: {currentPage: 1, pageSize: 10, showClosed: true, total: 0},
            yourLessonReserves: [],
        }
    },
    computed: mapState(['user', 'system', 'reserveStatuses']),
    components: {ReservesTableActions},
    methods: {
        ...mapActions(['loadCommonReserves']),
        async recommendLesson(lessonId) {
            let response = await this.post('/lesson/recommend', {"ID": lessonId});
            if (response.result === 0) {
                this.$store.commit('setRecommendations', response.recommendations);
                return this.$message.success(this.$t('p.pHistory.recommendSuccess'));
            }
            if (response.result === 1) {
                return this.$message.warning(this.$t('p.pHistory.alreadyRecommend'));
            }
            return this.$message.error(this.$t('p.pHistory.recommendError'));
        },
        async notRecommendLesson(lessonId) {
            let response = await this.post('/lesson/notRecommend', {"ID": lessonId});
            if (response.result === 0) {
                this.$store.commit('setRecommendations', response.recommendations);
                return this.$message.success(this.$t('p.pHistory.recommendCanceled'));
            }
            if (response.result === 1) {
                return this.$message.warning(this.$t('p.pHistory.notRecommendYet'));
            }
            return this.$message.error(this.$t('p.pHistory.recommendError'));
        },
        openReviewDialog(id) {
            this.newReview = {
                lessonId: id,
                message: null,
                rating: null
            };
            this.reviewDlgVis = true;
        },
        closeDialog() {
            this.reviewDlgVis = false;
            this.newReview = {};
        },
        async saveReview() {
            try {
                this.reviewSaving = true;
                await this.$refs.revForm.validate();
                let response = await this.post('/lessonReview', this.newReview);
                this.reviewDlgVis = false;
                if (response.status === 0) {
                    this.$message.success(this.$t('p.pHistory.reviewSaved'));
                    await this.loadReserves();
                } else {
                    this.$message.error(this.$t('p.pHistory.reviewError'));
                }
                this.newReview = {};
            } catch (e) {
                console.debug("Диалог сохранения отзыва: " + e);
            } finally {
                this.reviewSaving = false;
            }
        },
        async loadReserves() {
            try {
                this.reservesLoading = true;
                const resResponse = await this.loadCommonReserves([this, false, this.paginatorReserves, false]);
                if (resResponse.status !== 4) {
                    this.reserves = resResponse.data;
                    this.paginatorReserves.total = resResponse.total;
                }
            } finally {
                this.reservesLoading = false;
            }
        },
        async loadYouLessonReserves() {
            try {
                this.yourLessonReservesLoading = true;
                const tourResResponse = await this.loadCommonReserves([this, true, this.paginatorYourLessonReserves, false]);
                if (tourResResponse.status !== 4) {
                    this.yourLessonReserves = tourResResponse.data;
                    this.paginatorYourLessonReserves.total = tourResResponse.total;
                }
            } finally {
                this.yourLessonReservesLoading = false;
            }
        }
    },
    async created() {
        document.title = this.$t('p.pHistory.pageTitle');
        await this.$root.waitAppDataLoading();
        await this.loadReserves();
        await this.loadYouLessonReserves();
    }
}
</script>
