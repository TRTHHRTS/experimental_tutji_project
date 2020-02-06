<template>
    <div>
        <div>
            <el-card class="m5 w400 inline-block" style="vertical-align: top;">
                <el-date-picker v-model="LESSON_DATE" :placeholder="$t('p.filter.date')"
                                class="mb10 w100p" format="dd.MM.yyyy"></el-date-picker>
                <el-autocomplete :placeholder="$t('p.filter.city')" v-model="pageFilter.cityName" class="mb10"
                          :fetch-suggestions="findCities" :maxlength="50"></el-autocomplete>
                <el-select v-model="pageFilter.pupils" :placeholder="$t('p.filter.count')" clearable class="mb10 w100p">
                    <el-option v-for="option in pupilsRef" :key="option.id" :label="option.val" :value="option.id"></el-option>
                </el-select>
                <el-input :placeholder="$t('p.filter.name')" v-model="pageFilter.lessonName" :maxlength="200" class="mb10"></el-input>
                <div class="mb5">
                    <span v-on:click="selectAll()" class="xs-text internal_link">{{ pageFilter.selectAll ? $t('p.filter.resetCats') : $t('p.filter.selectCats') }}</span>
                </div>
                <div class="mb10">
                    <el-select v-model="pageFilter.CATS" multiple :placeholder="$t('p.filter.chooseCats')" class="w100p">
                        <el-option v-for="item in categories" :key="item.id" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                    <div v-if="pageFilter.CATS.length === 0" class="alignC xs-text">{{$t('p.filter.noteCats')}}</div>
                </div>
                <el-button v-on:click="clearFilter()" plain>Очистить</el-button>
                <el-button v-on:click="findLessons(false)" type="danger" :loading="findLessonsLoading">{{$t('p.filter.findLessonsBtn')}}</el-button>
            </el-card>
            <div id="map_canvas" style="width: 50%; min-height: 400px; max-height: 600px; display: inline-block;"></div>
        </div>
        <div class="alignC mAuto">
            <template v-if="searchedLessons">
                <div v-if="searchedLessons.length > 0">
                    <div class="w100p"><span class="md-title">{{$t('p.filter.fonud', {count: searchedLessons.length})}}</span></div>
                    <lesson-card v-for="item in searchedLessons" :key="item.id" :lesson="item" :showImage="true" width="320" @openProfile="openUserProfileDialog"></lesson-card>
                </div>
                <el-button v-if="searchedLessons.length > 0" v-on:click="findLessons(true)" type="primary" plain :loading="findLessonsLoading">
                    {{$t('p.filter.more')}}
                </el-button>
                <div v-if="searchedLessons.length === 0">
                    <h4>{{$t('p.filter.noLes1')}}</h4>
                    <h5>{{$t('p.filter.noLes2')}}</h5>
                </div>
            </template>
        </div>

        <profile-dialog :isVisible="profileDlgVisible" :user="profileUserInfo" @load="openUserProfileDialog"
                        :isLoading="profileLoading" @show-changed="profileDlgVisible=false"></profile-dialog>
    </div>
</template>
<style lang="scss">
.profileDialog {
    .el-dialog__body {
        padding-top: 0;
    }
}
</style>
<script>
import LessonCard from '../components/LessonCard.vue';
import ProfileDialog from '../components/ProfileDialog.vue';

export default {
    data() {
        return {
            LESSON_DATE: null,
            center: {lat: 10.0, lng: 10.0},
            markers: [],
            map: null,
            findLessonsLoading: false,

            profileDlgVisible: false,
            profileUserInfo: null,
            profileLoading: false
        }
    },
    computed: mapState(['categories', 'searchedLessons', 'pupilsRef', 'pageFilter', 'DATE_FORMAT']),
    components:{LessonCard, ProfileDialog},
    methods: {
        selectAll() {
            if (this.pageFilter.selectAll) {
                this.pageFilter.selectAll = false;
                this.pageFilter.CATS = [];
            } else {
                this.pageFilter.selectAll = true;
                _.forEach(this.categories, (value, key) => {
                    this.pageFilter.CATS.push(value.id);
                });
            }
        },
        async openUserProfileDialog(userId) {
            this.profileLoading = true;
            this.profileDlgVisible = true;
            this.profileUserInfo = null;
            this.profileUserInfo = await this.get('/user/' + userId);
            this.profileLoading = false;
        },
        async findCities(queryString, cb) {
            let res = await this.post('/findCities', {QUERY: queryString});
            cb(res.cities);
        },
        async findLessons(isAdd) {
            this.findLessonsLoading = true;
            let pageFilter = this.pageFilter;
            pageFilter.offset = isAdd ? this.searchedLessons.length : 0;
            if (this.LESSON_DATE) {
                pageFilter.plannedDate = moment(this.LESSON_DATE).format("YYYY-MM-DD");
            } else {
                pageFilter.plannedDate = null;
            }
            if (!isAdd) {
                _.forEach(this.markers, (value, key) => {
                    value.setMap(null);
                });
                this.markers = [];
            }
            let response = await this.get('/findlessons', {params: pageFilter});
            console.log(response);
            let searchedLessons = response.lessons;
            if (searchedLessons.length <= 0) {
                this.$alert(this.$t('p.filter.noLes3'), this.$t('p.filter.notFound'), {
                    confirmButtonText: this.$t('p.common.closeBtn')
                });
            }
            let func = isAdd ? 'addSearchedLessons' : 'setSearchedLessons';
            this.$store.commit(func, searchedLessons);
            this.searchedLessons.forEach(lesson => {
                if (lesson.lat && lesson.lng) {
                    this.markers.push(new google.maps.Marker({
                        position: {lat: lesson.lat, lng: lesson.lng},
                        map: this.map,
                        animation: google.maps.Animation.DROP,
                        title: "(" + lesson.lat + ", " + lesson.lng + ") " + lesson.name
                    }));
                }
            });
            this.findLessonsLoading = false;
        },
        clearFilter() {
            this.pageFilter.selectAll = true;
            this.$store.commit('clearFilter', null);
            _.forEach(this.markers, (value, key) => {
                value.setMap(null);
            });
            this.markers = [];
        },
        initMap() {
            let myOptions = {
                zoom: 4,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: new google.maps.LatLng(60, 60)
            };
            this.map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        },
    },
    async created() {
        await this.$root.waitAppDataLoading();
        Vue.nextTick(() => {
            this.initMap();
        });
    }
}
</script>
