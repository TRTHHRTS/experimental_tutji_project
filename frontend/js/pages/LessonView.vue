<template>
    <div v-if="lesson" class="p20 mAuto" style="max-width: 1200px;">
        <template v-if="lesson.images">
            <el-carousel v-if="lesson.images.length > 0" trigger="click" height="500px" arrow="always" class="alignC">
                <el-carousel-item v-for="item in lesson.images" :key="item.url">
                    <img :src="item.url"/>
                </el-carousel-item>
            </el-carousel>
        </template>
        <h2 class="alignC">
            <span style="word-break: break-word">{{lesson.name + (lesson.aging ? ' ('+lesson.aging.name+')' : $t('p.viewLes.agingNotSet'))}}</span>
            <el-tag v-if="lesson.your_lesson" class="m5" type="warning">{{$t('p.viewLes.yourLesson')}}</el-tag>
        </h2>
        <div v-if="lesson.categories.length > 0" class="alignC">
            <el-tag v-for="category in lesson.categories" :key="category.id" class="m5" type="info" size="medium">{{ category.name }}</el-tag>
        </div>
        <!-- Редактирование занятия -->
        <div class="mb10 mt5" v-if="lesson.your_lesson">
            <el-button v-on:click="editThisLesson()" icon="el-icon-edit" type="primary" size="small"
                       :disabled="lesson.status.id !== STATUS.CREATED" circle>
            </el-button>
            <span v-if="!isNewLesson" class="xs-text">{{$t('p.viewLes.editNewOnly')}}</span>
            <span v-else class="xs-text">{{$t('p.viewLes.editTip', {status: lesson.status.name})}}</span>
        </div>
        <!-- Если это чужое занятие - показываем блок с записью на занятие -->
        <div v-if="user && !lesson.your_lesson" class="alignC mb20">
            <el-card class="inline-block">
                <div v-if="reservedThisLesson">
                    <i class="el-icon-time"></i>
                    <span>{{$t('p.viewLes.reserveOn')}}<strong>{{reservedThisLesson.ldate | formatDate}}</strong> в
                        <strong>{{reservedThisLesson.ltime}}</strong> ({{$t('p.viewLes.resCount', {count: reservedThisLesson.count})}})
                    </span>
                    <div><span>{{$t('p.viewLes.resDur')}}<strong>{{reservedThisLesson.duration | formatDuration}}</strong></span></div>
                    <div v-if="reservedThisLesson.phone" class="alignL"><span>Номер телефона преподавателя: <strong>+7 {{reservedThisLesson.phone | formatPhone}}</strong></span></div>
                    <div v-if="reservedThisLesson.address" class="alignL"><span>Уточнения по адресу: <strong>{{reservedThisLesson.address}}</strong></span></div>
                    <div class="mt10"><el-button v-on:click="cancel(reservedThisLesson.rlid)" type="danger" size="mini" plain round>{{$t('p.viewLes.resCancel')}}</el-button></div>
                </div>
                <div v-if="!reservedThisLesson">
                    <el-form :inline="true" :model="reserve" class="reserve-form-inline alignC">
                        <el-form-item>
                            <el-select v-model="reserve.count" :placeholder="$t('p.viewLes.resPupilCount')">
                                <el-option v-for="n in lesson.pupil_count" :key="n" :value="n" :label="$t('p.viewLes.resCount', {count: n})"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-select v-model="reserve.res_time_id" :placeholder="$t('p.viewLes.resTime')" class="w300">
                                <el-option v-for="t in lesson.reserved_time" :key="t.id" :value="t.id"
                                           :label="t.lesson_time+' '+t.lesson_date+' ('+$options.filters.formatDuration(t.duration)+')'">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" v-on:click="reserveLesson()">{{$t('p.viewLes.resBtn')}}</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </el-card>
        </div>
        <!-- Если это свое занятие - показываем кто куда записался -->
        <div v-if="lesson.your_lesson && !isNewLesson" class="alignC mb20">
            <el-card v-for="t in lesson.reserved_time" :key="t.id"
                     :class="t.isGone ? 'finished-lesson-border' : ''"
                     class="reserveCard">
                <h2><strong>{{ t.lesson_date + " " + t.lesson_time }}</strong></h2>
                <h4 v-if="t.users_to_reserve.length > 0 && getSumInArray(t.users_to_reserve, 'count') < lesson.pupil_count">
                    <strong>{{$t('p.viewLes.reserved', {count: getSumInArray(t.users_to_reserve, 'count'), n: lesson.pupil_count})}}</strong>
                </h4>
                <div v-if="getSumInArray(t.users_to_reserve, 'count') === lesson.pupil_count">{{$t('p.viewLes.complect')}}</div>
                <div v-if="t.users_to_reserve.length === 0">{{$t('p.viewLes.noRes')}}</div>
                <span v-if="t.users_to_reserve.length > 0" >{{$t('p.viewLes.reserved2')}}</span>
                <span v-for="usersToReserve in t.users_to_reserve" class="internal_link pr5"
                      v-on:click="openUserProfileDialog(usersToReserve.user_id)"
                      :key="usersToReserve.user_id">
                    {{ usersToReserve.name + " (" + usersToReserve.count + " чел.)"}}
                </span>
            </el-card>
        </div>
        <div v-if="lesson.your_lesson && isNewLesson" class="alignC mb20">
            <i18n path="p.viewLes.tryToPublish" tag="span" for="p.viewLes.tryToPublish2" class="warningText">
                <span @click="publish" class="internal_link">{{$t('p.viewLes.tryToPublish2')}}</span>
            </i18n>
        </div>

        <el-card class="inline floatR" v-if="lesson.price">
            <h2 class="alignC" v-html="$t('p.viewLes.priceForLesson', {price: lesson.price})"></h2>
        </el-card>

        <div v-if="!lesson.your_lesson">
            <strong>{{$t('p.viewLes.teacher')}}<span v-on:click="openUserProfileDialog(lesson.user_id)" class="internal_link">{{lesson.username}}</span></strong>
            <el-button v-on:click="contactToTeacher()" icon="el-icon-message" type="primary" size="mini" plain></el-button>
        </div>
        <div><span style="word-break: break-word">{{lesson.description ? lesson.description : $t('p.viewLes.descNotSet')}}</span></div>
        <!--<el-button type="text">Правила дома</el-button>-->
        <!--<div>Вмещает учеников: <strong>{{ lesson.pupil_count ? lesson.pupil_count + " чел." : "(не задано)"}}</strong></div>-->
        <div v-if="lesson.reserved_time" class="mt10">
            <strong>{{$t('p.viewLes.lesConds')}}</strong>
            <el-tag v-if="lesson.equipment_have_all" type="info" size="medium" color="white" class="m5">
                <i class="el-icon-check"></i>
                <span>{{$t('p.viewLes.condHaveAll')}}</span>
            </el-tag>
            <el-tag v-if="!lesson.equipment_have_all" type="info" size="medium" color="white" class="m5">
                <i class="el-icon-check"></i><span>{{$t('p.viewLes.condHaveNotAll', {desc: lesson.equipment_have_all_desc})}}</span>
            </el-tag>
            <el-tag v-if="lesson.equipment_first_aid" type="info" size="medium" color="white" class="m5">
                <i class="el-icon-check"></i><span>{{$t('p.viewLes.condFirstAid')}}</span>
            </el-tag>
            <el-tag v-if="lesson.equipment_memo_security" type="info" size="medium" color="white" class="m5">
                <i class="el-icon-check"></i><span>{{$t('p.viewLes.condMemo')}}</span>
            </el-tag>
            <el-tag v-if="lesson.equipment_extinguisher" type="info" size="medium" color="white" class="m5">
                <i class="el-icon-check"></i><span>{{$t('p.viewLes.condExtinguisher')}}</span>
            </el-tag>
        </div>
        <div v-if="!lesson.your_lesson && lesson.reserved_time" class="mt10 mb10">
            <strong>{{$t('p.viewLes.dates')}}</strong>
            <span v-for="t in lesson.reserved_time" class="m5" :key="t.id">
                <el-tag type="success" size="medium" color="white">
                    <i class="el-icon-time"></i><span> {{t.lesson_date}} {{t.lesson_time}} ({{t.duration | formatDuration}})</span>
                </el-tag>
            </span>
        </div>

        <el-collapse v-model="activeNames" accordion @change="lessonViewChangeTab">
            <el-collapse-item name="reviews">
                <template slot="title">
                    <h3 class="btn_link">{{$t('p.viewLes.reviews', {count: reviews.length})}}</h3>
                </template>
                <div v-if="reviews && reviews.length > 0">
                    <el-card v-for="review of reviews" :key="review.id" class="w300 m5 inline-block">
                        <div slot="header" class="clearfix alignL pointer">
                            <a v-on:click="openUserProfileDialog(review.user.id)" class="internal_link">{{ review.user.name }}</a>
                        </div>
                        <span class="floatR xs-text">{{review.created_at | formatDateTime}}</span>
                        <div style="word-break: break-word;">{{ review.message }}</div>
                        <div><el-rate v-model="review.rating" disabled></el-rate></div>
                    </el-card>
                </div>
                <div v-if="!reviews || reviews.length <= 0" class="alignC">
                    <div>{{$t('p.viewLes.noReviews')}}</div>
                    <el-button disabled>{{$t('p.viewLes.makeReview')}}</el-button>
                </div>
            </el-collapse-item>

             <el-collapse-item name="geo">
                 <template slot="title"><h3 class="btn_link">{{$t('p.viewLes.geo')}}</h3></template>
                 <div><span style="word-break: break-word">{{lesson.country_name + (lesson.state_name ? ", "+lesson.state_name : "") + (lesson.city_name ? ", "+lesson.city_name: "")}}</span></div>
                 <div id="map_canvas" style="width: 600px; height: 400px;"></div>
            </el-collapse-item>
        </el-collapse>

        <profile-dialog :isVisible="profileDlgVisible" :user="profileUserInfo" @load="openUserProfileDialog"
                        :isLoading="profileLoading" @show-changed="profileDlgVisible=false"></profile-dialog>
    </div>
</template>
<style lang="scss" scoped>
.el-card__header {
    padding: 5px !important;
}
.reserve-form-inline .el-form-item {
    margin-bottom: 0;
}
.reserveCard {
    min-width: 300px;
    display: inline-block;
    vertical-align: top;
    margin: 5px;
    .el-card__body {
        padding: 5px;
    }
}
img {
    max-width: 100%;
    max-height: 100%;
}
</style>
<script>
import ProfileDialog from '../components/ProfileDialog.vue';

export default {
    data(){
        return {
            // список развернутых блоков
            activeNames: [],

            pagetitle: null,
            lesson: null,
            rules: [],
            reviews: [],
            showImages: true,

            reserve: {
                res_time_id: null,
                count: 1,
            },
            profileDlgVisible: false,
            profileUserInfo: null,
            profileLoading: false
        }
    },
    computed: {
        isNewLesson() {
            return this.lesson.status.id === this.STATUS.CREATED;
        },
        reservedThisLesson() {
            if (this.user) {
                let recordIndex = _.findIndex(this.user.reservedLessons, {'id': this.lesson.id});
                if (recordIndex !== -1) {
                    return this.user.reservedLessons[recordIndex];
                }
            }
            return null;
        },
        ...mapState(['user', 'TRUE', 'STATUS'])
    },
    components: {
        ProfileDialog
    },
    methods: {
        ...mapActions(['commonCancelRecord']),
        async publish() {
            let response = await this.post('/publish/' + this.lesson.id);
            if (response.errors) {
                let message = "";
                response.errors.forEach((item, i, arr) => {
                    message += '<div><i class="el-icon-warning"></i><span> ' + item + '</span></div>';
                });
                this.$alert(message, this.$t('p.viewLes.publishErrorText'), {dangerouslyUseHTMLString: true, type: "error"});
                return;
            }
            this.$alert(this.$t('p.viewLes.publishSuccess'), this.$t('p.viewLes.publishSuccessText'), {type: "success"});
            this.$router.push("/lesson/" + this.lesson.id);
        },
        async contactToTeacher() {
            // Отправляем запрос на создание комнаты и переходим в диалог переписки
            let rcptId = await this.post('/channel', {'RCPT': this.lesson.user_id });
            this.$router.push('/messages/' + rcptId);
        },
        async reserveLesson() {
            if (!this.reserve.res_time_id) {
                return this.$message.error(this.$t('p.viewLes.resTimeNotSet'));
            }
            let t = this.findInArrayById(this.lesson.reserved_time, this.reserve.res_time_id);
            const totalPrice = this.reserve.count * this.lesson.price;
            let message = `
                <p>${this.$t('p.viewLes.resLes1', {name: this.lesson.name})}</p>
                <p>${this.$t('p.viewLes.resLes1_2', {price: this.lesson.price,total: totalPrice})}</p>
                <p>${this.$t('p.viewLes.resLes2', {date: t.lesson_date, time: t.lesson_time, dur: this.$options.filters.formatDuration(t.duration)})}</p>
                <p>${this.$t('p.viewLes.resLes3')}</p>
                <strong><ul class="mt0">
                    <li>${this.lesson.rules.animals ? this.$t('p.viewLes.animalsTrue') : this.$t('p.viewLes.animalsFalse')}</li>
                    <li>${this.lesson.rules.allow_smoking ? this.$t('p.viewLes.smokeTrue') : this.$t('p.viewLes.smokeFalse')}</li>
                    ${this.lesson.rules.confirm_email ? this.$t('p.viewLes.confirmEmail') : ""}
                    ${this.lesson.rules.confirm_phone ? this.$t('p.viewLes.confirmPhone') : ""}
                    ${this.lesson.rules.profile_photo ? this.$t('p.viewLes.profilePhoto') : ""}
                    ${this.lesson.rules.added_info ? "<li>" + this.lesson.rules.added_info + "</li>" : ""}
                </ul></strong>
            `;
            this.$confirm(message, this.$t('p.common.warning'), {
                dangerouslyUseHTMLString: true,
                confirmButtonText: this.$t('p.common.payBtn'),
                cancelButtonText: this.$t('p.common.cancelBtn'),
                type: 'info'
            }).then(async () => {
                let response = await this.post('/reserve', {
                    'LESSON_ID': this.lesson.id,
                    'RESERVED_TIME_ID': this.reserve.res_time_id,
                    'COUNT': this.reserve.count});
                if (response.status === 0) {
                    this.$message.success(this.$t('p.viewLes.youReserve'));
                    this.$store.commit('setUserReservedLessons', response.data);
                } else {
                    this.$alert(response.message, this.$t('p.viewLes.cantReserve'), {type: "error"});
                }
            }).catch(() => {

            });
        },
        async cancel(reserveId) {
            await this.commonCancelRecord([this, reserveId, false, false]);
            await this.loadUserReservedLessons();
        },
        async loadUserReservedLessons() {
            let response = await this.get("/reservedLessons");
            this.$store.commit('setUserReservedLessons', response);
        },
        editThisLesson() {
            if (this.lesson.status.id === this.STATUS.CREATED) {
                return this.$router.push('/edit/' + this.lesson.id);
            }
        },
        async openUserProfileDialog(userId) {
            this.profileLoading = true;
            this.profileDlgVisible = true;
            this.profileUserInfo = null;
            this.profileUserInfo = await this.get('/user/' + userId);
            this.profileLoading = false;
        },
        closeUserProfileDialog() {
            this.$refs['profileDialogRef'].close();
        },
        lessonViewChangeTab(name) {
            if ("geo" === name) {
                Vue.nextTick(this.initMap);
            }
        },
        initMap() {
            if (this.lesson) {
                let lat = this.lesson.lat;
                let lng = this.lesson.lng;
                if (lat && lng) {
                    let myOptions = {
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: new google.maps.LatLng(lat, lng)
                    };
                    let map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                    new google.maps.Marker({
                        position: {lat: lat, lng: lng},
                        map: map,
                        animation: google.maps.Animation.DROP
                    });
                }
            }
        },
        getSumInArray(array, fieldName) {
            let c = 0;
            _.forEach(array, value => {
                c += value[fieldName];
            });
            return c;
        }
    },
    async created() {
        await this.$root.waitAppDataLoading();
        let response = await this.get('/view/'+this.$route.params.lessonId);
        // TODO переделать на конкретные переменные
        _.forIn(response, (value, key) => {
            this[key] = value;
        });
        Vue.nextTick(this.initMap);
    }
}
</script>
