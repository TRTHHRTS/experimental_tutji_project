import Vuex from 'vuex'
import getters from './getters';
import actions from './actions';
import mutations from './mutations';
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        // Утилитные, форматирование
        DATE_FORMAT: "DD.MM.YYYY",
        LESSON_DATE_FORMAT: "dd.MM.yyyy",
        TIME_FORMAT: "HH:mm",
        TRUE: true,
        FALSE: false,

        // Enum'ы
        // Статусы урока
        STATUS: Object.freeze({CREATED: 0, ACTIVE: 1, DELETED: 2, FINISHED: 3}),
        STATUSES: null,
        STATUSES_COLOR: Object.freeze({0: "new-lesson-border", 1: "active-lesson-border", 2: "deleted-lesson-border", 3: "finished-lesson-border"}),
        pupilsRef: [
            {id:1,val:'1 ученик'},
            {id:2,val:'2 ученика'},
            {id:3,val:'3 ученика'},
            {id:4,val:'4 ученика'},
            {id:5,val:'5 учеников'},
            {id:6,val:'> 5 учеников'}],
        categories: null,
        agings: null,
        auditories: null,
        news: null,
        reserveStatuses: [],
        // Состояние
        user: null,
        searchedLessons: [],
        pageFilter: {lessonName: null, cityName: null, pupils: null, CATS: [], selectAll: false, resultsOnPage: 10, plannedDate: null },
        // Геоданные
        countries: null,
        states: {},
        cities: {},
        defaultLatLng: {lat: 50.00, lng: 50.00},
        // Система
        initDataLoading: false,
        system: {},
    },
    getters,
    mutations,
    actions: actions
})