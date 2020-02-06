<template>
    <div v-if="lesson && user" v-loading.fullscreen.lock="savingRequestLoad" class="p10" style="padding: 10px 100px;">
        <h1 class="alignC">{{$t('p.newLes.lessonEditor')}}</h1>
        <el-steps :active="curStepNum" class="mb20" finish-status="success">
            <el-step v-for="step in steps" :key="step.id" :title="step.name"></el-step>
        </el-steps>
        <h4 class="mb10">{{ steps[curStepNum].subname }}</h4>
        <div class="mb20">
            <el-button v-if="steps[curStepNum].beginBtnEnabled" v-on:click="changeStep(0, steps[curStepNum].needSave)" type="warning" plain>{{$t('p.common.backToBegin')}}</el-button>
            <el-button v-if="steps[curStepNum].backBtnEnabled" v-on:click="changeStep(curStepNum-1, steps[curStepNum].needSave)" type="default">{{$t('p.common.backBtn')}}</el-button>
            <el-button v-if="steps[curStepNum].nextBtnEnabled" v-on:click="changeStep(curStepNum+1, steps[curStepNum].needSave)" type="primary" plain>{{$t('p.common.nextBtn')}}</el-button>
        </div>

        <div v-if="curStepNum === 0">
            <div class="mb20">
                <el-button v-on:click="changeStep(curStepNum+1, false)" type="success" plain>{{$t('p.newLes.beginFill')}}</el-button>
                <el-button v-on:click="publishLesson()" :disabled="lesson.status.id !== STATUS.CREATED" type="danger" plain>
                    {{ lesson.status.id === STATUS.ACTIVE ? $t('p.newLes.published') : $t('p.common.publishBtn') }}
                </el-button>
                <el-button v-on:click="$router.push('/profile')" plain>{{$t('p.newLes.toMainProfile')}}</el-button>
            </div>
            <el-collapse :value="['steps_list']" class="w600">
                <el-collapse-item name="steps_list">
                    <template slot="title">
                        <h3>{{$t('p.newLes.stepList')}}</h3>
                    </template>
                    <div v-for="step in steps" :key="step.id" class="mb10 step_list">
                        <h4 class="inline" @click="changeStep(step.id, false)" :class="{internal_link: curStepNum!==step.id}">{{step.name}}</h4>
                        <h5>{{step.subname}}</h5>
                    </div>
                </el-collapse-item>
            </el-collapse>
        </div>

        <table class="w100p" v-if="curStepNum === 1"><tr>
            <td style="max-width: 400px;">
                <el-form label-position="top" label-width="150px" style="width: 400px; display: inline-block;">
                    <el-form-item :label="$t('p.newLes.country')">
                        <el-select v-model="lesson.country_code" :placeholder="$t('p.newLes.country')" @change="countryChanged" class="w300">
                            <!--<el-option :label="$t('p.newLes.notSet')" :value="null"></el-option>-->
                            <el-option v-for="country in countries" :key="country.code" :label="country.name" :value="country.code"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('p.newLes.state')">
                        <el-select v-model="lesson.state_code" :placeholder="$t('p.newLes.state')" @change="stateChanged" class="w300">
                            <el-option :label="$t('p.newLes.notSet')" :value="0"></el-option>
                            <el-option v-for="state in states[lesson.country_code]" :key="state.code" :label="state.name" :value="state.code"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('p.newLes.city')" class="mb0">
                        <el-select v-if="!hasNoCityInList" v-model="lesson.city_code" :placeholder="$t('p.newLes.city')" class="w300" @change="$forceUpdate()"
                                   :disabled="!lesson.country_code || !states[lesson.country_code] || !cities[lesson.state_code]">
                            <el-option :label="$t('p.newLes.notSet')" :value="0"></el-option>
                            <el-option v-for="city in cities[lesson.state_code]" :key="city.code" :label="city.name" :value="city.code"></el-option>
                        </el-select>
                        <el-input v-if="hasNoCityInList" :placeholder="$t('p.newLes.typeCity')" v-model="lesson.city_name" :maxlength="50" class="w300"></el-input>
                    </el-form-item>
                    <el-checkbox v-model="hasNoCityInList" :disabled="!cities[lesson.state_code]" class="mb20">{{$t('p.newLes.noCityInList')}}</el-checkbox>
                    <el-form-item :label="$t('p.newLes.addr')" class="mb0">
                        <el-input type="textarea" :rows="2" :placeholder="$t('p.newLes.addrPlaceholder')" v-model="lesson.address" :maxlength="200" class="w300"></el-input>
                    </el-form-item>
                </el-form>
            </td>
            <td class="w100p">
                <div id="map_canvas" class="mAuto inline-block" style="width: 100%; height: 400px;"></div>
            </td>
        </tr></table>

        <el-form v-if="curStepNum === 2" label-position="top" label-width="150px" style="width: 600px; display: inline-block;">
            <el-form-item :label="$t('p.newLes.lessonName')">
                <el-input :placeholder="$t('p.newLes.lessonName')" v-model="lesson.name" :maxlength="200" class="w500"></el-input>
            </el-form-item>
            <el-form-item :label="$t('p.newLes.shortDesc')">
                <el-input type="textarea" :rows="2" :placeholder="$t('p.newLes.shortDesc')" v-model="lesson.short_desc" :maxlength="200" class="w500"></el-input>
            </el-form-item>
            <el-form-item :label="$t('p.newLes.desc')">
                <el-input type="textarea" :rows="2" :placeholder="$t('p.newLes.desc')" v-model="lesson.description" :maxlength="2000" class="w500"></el-input>
            </el-form-item>
        </el-form>

        <div v-if="curStepNum === 3">
            <div v-if="lesson && lesson.images.length < 1">{{$t('p.newLes.imgInfo')}}</div>
            <el-upload class="w450" action="/image/add" :headers="uploadHeaders" :data="{'lessonId': lesson.id}"
                       :on-preview="imagePreviewHandler" :on-remove="imageRemoveHandler" :file-list="lesson.images"
                       :on-success="imageUploadHandler" :on-error="imageUploadErrorHandler"
                       list-type="picture">
                <el-button size="small" type="success">{{$t('p.newLes.imgDonwload')}}</el-button>
                <div slot="tip" class="el-upload__tip">{{$t('p.newLes.imgTip')}}</div>
            </el-upload>
        </div>

        <el-form v-if="curStepNum === 4" label-position="top" label-width="150px" style="width: 600px; display: inline-block;">
            <el-form-item :label="$t('p.newLes.category')">
                <el-select v-model="lesson.category_ids" multiple :placeholder="$t('p.newLes.chooseCategory')" class="w100p">
                    <el-option v-for="item in categories" :key="item.id" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$t('p.newLes.aging')" class="mb0">
                <el-select v-model="lesson.aging" value-key="id" :placeholder="$t('p.newLes.chooseAging')" clearable class="w100p">
                    <el-option v-for="aging in agings" :key="aging.id" :label="aging.name" :value="aging"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item class="mb0">
                <el-checkbox v-model="lesson.equipment_have_all">{{$t('p.newLes.eqHaveAll')}}</el-checkbox>
            </el-form-item>
            <el-form-item :label="$t('p.newLes.eqNotHaveAll')" v-if="!lesson.equipment_have_all" class="mb0">
                <el-input type="textarea" :rows="2" :placeholder="$t('p.newLes.eqNotHaveAllDesc')" v-model="lesson.equipment_have_all_desc" :maxlength="1024" class="w100p"></el-input>
            </el-form-item>
            <el-form-item class="mb0">
                <el-checkbox v-model="lesson.equipment_first_aid" class="w100p">{{$t('p.newLes.firstAid')}}</el-checkbox>
            </el-form-item>
            <el-form-item class="mb0">
                <el-checkbox v-model="lesson.equipment_memo_security" class="w100p">{{$t('p.newLes.memoSecurity')}}</el-checkbox>
            </el-form-item>
            <el-form-item class="mb0">
                <el-checkbox v-model="lesson.equipment_extinguisher" class="w100p">{{$t('p.newLes.extinguisher')}}</el-checkbox>
            </el-form-item>
            <el-form-item :label="$t('p.newLes.pupilCount')">
                <el-select v-model="lesson.pupil_count" :placeholder="$t('p.newLes.chooseCount')" clearable class="w100p">
                    <el-option v-for="option in pupilsRef" :key="option.id" :label="option.val" :value="option.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$t('p.newLes.price')" class="mb0">
                <el-input-number v-model="lesson.price" :step="100" label="₽"></el-input-number>
            </el-form-item>
        </el-form>

        <div v-if="curStepNum === 5" class="alignC">
            <el-form label-position="top" label-width="150px" style="display: inline-block;" class="w600 alignL">
                <h4 class="mb10">{{$t('p.newLes.extinguisher')}}</h4>
                <el-form-item :label="$t('p.newLes.bDate')" class="inline-block m5">
                    <el-date-picker v-model="curDate" type="date" :placeholder="$t('p.newLes.choose')" format="dd.MM.yyyy" value-format="dd.MM.yyyy"></el-date-picker>
                </el-form-item>
                <el-form-item :label="$t('p.newLes.bTime')" class="inline-block m5">
                    <el-time-picker v-model="curTime" :placeholder="$t('p.newLes.choose')" format="HH:mm" value-format="HH:mm">
                    </el-time-picker>
                </el-form-item>
                <div></div>
                <el-form-item :label="$t('p.newLes.duration')" class="inline-block m5">
                    <el-time-select v-model="curDuration" :placeholder="$t('p.newLes.choose')"
                                    :picker-options="{start:'00:15',step:'00:15',end:'06:00'}" >
                    </el-time-select>
                </el-form-item>
                <el-form-item class="inline-block m5">
                    <el-button @click="addReservedTime" type="warning" plain>{{$t('p.newLes.addThisTime')}}</el-button>
                </el-form-item>
            </el-form>
            <h4>{{$t('p.newLes.settedTime')}}</h4>
            <el-table :data="lesson.reserved_time" class="w600 mAuto" stripe :empty-text="$t('p.newLes.notSetYet')">
                <el-table-column :label="$t('p.newLes.beginLesson')" width="200" align="left">
                    <template slot-scope="t">
                        <span>{{t.row.lesson_date}} в {{t.row.lesson_time}}</span>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.newLes.durLesson')" width="150" align="left">
                    <template slot-scope="t">
                        <span>{{t.row.duration | formatDuration}}</span>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('p.common.deleteBtn')" width="250" header-align="center">
                    <template slot-scope="t">
                        <el-button type="danger" icon="el-icon-delete" size="small" plain @click="deleteReservedTime(t.row)"></el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <div v-if="curStepNum === 6" class="alignC">
            <el-card class="w600 mAuto alignL mb20">
                <h5 class="mt0 mb10">{{$t('p.newLes.rules')}}</h5>
                <el-checkbox v-model="lesson.rules.animals" class="w100p mb5">{{$t('p.newLes.animals')}}</el-checkbox>
                <el-checkbox v-model="lesson.rules.allow_smoking" class="w100p mb5">{{$t('p.newLes.smoking')}}</el-checkbox>
                <el-checkbox v-model="lesson.rules.profile_photo" class="w100p mb5">{{$t('p.newLes.profilePhoto')}}</el-checkbox>
                <el-checkbox v-model="lesson.rules.confirm_email" class="w100p mb5" checked disabled>{{$t('p.newLes.confirmEmail')}}</el-checkbox>
                <el-checkbox v-model="lesson.rules.confirm_phone" class="w100p mb5" checked disabled>{{$t('p.newLes.confirmPhone')}}</el-checkbox>
                <el-input type="textarea" :rows="2" :placeholder="$t('p.newLes.addedRules')" v-model="lesson.rules.added_info" :maxlength="2000" class="w100p">
                </el-input>
            </el-card>
            <el-card class="w600 mAuto alignL">
                <h5 class="mb5">{{$t('p.newLes.rulesInfo')}}</h5>
                <el-checkbox class="w100p mb5" checked disabled>{{$t('p.newLes.rulesInfo2')}}</el-checkbox>
                <!--<el-checkbox class="w100p mb5" checked disabled>Написать вам о цели занятия</el-checkbox>-->
                <div class="alignC">
                    <el-button type="danger" v-on:click="publishLesson()" :disabled="lesson.status.id !== STATUS.CREATED">
                        {{ lesson.status.id === STATUS.ACTIVE ? $t('p.newLes.published') : $t('p.common.publishBtn') }}
                    </el-button>
                </div>
                <span class="xs-text mAuto">{{$t('p.newLes.publishOnlyNew')}}</span>
            </el-card>
        </div>
    </div>
</template>
<style scoped>
.el-tag + .el-tag {
    margin-left: 10px;
}
.el-checkbox + .el-checkbox {
     margin-left: 0;
}
.el-input-number {
    text-align: right !important;
}
.step_list {
    font-size: inherit !important;
    line-height: normal !important;
}
</style>
<script>
export default{
    data() {
        return {
            steps: [
                {
                    id: 0,
                    name: this.$t('p.newLes.step0'), subname: this.$t('p.newLes.step00'),
                    nextBtnEnabled: false, beginBtnEnabled: false, backBtnEnabled: false,
                    needSave: false
                },
                {
                    id: 1,
                    name: this.$t('p.newLes.step1'), subname: this.$t('p.newLes.step10'),
                    nextBtnEnabled: true, beginBtnEnabled: true, backBtnEnabled: true,
                    needSave: true
                },
                {
                    id: 2,
                    name: this.$t('p.newLes.step2'), subname: this.$t('p.newLes.step20'),
                    nextBtnEnabled: true, beginBtnEnabled: true, backBtnEnabled: true,
                    needSave: true
                },
                {
                    id: 3,
                    name: this.$t('p.newLes.step3'), subname: this.$t('p.newLes.step30'),
                    nextBtnEnabled: true, beginBtnEnabled: true, backBtnEnabled: true,
                    needSave: false
                },
                {
                    id: 4,
                    name: this.$t('p.newLes.step4'), subname: this.$t('p.newLes.step40'),
                    nextBtnEnabled: true, beginBtnEnabled: true, backBtnEnabled: true,
                    needSave: true
                },
                {
                    id: 5,
                    name: this.$t('p.newLes.step5'), subname: this.$t('p.newLes.step50'),
                    nextBtnEnabled: true, beginBtnEnabled: true, backBtnEnabled: true,
                    needSave: true
                },
                {
                    id: 6,
                    name: this.$t('p.newLes.step6'), subname: this.$t('p.newLes.step60'),
                    nextBtnEnabled: false, beginBtnEnabled: true, backBtnEnabled: true,
                    needSave: true
                },
            ],
            curStepNum: 0,
            // занятие
            lesson: null,

            curDate: '',
            curTime: '',
            curDuration: '',

            savingRequestLoad: false,

            map: null,
            marker: null,
            hasNoCityInList: false,
        }
    },
    computed: {
        ...mapState([
            'user', 'categories', 'agings', 'countries', 'states', 'cities', 'STATUS',
            'TRUE', 'FALSE', 'pupilsRef', 'defaultLatLng', 'DATE_FORMAT', 'TIME_FORMAT'
        ]),
        uploadHeaders() {
            return {'X-CSRF-TOKEN': window.token};
        }
    },
    methods: {
        async changeStep(nextStep, needSave) {
            if (needSave) {
                this.savingRequestLoad = true;
                let saved = await this.saveLesson();
                if (!saved) {
                    return;
                }
            }
            this.curStepNum = nextStep;
            if (nextStep === 1) {
                Vue.nextTick(this.initMap);
            }
        },
        async stateChanged(data) {
            if (data && !this.cities[data]) {
                let response = await this.get('/getCities/' + data);
                if (response.length > 0) {
                    this.$store.commit('addCities', {stateCode: data, cities: response});
                }
            }
            if (!_.find(this.cities[this.lesson.state_code], {code: this.lesson.city_code})) {
                this.lesson.city_code = 0;
            }
            this.hasNoCityInList = !!this.lesson.city_name || !this.cities[data] || !this.cities[data].length === 0;
            this.$forceUpdate();
        },
        async countryChanged(data) {
            if (data && !this.states[data]) {
                let response = await this.get('/getStates/' + data);
                if (response.length > 0) {
                    this.$store.commit('addStates', { code: data, states: response});
                }
            }
            if (!_.find(this.states[this.lesson.country_code], {code: this.lesson.state_code})) {
                this.lesson.state_code = 0;
            }
            this.hasNoCityInList = true;
            this.$forceUpdate();
        },
        addReservedTime() {
            if (this.checkExistReservedTime()) {
                this.lesson.reserved_time.push({
                    lesson_date: this.curDate,
                    lesson_time: this.curTime,
                    duration: this.curDuration.replace(":", ""),
                });
            }
        },
        deleteReservedTime(resTime) {
            const resTimeIndex = this.lesson.reserved_time.indexOf(resTime);
            this.lesson.reserved_time.splice(resTimeIndex, 1);
        },
        /**
         * Проверить, что выбранного времени нет в списке добавленных
         * @returns {Promise<boolean>} true если такое время существует, иначе false
         */
        checkExistReservedTime() {
            // TODO сделать проверку на корелляцию длительности и времени занятия
            if (!this.curDate) {
                this.$message.error(this.$t('p.newLes.needBDate'));
                return false;
            }
            if (!this.curTime) {
                this.$message.error(this.$t('p.newLes.needBTime'));
                return false;
            }
            if (!this.curDuration) {
                this.$message.error(this.$t('p.newLes.needDur'));
                return false;
            }
            let self = this;
            let i = _.findIndex(this.lesson.reserved_time, o => {
                return o.lesson_date === self.curDate &&
                    o.lesson_time === self.curTime;
            });
            if (i !== -1) {
                this.$message.error(this.$t('p.newLes.timeAddedAlready'));
                return false;
            }
            return true;
        },
        async imageRemoveHandler(file, fileList) {
            try {
                this.lesson.images = await this.postWithErrors('/image/delete', file);
                this.$message.success(this.$t('p.newLes.imageDeleted'));
            } catch (e) {
                this.lesson.images = fileList.push(file);
                if (e.status === 500 || e.status === 422) {
                    this.$alert(e.body, this.$t('p.common.error'));
                } else {
                    this.$alert(this.$t('p.common.errorText'), this.$t('p.common.error'));
                }
            }

        },
        imagePreviewHandler(file) {
            window.open(file.url,"_blank");
        },
        imageUploadErrorHandler(err, file, filelist) {
            return this.$message.error(err.message);
        },
        imageUploadHandler(response, file, filelist) {
            this.lesson.images = response;
            return this.$message.success(this.$t('p.newLes.imageDownloaded'));
        },
        changeTabTo(tabName) {

        },
        showPanel(id) {
            this.currentPanel = id;
            this.currentSubPanel = null;
        },
        /**
         * Сохранить занятие
         * @returns {Promise<boolean>} true если занятие сохранено, иначе false
         */
        async saveLesson() {
            try {
                this.savingRequestLoad = true;
                this.prepareToSave();
                let response = await this.postWithErrors('/lesson', {lesson: this.lesson}, null);
                if (response.status === 0) {
                    this.lesson.id = response.lessonId;
                    return true;
                } else if (response.status === 4) {
                    this.$alert(response.message, this.$t('p.common.error'));
                }
                return false;
            } catch (e) {
                if (e.status === 500 || e.status === 422) {
                    this.$alert(e.body, this.$t('p.common.error'));
                } else {
                    this.$alert(this.$t('p.common.errorText'), this.$t('p.common.error'));
                }
            } finally {
                this.savingRequestLoad = false;
            }
        },
        prepareToSave() {
            this.lesson.user_id = this.user.id;
            this.lesson.USE_CITY_NAME = this.hasNoCityInList;
            this.lesson.reservedTimes = [];
            if (this.lesson.aging === "" || this.lesson.aging === 0) {
                this.lesson.aging = null;
            }
            if (this.marker) {
                let position = this.marker.getPosition();
                this.lesson.lat = position.lat();
                this.lesson.lng = position.lng();
            }
        },

        async publishLesson() {
            try {
                // сначала сохраняем занятие
                let saved = await this.saveLesson();
                if (!saved) {
                    this.$alert(this.$t('p.newLes.saveError'), this.$t('p.common.error'));
                    return;
                }
                // отправляем запрос на публикацию занятия
                let response = await this.postWithErrors('/publish/' + this.lesson.id);
                if (response.errors) {
                    let message = "";
                    response.errors.forEach((item, i, arr) => {
                        message += '<div><i class="el-icon-warning"></i><span> ' + item + '</span></div>';
                    });
                    return this.$alert(message, this.$t('p.newLes.publishFail'), {dangerouslyUseHTMLString: true, type: "error"});
                }
                this.$alert(this.$t('p.newLes.publishSuccess'), this.$t('p.newLes.publishSuccessText'), {type: "success"});
                if (response.id) {
                    return this.$router.push("/lesson/" + response.id);
                }
            } catch(error) {
                this.$alert(error, this.$t('p.common.error'), {type: "error"});
            }
        },
        tabChanged(tabId) {
            if (tabId === 1) {
                this.initMap();
            }
        },
        initMap() {
            if (this.lesson) {
                let lat = this.lesson.lat;
                let lng = this.lesson.lng;
                if (!lat && !lng) {
                    lat = this.defaultLatLng.lat;
                    lng = this.defaultLatLng.lng;
                }
                let myOptions = {
                    zoom: 7,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: new google.maps.LatLng(lat, lng)
                };
                this.map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
                this.setMarker({latLng: {lat: lat, lng: lng}});
                this.map.addListener('click', this.setMarker);
            }
        },
        setMarker(event) {
            this.clearMarkers();
            let position = event.latLng;
            this.marker = new google.maps.Marker({
                position: position,
                map: this.map,
                draggable:true,
                animation: google.maps.Animation.DROP
            });
        },
        clearMarkers() {
            if (this.marker) {
                this.marker.setMap(null);
            }
        },
    },
    async created() {
        await this.$root.waitAppDataLoading();
        document.title = "Tutji - " + this.$t('p.newLes.lessonEditor');
        try {
            let response = await this.getWithErrors('/edit/'+this.$route.params.id);
            if (undefined === response) { return; }
            this.lesson = response.lesson;
            this.hasNoCityInList = response.hasNoCityInList;
            this.reviews = response.reviews;
            if (response.states) {
                this.$store.commit('addStates', { code: response.lesson.country_code, states: response.states});
            }
            if(response.cities) {
                this.$store.commit('addCities', { stateCode: response.lesson.state_code, cities: response.cities});
            }
            if (!this.lesson.images) {
                this.lesson.images = [];
            }
            if (!this.lesson.rules) {
                this.lesson.rules = {};
            }
        } catch (e) {
            if (e.status === 401) {
                return this.$router.push('/register');
            } else if (e.status === 500) {
                this.$alert(e.body, this.$t('p.common.error'), {type: "error"});
                this.$router.push("/home");
            } else {
                this.$alert(this.$t('p.common.errorText'), this.$t('p.common.error'), {type: "error"});
                this.$router.push("/home");
            }
        }
    }
}
</script>
